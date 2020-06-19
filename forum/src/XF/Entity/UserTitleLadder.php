<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int minimum_level
 * @property string title
 */
class UserTitleLadder extends Entity
{
	protected function _postSave()
	{
		if ($this->isChanged(['minimum_level', 'title']))
		{
			$this->rebuildLadderCache();
		}
	}

	protected function _postDelete()
	{
		$this->rebuildLadderCache();
	}

	protected function rebuildLadderCache()
	{
		/** @var \XF\Repository\UserTitleLadder $repo */
		$repo = $this->repository('XF:UserTitleLadder');

		\XF::runOnce('userTitleLadderCacheRebuild', function() use ($repo)
		{
			$repo->rebuildLadderCache();
		});
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_user_title_ladder';
		$structure->shortName = 'XF:UserTitleLadder';
		$structure->primaryKey = 'minimum_level';
		$structure->columns = [
			'minimum_level' => ['type' => self::UINT, 'required' => true],
			'title' => ['type' => self::STR, 'required' => true, 'maxLength' => 250]
		];
		$structure->getters = [];
		$structure->relations = [];

		return $structure;
	}
}