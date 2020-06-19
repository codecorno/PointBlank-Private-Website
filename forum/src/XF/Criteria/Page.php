<?php

namespace XF\Criteria;

class Page extends AbstractCriteria
{
	protected $pageState = [];

	public function __construct(\XF\App $app, array $criteria, array $pageState = [])
	{
		parent::__construct($app, $criteria);
		$this->pageState = $pageState;
	}

	public function setPageState(array $pageState)
	{
		$this->pageState = $pageState;
	}

	public function getPageState()
	{
		return $this->pageState;
	}

	protected function isUnknownMatched($rule, array $data, \XF\Entity\User $user)
	{
		$eventReturnValue = false;
		$this->app->fire('criteria_page', [$rule, $data, $user, $this->pageState, &$eventReturnValue]);

		return $eventReturnValue;
	}

	protected function _matchBefore(array $data, \XF\Entity\User $user)
	{
		try
		{
			$tz = new \DateTimeZone($data['user_tz'] ? $user->timezone : $data['timezone']);
		}
		catch (\Exception $e)
		{
			$tz = \XF::language()->getTimeZone();
		}

		$data['hh'] = !empty($data['hh']) ? $data['hh'] : '12';
		$data['mm'] = !empty($data['mm']) ? $data['mm'] : '00';

		$datetime = new \DateTime("$data[ymd] $data[hh]:$data[mm]", $tz);
		return (time() < $datetime->format('U'));
	}

	protected function _matchAfter(array $data, \XF\Entity\User $user)
	{
		try
		{
			$tz = new \DateTimeZone($data['user_tz'] ? $user->timezone : $data['timezone']);
		}
		catch (\Exception $e)
		{
			$tz = \XF::language()->getTimeZone();
		}

		$data['hh'] = !empty($data['hh']) ? $data['hh'] : '12';
		$data['mm'] = !empty($data['mm']) ? $data['mm'] : '00';

		$datetime = new \DateTime("$data[ymd] $data[hh]:$data[mm]", $tz);
		return (time() >= $datetime->format('U'));
	}

	protected function _matchFromSearch(array $data, \XF\Entity\User $user)
	{
		if (!empty($this->pageState['fromSearch']))
		{
			// we're potentially doing something search specific here, so don't allow this to be cached
			\XF\Pub\App::$allowPageCache = false;
			return true;
		}
		else
		{
			return false;
		}
	}

	protected function _matchStyle(array $data, \XF\Entity\User $user)
	{
		return !empty($this->pageState['pageStyleId']) && $this->pageState['pageStyleId'] == $data['style_id'];
	}

	protected function _matchNodes(array $data, \XF\Entity\User $user)
	{
		$params = $this->pageState;

		if (!isset($params['breadcrumbs']) || !is_array($params['breadcrumbs']))
		{
			return false;
		}
		if (empty($data['node_ids']))
		{
			return false; // no node ids specified
		}

		if (empty($data['node_only']))
		{
			foreach ($params['breadcrumbs'] AS $i => $navItem)
			{
				if (isset($navItem['attributes']['node_id']) && in_array($navItem['attributes']['node_id'], $data['node_ids']))
				{
					return true;
				}
			}
		}

		if ($params['containerKey'])
		{
			list ($type, $id) = explode('-', $params['containerKey'], 2);

			if ($type == 'node' && $id && in_array($id, $data['node_ids']))
			{
				return true;
			}
		}

		return false;
	}

	protected function _matchController(array $data, \XF\Entity\User $user)
	{
		$params = $this->pageState;

		if (!isset($params['controller']))
		{
			return false;
		}

		$formatter = '%s\%s\Controller\%s';

		$controllerParam = $params['controller'];
		if (strpos($controllerParam, ':'))
		{
			$controllerParam = \XF::stringToClass($controllerParam, $formatter, $params['classType']);
		}

		$controllerCriteria = $data['name'];
		if (strpos($controllerCriteria, ':'))
		{
			$controllerCriteria = \XF::stringToClass($controllerCriteria, $formatter, $params['classType']);
		}

		if ($controllerParam != $controllerCriteria)
		{
			return false;
		}

		if (!empty($data['action']) && isset($params['action']))
		{
			if (strtolower($params['action']) != strtolower($data['action']))
			{
				return false;
			}
		}

		return true;
	}

	protected function _matchView(array $data, \XF\Entity\User $user)
	{
		$params = $this->pageState;

		if (!isset($params['view']))
		{
			return false;
		}

		$formatter = '%s\%s\View\%s';

		$viewParam = $params['view'];
		if (strpos($viewParam, ':'))
		{
			$viewParam = \XF::stringToClass($viewParam, $formatter, $params['classType']);
		}

		$viewCriteria = $data['name'];
		if (strpos($viewCriteria, ':'))
		{
			$viewCriteria = \XF::stringToClass($viewCriteria, $formatter, $params['classType']);
		}

		if ($viewParam != $viewCriteria)
		{
			return false;
		}

		return true;
	}

	protected function _matchTemplate(array $data, \XF\Entity\User $user)
	{
		$params = $this->pageState;
		return (isset($params['template']) && strtolower($params['template']) == strtolower($data['name']));
	}

	protected function _matchTab(array $data, \XF\Entity\User $user)
	{
		$params = $this->pageState;
		return (isset($params['pageSection']) && strtolower($params['pageSection']) == strtolower($data['id']));
	}
}