<?php

namespace XF\Service\Alert;

use Symfony\Component\DomCrawler\Crawler;
use XF\Alert\AbstractHandler;
use XF\Entity\User;
use XF\Entity\UserAlert;
use XF\Mvc\Entity\Entity;
use XF\Service\AbstractService;
use XF\Service\PusherTrait;
use XF\Service\PushNotification;

class Pusher extends AbstractService
{
	use PusherTrait;

	/**
	 * @var UserAlert
	 */
	protected $alert;

	/**
	 * @var AbstractHandler
	 */
	protected $handler;

	/**
	 * @var Entity
	 */
	protected $content;

	protected function setInitialProperties(UserAlert $alert)
	{
		$this->alert = $alert;
		$this->handler = $alert->getHandler();
		$this->content = $alert->getContent();
	}

	protected function getNotificationTitle()
	{
		$phrase = $this->language->phrase('new_alert_at_x', ['boardTitle' => $this->app->options()->boardTitle]);

		return $phrase->render('raw');
	}

	protected function getNotificationBody()
	{
		$alert = $this->alert;
		$handler = $this->handler;
		$content = $this->content;

		$templateName = $handler->getPushTemplateName($alert->action);
		if (!$this->templater->isKnownTemplate($templateName))
		{
			$templateName = $handler->getTemplateName($alert->action);
		}
		$templateData = $handler->getTemplateData($alert->action, $alert, $content);

		return $this->renderPushTemplate($templateName, $templateData);
	}

	protected function setAdditionalOptions(PushNotification $pushNotification)
	{
		$user = $this->alert->User;
		if ($user)
		{
			$this->setUserOptions($pushNotification, $user);
		}
	}
}