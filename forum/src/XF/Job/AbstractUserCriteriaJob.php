<?php

namespace XF\Job;

use XF\Entity\User;

abstract class AbstractUserCriteriaJob extends AbstractJob
{
	protected $extraDefaultData = [
		'start' => 0,
		'count' => 0,
		'total' => null,
		'criteria' => null,
		'userIds' => null
	];

	abstract protected function executeAction(User $user);
	abstract protected function getActionDescription();

	protected function setupData(array $data)
	{
		$this->defaultData = array_merge($this->extraDefaultData, $this->defaultData);

		return parent::setupData($data);
	}

	public function run($maxRunTime)
	{
		$startTime = microtime(true);
		$em = $this->app->em();

		$ids = $this->prepareUserIds();
		if (!$ids)
		{
			return $this->complete();
		}

		$this->actionSetup();

		$transaction = $this->wrapTransaction();

		$db = $this->app->db();
		if ($transaction)
		{
			$db->beginTransaction();
		}

		$limitTime = ($maxRunTime > 0);
		foreach ($ids AS $key => $id)
		{
			$this->data['count']++;
			$this->data['start'] = $id;
			unset($ids[$key]);

			/** @var User $user */
			$user = $em->find('XF:User', $id);
			if ($user)
			{
				$this->executeAction($user);
			}

			if ($limitTime && microtime(true) - $startTime > $maxRunTime)
			{
				break;
			}
		}

		if (is_array($this->data['userIds']))
		{
			$this->data['userIds'] = array_values($ids);
		}

		if ($transaction)
		{
			$db->commit();
		}

		return $this->resume();
	}

	protected function prepareUserIds()
	{
		if (is_array($this->data['criteria']) && is_array($this->data['userIds']))
		{
			throw new \LogicException("Cannot have both criteria and userIds values; one must be null");
		}

		if (is_array($this->data['criteria']))
		{
			$searcher = $this->app->searcher('XF:User', $this->data['criteria']);
			$results = $searcher->getFinder()
				->where('user_id', '>', $this->data['start'])
				->order('user_id')
				->limit(1000)
				->fetchColumns('user_id');
			$ids = array_column($results, 'user_id');
		}
		else if (is_array($this->data['userIds']))
		{
			$ids = $this->data['userIds'];
		}
		else
		{
			throw new \LogicException("One of criteria and userIds values must be an array");
		}

		sort($ids, SORT_NUMERIC);
		return $ids;
	}

	protected function actionSetup()
	{
	}

	protected function wrapTransaction()
	{
		return true;
	}

	public function getStatusMessage()
	{
		$description = $this->getActionDescription();

		if ($this->data['total'] !== null)
		{
			return sprintf('%s (%d/%d)', $description, $this->data['count'], $this->data['total']);
		}
		else
		{
			return sprintf('%s (%d)', $description, $this->data['count']);
		}
	}
}