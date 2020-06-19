<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null log_id
 * @property string content_type
 * @property int content_id
 * @property int edit_user_id
 * @property int edit_date
 * @property string field
 * @property string old_value
 * @property string new_value
 * @property bool protected
 *
 * GETTERS
 * @property Entity|null Content
 * @property \XF\ChangeLog\DisplayEntry|null DisplayEntry
 *
 * RELATIONS
 * @property \XF\Entity\User EditUser
 */
class ChangeLog extends Entity
{
	public function getHandler()
	{
		return $this->getChangeLogRepo()->getChangeLogHandler($this->content_type);
	}

	/**
	 * @return Entity|null
	 */
	public function getContent()
	{
		$handler = $this->getHandler();
		return $handler ? $handler->getContent($this->content_id) : null;
	}

	public function setContent(Entity $content = null)
	{
		$this->_getterCache['Content'] = $content;
	}

	/**
	 * @return \XF\ChangeLog\DisplayEntry|null
	 */
	public function getDisplayEntry()
	{
		$handler = $this->getHandler();
		return $handler ? $handler->getDisplayEntry($this) : null;
	}

	public function setDisplayEntry(\XF\ChangeLog\DisplayEntry $displayEntry)
	{
		$this->_getterCache['DisplayEntry'] = $displayEntry;
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_change_log';
		$structure->shortName = 'XF:ChangeLog';
		$structure->primaryKey = 'log_id';
		$structure->columns = [
			'log_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'content_type' => ['type' => self::STR, 'maxLength' => 25, 'required' => true],
			'content_id' => ['type' => self::UINT, 'required' => true],
			'edit_user_id' => ['type' => self::UINT, 'default' => 0],
			'edit_date' => ['type' => self::UINT, 'default' => \XF::$time],
			'field' => ['type' => self::STR, 'maxLength' => 100, 'required' => true],
			'old_value' => ['type' => self::STR],
			'new_value' => ['type' => self::STR],
			'protected' => ['type' => self::BOOL, 'default' => 0]
		];
		$structure->getters = [
			'Content' => true,
			'DisplayEntry' => true
		];
		$structure->relations = [
			'EditUser' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => [['user_id', '=', '$edit_user_id']],
				'primary' => true
			],
		];
		$structure->options = [];

		return $structure;
	}

	/**
	 * @return \XF\Repository\ChangeLog
	 */
	protected function getChangeLogRepo()
	{
		return $this->repository('XF:ChangeLog');
	}
}