<?php

namespace XF\EmailUnsubscribe;

use XF\App;
use XF\Entity\User;
use Zend\Mail\Storage;

class Processor
{
	/**
	 * @var App
	 */
	protected $app;

	protected $verpBase;

	public function __construct(App $app, $verpBase)
	{
		$this->app = $app;
		$this->verpBase = $verpBase;
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
			$message = $storage->getMessage($messageId);
			$storage->removeMessage($messageId);
			$this->processMessage($message);

			if ($maxRunTime && microtime(true) - $s > $maxRunTime)
			{
				$finished = false;
				break;
			}
		}

		return $finished;
	}

	public function processMessage(Storage\Message $message)
	{
		$confirmKey = null;
		$email = null;

		if ($this->verpBase && isset($message->to))
		{
			$matchRegex = str_replace('@', '\+([a-z0-9]+)\+([^@=]+=[^@=]+)@', preg_quote($this->verpBase, '#'));
			if (preg_match("#$matchRegex#i", $message->to, $matches))
			{
				$confirmKey = $matches[1];
				$email = str_replace('=', '@', $matches[2]);
			}
		}

		if (!$confirmKey && !$email)
		{
			$subject = trim($message->getHeader('subject', 'string'));

			if (preg_match('/^\[List-Unsubscribe\[([a-f0-9]{32}),(.*@.*)\]\]$/', $subject, $matches))
			{
				$confirmKey = $matches[1];
				$email = $matches[2];
			}
		}

		if (!$confirmKey || !$email)
		{
			return;
		}

		/** @var User $user */
		$user = $this->app->em()->findOne('XF:User', ['email' => $email]);

		if (!$user || substr($user->getEmailConfirmKey(), 0, 8) !== substr($confirmKey, 0, 8))
		{
			return;
		}

		$this->applyUserUnsubscribeAction($user);
	}

	public function applyUserUnsubscribeAction(User $user)
	{
		$user->Option->receive_admin_email = false;
		$user->Option->save(false);
	}

	/**
	 * @param App $app
	 *
	 * @return null|Storage\AbstractStorage
	 */
	public static function getDefaultUnsubscribeHandlerStorage(App $app)
	{
		$options = $app->options();

		if (!$options->unsubscribeEmailAddress)
		{
			return null;
		}

		$handler = $options->emailUnsubscribeHandler;
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
			$app->logException($e, false, "Unsubscribe connection error: ");
			return null;
		}

		return $connection;
	}
}