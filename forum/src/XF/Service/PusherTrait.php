<?php

namespace XF\Service;

use Symfony\Component\DomCrawler\Crawler;
use XF\Entity\User;
use XF\Language;
use XF\Style;

trait PusherTrait
{
	/**
	 * @var User
	 */
	protected $receiever;

	/**
	 * @var Language
	 */
	protected $language;

	/**
	 * @var Style
	 */
	protected $style;

	/**
	 * @var \XF\Template\Templater
	 */
	protected $templater;

	protected $notificationUrl = null;

	protected $notificationTag = '';

	public function __construct(\XF\App $app, User $receiver, ...$properties)
	{
		parent::__construct($app, $receiver, $properties);

		$this->receiver = $receiver;
		$this->language = $app->language($receiver->language_id);
		$this->style = $app->style($receiver->style_id ?: $app->options()->defaultStyleId);
		$this->templater = $app->templater();

		$this->setInitialProperties(...$properties);
	}

	abstract protected function setInitialProperties(...$properties);

	abstract protected function getNotificationTitle();

	abstract protected function getNotificationBody();

	protected function getNotificationUrl()
	{
		return $this->notificationUrl;
	}

	protected function getNotificationTag()
	{
		return $this->notificationTag;
	}

	protected function setAdditionalOptions(PushNotification $pushNotification)
	{
	}

	protected function setUserOptions(PushNotification $pushNotification, \XF\Entity\User $user)
	{
		/** @var \XF\App $app */
		$app = $this->app;

		$avatar = $user->getAvatarUrl('m');
		if ($avatar)
		{
			$pushNotification->setIconAndBadge($avatar);
		}
		else if (!$app->options()->dynamicAvatarEnable)
		{
			$style = $app->style($this->receiver->style_id);
			if ($style->getProperty('avatarDefaultType') === 'image')
			{
				$url = $style->getProperty('avatarDefaultImage');
				if ($url)
				{
					$pushNotification->setIconAndBadge($url);
				}
			}
		}
	}
	
	protected function renderPushTemplate($template, array $params = [])
	{
		$originalLang = $this->templater->getLanguage();
		$originalStyle = $this->templater->getStyle();

		try
		{
			$this->templater->setLanguage($this->language);
			$this->templater->setStyle($this->style);

			$this->templater->addDefaultParam('xf', \XF::app()->getGlobalTemplateData());
			$pushContent = $this->templater->renderTemplate($template, $params);
		}
		finally
		{
			if ($originalLang)
			{
				$this->templater->setLanguage($originalLang);
			}
			if ($originalStyle)
			{
				$this->templater->setStyle($originalStyle);
			}
		}

		$this->notificationUrl = $this->findNotificationUrl($pushContent);
		$this->notificationTag = $this->findNotificationTag($pushContent);

		$pushContent = strip_tags($pushContent);
		$pushContent = html_entity_decode($pushContent, ENT_QUOTES);

		return trim($pushContent);
	}
	
	protected function findNotificationUrl(&$pushContent)
	{
		if (preg_match('#<push:url>(.*)</push:url>#siU', $pushContent, $match))
		{
			$targetLink = trim(htmlspecialchars_decode($match[1], ENT_QUOTES));
			$pushContent = preg_replace('#<push:url>.*</push:url>#siU', '', $pushContent);
		}
		else
		{
			// can do a quick bail out if there's no clear block link
			if (strpos($pushContent, 'fauxBlockLink-blockLink') === false)
			{
				return '';
			}

			$crawler = new Crawler($pushContent);
			$crawler = $crawler->filter('a.fauxBlockLink-blockLink');

			if (!$crawler->count())
			{
				return '';
			}

			$hrefAttrs = $crawler->extract(['href']);
			$targetLink = reset($hrefAttrs);
		}

		return $targetLink;
	}

	protected function findNotificationTag(&$pushContent)
	{
		$tag = '';

		if (preg_match('#<push:tag>(.*)</push:tag>#siU', $pushContent, $match))
		{
			$tag = trim(htmlspecialchars_decode($match[1], ENT_QUOTES));
			$pushContent = preg_replace('#<push:tag>.*</push:tag>#siU', '', $pushContent);
		}

		return $tag;
	}

	public function push()
	{
		/** @var \XF\Service\PushNotification $pushNotification */
		$pushNotification = $this->service('XF:PushNotification', $this->receiver);

		$pushNotification->setNotificationContent(
			$this->getNotificationTitle(), $this->getNotificationBody(), $this->getNotificationUrl()
		);

		$pushNotification->setNotificationTag($this->getNotificationTag());

		$this->setAdditionalOptions($pushNotification);

		$pushNotification->sendNotifications();
	}
}