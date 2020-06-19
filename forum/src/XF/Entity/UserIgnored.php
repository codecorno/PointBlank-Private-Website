<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int user_id
 * @property int ignored_user_id
 */
class UserIgnored extends Entity
{
	protected function _preSave()
	{
		if ($this->isInsert())
		{
			if ($this->user_id == $this->ignored_user_id)
			{
				$this->error(\XF::phrase('you_may_not_ignore_yourself'));
			}

			$exists = $this->em()->findOne('XF:UserIgnored', [
				'user_id' => $this->user_id,
				'ignored_user_id' => $this->ignored_user_id
			]);
			if ($exists)
			{
				$this->error(\XF::phrase('you_already_ignoring_this_member'));
			}

			$ignoredFinder = $this->finder('XF:UserIgnored');
			$total = $ignoredFinder
				->where('user_id', $this->user_id)
				->total();
			$ignoredLimit = 1000;
			if ($total >= $ignoredLimit )
			{
				$this->error(\XF::phrase('you_may_only_ignore_x_people', ['count' => $ignoredLimit]));
			}
		}
	}

	protected function _postSave()
	{
		$this->rebuildIgnoredCache();
	}

	protected function _postDelete()
	{
		$this->rebuildIgnoredCache();
	}

	protected function rebuildIgnoredCache()
	{
		$this->getIgnoredRepo()->rebuildIgnoredCache($this->user_id);
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_user_ignored';
		$structure->shortName = 'XF:UserIgnored';
		$structure->primaryKey = ['user_id', 'ignored_user_id'];
		$structure->columns = [
			'user_id' => ['type' => self::UINT, 'required' => true],
			'ignored_user_id' => ['type' => self::UINT, 'required' => true]
		];
		$structure->getters = [];
		$structure->relations = [];

		return $structure;
	}

	/**
	 * @return \XF\Repository\UserIgnored
	 */
	protected function getIgnoredRepo()
	{
		return $this->repository('XF:UserIgnored');
	}
}