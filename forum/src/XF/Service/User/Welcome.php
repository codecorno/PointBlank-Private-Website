<?php

namespace XF\Service\User;

use XF\Entity\User;
use XF\Language;

class Welcome extends \XF\Service\AbstractService
{
	/**
	 * @var User
	 */
	protected $user;

	protected $options = [
		'emailEnabled' => false,
		'emailBody' => '',
		'emailTitle' => '',
		'emailFormat' => 'plain',
		'emailFromName' => '',
		'emailFromEmail' => '',

		'messageEnabled' => false,
		'messageParticipants' => [],
		'messageTitle' => '',
		'messageBody' => '',
		'messageOpenInvite' => false,
		'messageLocked' => false,
		'messageDelete' => 'no_delete'
	];

	/**
	 * @var \XF\Mail\Mail
	 */
	protected $sentMail;

	/**
	 * @var \XF\Entity\ConversationMaster
	 */
	protected $sentMessage;

	public function __construct(\XF\App $app, User $user)
	{
		parent::__construct($app);
		$this->user = $user;
		$this->setOptions($app->options()->registrationWelcome);
	}

	public function setOptions(array $options)
	{
		$this->options = array_merge($this->options, $options);
	}

	/**
	 * @return null|\XF\Mail\Mail
	 */
	public function getSentMail()
	{
		return $this->sentMail;
	}

	/**
	 * @return null|\XF\Entity\ConversationMaster
	 */
	public function getSentMessage()
	{
		return $this->sentMessage;
	}

	public function send()
	{
		if ($this->user->user_state != 'valid')
		{
			throw new \LogicException("User must have a valid user_state to send");
		}

		if ($this->options['emailEnabled'])
		{
			$this->sendMail();
		}

		if ($this->options['messageEnabled'])
		{
			$this->sendMessage();
		}
	}

	protected function sendMail()
	{
		$options = $this->options;

		$language = $this->app->language($this->user->language_id);
		$options['emailBody'] = $this->replacePhrases($options['emailBody'], $language);
		$options['emailTitle'] = $this->replacePhrases($options['emailTitle'], $language);

		if ($options['emailFormat'] == 'html')
		{
			$tokens = $this->prepareTokens();
			$body = $this->replaceTokens($options['emailBody'], $tokens);
			$text = $this->app->mailer()->generateTextBody($body);
		}
		else
		{
			$tokens = $this->prepareTokens(false);
			$text = $this->replaceTokens($options['emailBody'], $tokens);
			$body = nl2br(htmlspecialchars($text));
		}
		$title = $this->replaceTokens($options['emailTitle'], $tokens);

		$mail = $this->getMail($this->user);

		if ($options['emailFromEmail'])
		{
			$mail->setFrom($options['emailFromEmail'], $options['emailFromName']);
		}

		$mail->setTemplate('prepared_email', [
			'title' => $title,
			'htmlBody' => $body,
			'textBody' => $text
		]);
		$mail->queue();

		$this->sentMail = $mail;
	}

	protected function getMail(\XF\Entity\User $user)
	{
		return $this->app->mailer()->newMail()->setToUser($user);
	}

	public function sendMessage()
	{
		$options = $this->options;

		$participants = $options['messageParticipants'];
		if (!is_array($participants))
		{
			\XF::logError('Cannot send welcome message as there are no valid participants to send the message from.');
			return;
		}

		$starter = array_shift($participants);

		$starterUser = null;
		if ($starter)
		{
			/** @var User $starterUser */
			$starterUser = $this->em()->find('XF:User', $starter);
		}
		if (!$starterUser)
		{
			\XF::logError('Cannot send welcome message as there are no valid participants to send the message from.');
			return;
		}

		$tokens = $this->prepareTokens(false);
		$language = $this->app->language($this->user->language_id);

		$title = $this->replacePhrases($this->replaceTokens($options['messageTitle'], $tokens), $language);
		$body = $this->replacePhrases($this->replaceTokens($options['messageBody'], $tokens), $language);

		$recipients = [];
		if ($participants)
		{
			$recipients = $this->em()->findByIds('XF:User', $participants)->toArray();
		}
		$recipients[$this->user->user_id] = $this->user;

		/** @var \XF\Service\Conversation\Creator $creator */
		$creator = $this->service('XF:Conversation\Creator', $starterUser);
		$creator->setIsAutomated();
		$creator->setOptions([
			'open_invite' => $options['messageOpenInvite'],
			'conversation_open' => !$options['messageLocked']
		]);
		$creator->setRecipientsTrusted($recipients);
		$creator->setContent($title, $body);
		if (!$creator->validate($errors))
		{
			return;
		}
		$creator->setAutoSendNotifications(false);
		$conversation = $creator->save();

		/** @var \XF\Repository\Conversation $conversationRepo */
		$conversationRepo = $this->app->repository('XF:Conversation');
		$convRecipients = $conversation->getRelationFinder('Recipients')->with('ConversationUser')->fetch();

		$recipientState = ($options['messageDelete'] == 'delete_ignore' ? 'deleted_ignored' : 'deleted');

		/** @var \XF\Entity\ConversationRecipient $recipient */
		foreach ($convRecipients AS $recipient)
		{
			if ($recipient->user_id == $this->user->user_id)
			{
				continue;
			}

			$conversationRepo->markUserConversationRead($recipient->ConversationUser);

			if ($options['messageDelete'] != 'no_delete')
			{
				$recipient->recipient_state = $recipientState;
				$recipient->save();
			}
		}

		/** @var \XF\Service\Conversation\Notifier $notifier */
		$notifier = $this->service('XF:Conversation\Notifier', $conversation);
		$notifier->addNotificationLimit($this->user)->notifyCreate();

		$this->sentMessage = $conversation;
	}

	protected function prepareTokens($escape = true)
	{
		$tokens = [
			'{name}' => $this->user->username,
			'{email}' => $this->user->email,
			'{id}' => $this->user->user_id
		];

		if ($escape)
		{
			array_walk($tokens, function(&$value)
			{
				if (is_string($value))
				{
					$value = htmlspecialchars($value);
				}
			});
		}

		return $tokens;
	}

	protected function replaceTokens($string, array $tokens)
	{
		return strtr($string, $tokens);
	}

	protected function replacePhrases($string, \XF\Language $language)
	{
		return $this->app->stringFormatter()->replacePhrasePlaceholders($string, $language);
	}
}