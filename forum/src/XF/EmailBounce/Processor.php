<?php

namespace XF\EmailBounce;

use XF\App;
use XF\Entity\User;
use Zend\Mail\Storage;

class Processor
{
	/**
	 * @var App
	 */
	protected $app;

	/**
	 * @var Parser
	 */
	protected $bounceParser;

	/**
	 * @var \XF\Repository\EmailBounce
	 */
	protected $bounceRepo;

	public function __construct(App $app, Parser $parser)
	{
		$this->app = $app;
		$this->bounceParser = $parser;
		$this->bounceRepo = $app->repository('XF:EmailBounce');
	}

	public function processFromStorage(Storage\AbstractStorage $storage, $maxRunTime = 0)
	{
		$s = microtime(true);

		$total = $storage->countMessages();
		if (!$total)
		{
			return true;
		}

		$finished = true;

		for ($messageId = $total; $messageId > 0; $messageId--)
		{
			$contentValid = false;

			try
			{
				$headers = $storage->getRawHeader($messageId);
				$content = $storage->getRawContent($messageId);
				$contentValid = true;
			}
			catch (\InvalidArgumentException $e)
			{
				$contentValid = false;
			}
			finally
			{
				$storage->removeMessage($messageId);
			}

			$rawMessage = trim($headers) . "\r\n\r\n" . trim($content);

			if ($contentValid)
			{
				$this->processMessage($rawMessage);
			}
			else
			{
				$result = new ParseResult();
				$result->date = \XF::$time;

				$this->logBounceMessage($rawMessage, null, $result);
			}

			if ($maxRunTime && microtime(true) - $s > $maxRunTime)
			{
				$finished = false;
				break;
			}
		}

		return $finished;
	}

	public function processMessage($rawMessage, &$result = null)
	{
		try
		{
			$result = $this->bounceParser->parseMessage($rawMessage);
		}
		catch (\Exception $e)
		{
			\XF::logException($e, false, "Bounce message processing failed: ");

			$result = new ParseResult();
			$result->date = \XF::$time;

			return $this->logBounceMessage($rawMessage, null, $result);
		}

		if ($result->recipient)
		{
			/** @var User $user */
			$user = $this->app->em()->findOne('XF:User', ['email' => $result->recipient]);
		}
		else
		{
			$user = null;
		}

		$action = null;

		if ($user && $result->messageType == ParseResult::TYPE_BOUNCE)
		{
			if ($result->recipientTrusted)
			{
				$bounceType = $this->bounceParser->getBounceTypeFromStatus($result->remoteStatus);
				if ($bounceType)
				{
					$action = $this->takeBounceAction($user, $bounceType, $result->date);
				}
			}
			else
			{
				$action = 'untrusted';
			}
		}

		return $this->logBounceMessage($rawMessage, $action, $result, $user);
	}

	public function takeBounceAction(User $user, $bounceType, $bounceDate)
	{
		if ($bounceType == 'hard')
		{
			$this->triggerUserBounceAction($user);
			return 'hard';
		}
		else if ($bounceType == 'soft')
		{
			$this->bounceRepo->insertSoftBounceLogEntry($user->user_id, $bounceDate);

			if ($this->hasSoftBouncedTooMuch($user->user_id))
			{
				$this->triggerUserBounceAction($user);
				return 'soft_hard';
			}

			return 'soft';
		}
		else
		{
			throw new \InvalidArgumentException("Unknown bounce type '$bounceType'");
		}
	}

	public function triggerUserBounceAction(User $user)
	{
		if ($this->canApplyUserBounceAction($user))
		{
			$this->applyUserBounceAction($user);

			return true;
		}

		return false;
	}

	public function applyUserBounceAction(User $user)
	{
		$user->user_state = 'email_bounce';
		$user->save();
	}

	protected function canApplyUserBounceAction(User $user)
	{
		return (
			$user->user_state == 'valid'
			&& !$user->is_moderator
			&& !$user->is_admin
			&& !$user->is_staff
		);
	}

	protected function hasSoftBouncedTooMuch($userId)
	{
		$bounces = $this->bounceRepo->countRecentSoftBounces($userId, 30);
		if (!$bounces['bounce_total'])
		{
			return false;
		}

		$thresholds = $this->app->options()->emailSoftBounceThreshold;
		return (
			$bounces['bounce_total'] >= $thresholds['bounce_total']
			&& $bounces['unique_days'] >= $thresholds['unique_days']
			&& $bounces['days_between'] >= $thresholds['days_between']
		);
	}

	protected function logBounceMessage($rawMessage, $action, ParseResult $result, User $user = null)
	{
		$bounce = $this->app->em()->create('XF:EmailBounceLog');
		$bounce->bulkSet([
			'email_date' => $result->date,
			'message_type' => $result->messageType ?: ParseResult::TYPE_UNKNOWN,
			'action_taken' => $action ?: '',
			'user_id' => $user ? $user->user_id : null,
			'recipient' => $result->recipient,
			'raw_message' => substr($rawMessage, 0, 1024 * 1024), // limit logging to 1MB to prevent potential insert issues
			'status_code' => $result->remoteStatus,
			'diagnostic_info' => $result->remoteDiagnostics
		]);
		$bounce->save();

		return $bounce;
	}

	/**
	 * @param App $app
	 *
	 * @return null|Storage\AbstractStorage
	 */
	public static function getDefaultBounceHandlerStorage(App $app)
	{
		$options = $app->options();

		if (!$options->bounceEmailAddress)
		{
			return null;
		}

		$handler = $options->emailBounceHandler;
		if (!$handler || empty($handler['enabled']))
		{
			return null;
		}

		$config = [
			'host' => $handler['host'],
			'user' => $handler['username'],
			'password' => $handler['password']
		];
		if ($handler['port'])
		{
			$config['port'] = intval($handler['port']);
		}
		if ($handler['encryption'])
		{
			$config['ssl'] = strtoupper($handler['encryption']);
		}

		try
		{
			if ($handler['type'] == 'pop3')
			{
				$connection = new Storage\Pop3($config);
			}
			else if ($handler['type'] == 'imap')
			{
				$connection = new Storage\Imap($config);
			}
			else
			{
				throw new \Exception("Unknown email handler $handler[type]");
			}
		}
		catch (\Zend\Mail\Exception\ExceptionInterface $e)
		{
			$app->logException($e, false, "Bounce connection error: ");
			return null;
		}

		return $connection;
	}
}
