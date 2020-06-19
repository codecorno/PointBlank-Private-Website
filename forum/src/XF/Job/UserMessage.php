<?php

namespace XF\Job;

class UserMessage extends AbstractUserCriteriaJob
{
	protected $defaultData = [
		'message' => []
	];

	/**
	 * @var User|null
	 */
	protected $author;

	protected function actionSetup()
	{
		$this->author = $this->app->em()->find('XF:User', $this->data['message']['user_id']);
	}

	protected function executeAction(\XF\Entity\User $user)
	{
		$message = $this->data['message'];

		$language = $this->app->language($user->language_id);
		$title = $this->replacePhrases($message['message_title'], $language);
		$body = $this->replacePhrases($message['message_body'], $language);

		$tokens = $this->prepareTokens($user);
		$title = strtr($title, $tokens);
		$body = strtr($body, $tokens);

		/** @var \XF\Service\Conversation\Creator $creator */
		$creator = $this->app->service('XF:Conversation\Creator', $this->author);
		$creator->setIsAutomated();
		$creator->setOptions([
			'open_invite' => $message['open_invite'],
			'conversation_open' => !$message['conversation_locked']
		]);
		$creator->setRecipientsTrusted($user);
		$creator->setContent($title, $body);
		if (!$creator->validate())
		{
			return;
		}

		$conversation = $creator->save();

		if ($message['delete_type'])
		{
			/** @var \XF\Entity\ConversationRecipient $recipient */
			$recipient = $conversation->Recipients[$this->author->user_id];
			$recipient->recipient_state = $message['delete_type'];
			$recipient->save(false);
		}
	}

	protected function getActionDescription()
	{
		$actionPhrase = \XF::phrase('messaging');
		$typePhrase = \XF::phrase('users');

		return sprintf('%s... %s', $actionPhrase, $typePhrase);
	}

	protected function wrapTransaction()
	{
		return false;
	}

	protected function replacePhrases($string, \XF\Language $language)
	{
		return $this->app->stringFormatter()->replacePhrasePlaceholders($string, $language);
	}

	protected function prepareTokens(\XF\Entity\User $user)
	{
		return [
			'{name}' => $user->username,
			'{email}' => $user->email,
			'{id}' => $user->user_id
		];
	}

	public function canCancel()
	{
		return true;
	}

	public function canTriggerByChoice()
	{
		return false;
	}
}