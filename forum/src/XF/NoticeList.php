<?php

namespace XF;

class NoticeList
{
	/**
	 * @var App
	 */
	protected $app;

	/**
	 * @var Entity\User
	 */
	protected $user;

	/**
	 * @var array
	 */
	protected $pageParams = [];

	protected $notices = [
		'block' => [],
		'floating' => [],
		'scrolling' => [],
		'bottom_fixer' => []
	];

	protected $tokens = [];

	protected $dismissed = [];

	protected $noticeBase = [
		'title' => '',
		'message' => '',
		'dismissible' => false,
		'wrap' => true,
		'user_criteria' => [],
		'page_criteria' => [],
		'display_image' => '',
		'image_url' => '',
		'visibility' => '',
		'display_style' => 'primary',
		'css_class' => '',
		'display_duration' => 0,
		'delay_duration' => 0,
		'auto_dismiss' => 0
	];

	public function __construct(App $app, \XF\Entity\User $user, array $pageParams)
	{
		$this->app = $app;
		$this->user = $user;
		$this->pageParams = $pageParams;

		$this->tokens = $this->getTokens();
	}

	public function setDismissed(array $dismissed)
	{
		$this->dismissed = $dismissed;
	}

	protected function getTokens()
	{
		return [
			'{user_id}' => strval($this->user->user_id),
			'{name}' => $this->user->user_id ? htmlspecialchars($this->user->username) : \XF::phrase('guest')
		];
	}

	public function addNotice($key, $type, $message, array $override = [])
	{
		$notice = $override + $this->noticeBase;

		$tokens = $this->tokens;
		$tokens['{title}'] = $notice['title'];

		$notice['message'] = strtr($message, $tokens);

		$this->notices[$type][$key] = $notice;
	}

	public function addConditionalNotice($key, $type, $message, array $override)
	{
		$notice = $override + $this->noticeBase;

		if ($notice['dismissible'] && isset($this->dismissed[$key]))
		{
			return false;
		}

		$matchesUser = $this->app->criteria('XF:User', $notice['user_criteria'])->isMatched($this->user);
		if (!$matchesUser)
		{
			return false;
		}

		$matchesPage = $this->app->criteria('XF:Page', $notice['page_criteria'], $this->pageParams)->isMatched($this->user);
		if (!$matchesPage)
		{
			return false;
		}

		$this->addNotice($key, $type, $message, $notice);
		return true;
	}

	public function getNotices()
	{
		return $this->notices;
	}

	public function getTypeNotices($type)
	{
		return $this->notices[$type];
	}

	public function hasTypeNotices($type)
	{
		return !empty($this->notices[$type]);
	}

	public function isValidNoticeType($type)
	{
		return isset($this->notices[$type]);
	}

	public function getNotice($key, $type = null)
	{
		if ($type)
		{
			if (isset($this->notices[$type][$key]))
			{
				return $this->notices[$type][$key];
			}
		}
		else
		{
			foreach (array_keys($this->notices) AS $type)
			{
				if (isset($this->notices[$type][$key]))
				{
					return $this->notices[$type][$key];
				}
			}
		}

		return null;
	}

	public function hasNotice($key, $type = null)
	{
		if ($type)
		{
			return isset($this->notices[$type][$key]);
		}
		else
		{
			foreach (array_keys($this->notices) AS $type)
			{
				if (isset($this->notices[$type][$key]))
				{
					return true;
				}
			}

			return false;
		}
	}

	public function removeNotice($key, $type = null)
	{
		if ($type)
		{
			unset($this->notices[$type][$key]);
		}
		else
		{
			foreach (array_keys($this->notices) AS $type)
			{
				unset($this->notices[$type][$key]);
			}
		}
	}
}