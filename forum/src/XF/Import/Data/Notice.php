<?php

namespace XF\Import\Data;

class Notice extends AbstractEmulatedData
{
	public function getImportType()
	{
		return 'notice';
	}

	public function getEntityShortName()
	{
		return 'XF:Notice';
	}

	public function setPageCriteria(array $criteria)
	{
		$pageCriteria = $this->app()->criteria('XF:Page', $this->reformatCriteria($criteria));
		$this->page_criteria = $pageCriteria->getCriteria();
	}

	public function setUserCriteria(array $criteria)
	{
		$userCriteria = $this->app()->criteria('XF:User', $this->reformatCriteria($criteria));
		$this->user_criteria = $userCriteria->getCriteria();
	}

	/**
	 * Reformats criteria from [$rule => $data] to [$rule => ['rule' => $rule, 'data' => $data]]
	 *
	 * @param array $criteria
	 *
	 * @return array
	 */
	protected function reformatCriteria(array $criteria)
	{
		$c = [];

		foreach ($criteria AS $rule => $data)
		{
			$c[$rule] = ['rule' => $rule, 'data' => $data];
		}

		return $c;
	}

	protected function postSave($oldId, $newId)
	{
		/** @var \XF\Repository\Notice $repo */
		$repo = $this->repository('XF:Notice');

		\XF::runOnce('noticeCacheRebuild', function() use ($repo)
		{
			$repo->rebuildNoticeCache();
		});
	}
}