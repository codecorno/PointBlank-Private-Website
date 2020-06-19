<?php

namespace XF\Job;

class UserAlert extends AbstractUserCriteriaJob
{
	protected $defaultData = [
		'alert' => []
	];

	protected $alert;
	protected $author;
	protected $replacements;

	protected function actionSetup()
	{
		$replacements = [];
		$alert = $this->prepareAlert($this->data['alert'], $replacements);
		$author = $this->prepareAuthor($alert);

		$this->alert = $alert;
		$this->author = $author;
		$this->replacements = $replacements;
	}

	protected function executeAction(\XF\Entity\User $user)
	{
		$alert = $this->alert;
		$replacements = $this->replacements;

		$body = $alert['alert_body'];

		$language = $this->app->language($user->language_id);
		$body = $this->replacePhrases($body, $language);

		$replacements = array_merge($replacements, [
			'{name}' => htmlspecialchars($user->username),
			'{id}' => $user->user_id
		]);
		$alert['alert_text'] = strtr($body, $replacements);

		/** @var \XF\Repository\UserAlert $alertRepo */
		$alertRepo = $this->app->repository('XF:UserAlert');
		$alertRepo->alert(
			$user,
			$this->author['user_id'], $this->author['username'],
			'user', $user->user_id,
			'from_admin', $alert
		);
	}

	protected function getActionDescription()
	{
		$actionPhrase = \XF::phrase('alerting');
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

	protected function prepareAlert(array $alert, &$replacements = [])
	{
		if ($alert['link_url'])
		{
			$link = '<a href="' . $alert['link_url'] . '" class="fauxBlockLink-blockLink">'
				. ($alert['link_title'] ? $alert['link_title'] : $alert['link_url'])
				. '</a>';
			$replacements['{link}'] = $link;

			if (strpos($alert['alert_body'], '{link}') === false)
			{
				$alert['alert_body'] .= ' {link}';
			}
		}
		return $alert;
	}

	protected function prepareAuthor(array $alert)
	{
		$em = $this->app->em();
		$author = $em->find('XF:User', $alert['user_id']);
		if (!$author)
		{
			$author = [
				'user_id' => 0,
				'username' => ''
			];
		}
		else
		{
			$author = $author->toArray();
		}

		return $author;
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