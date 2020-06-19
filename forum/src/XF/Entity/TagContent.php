<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null tag_content_id
 * @property string content_type
 * @property int content_id
 * @property int tag_id
 * @property int add_user_id
 * @property int add_date
 * @property bool visible
 * @property int content_date
 *
 * GETTERS
 * @property string|null tag
 *
 * RELATIONS
 * @property \XF\Entity\Tag Tag
 * @property \XF\Entity\User AddUser
 */
class TagContent extends Entity
{
	public function getHandler()
	{
		return $this->getTagRepo()->getTagHandler($this->content_type);
	}

	/**
	 * @return string|null
	 */
	public function getTagName()
	{
		return $this->Tag ? $this->Tag->tag : null;
	}

	/**
	 * @return null|Entity
	 */
	public function getContent()
	{
		$handler = $this->getHandler();
		return $handler ? $handler->getContent($this->content_id) : null;
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_tag_content';
		$structure->shortName = 'XF:TagContent';
		$structure->primaryKey = 'tag_content_id';
		$structure->columns = [
			'tag_content_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'content_type' => ['type' => self::STR, 'maxLength' => 25, 'required' => true],
			'content_id' => ['type' => self::UINT, 'required' => true],
			'tag_id' => ['type' => self::UINT, 'required' => true],
			'add_user_id' => ['type' => self::UINT, 'required' => true],
			'add_date' => ['type' => self::UINT, 'default' => \XF::$time],
			'visible' => ['type' => self::BOOL, 'required' => true],
			'content_date' => ['type' => self::UINT, 'required' => true],
		];
		$structure->getters = [
			'tag' => ['getter' => 'getTagName', 'cache' => false]
		];
		$structure->relations = [
			'Tag' => [
				'entity' => 'XF:Tag',
				'type' => self::TO_ONE,
				'conditions' => 'tag_id',
				'primary' => true
			],
			'AddUser' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => [['user_id', '=', '$add_user_id']],
				'primary' => true
			]
		];

		return $structure;
	}

	/**
	 * @return \XF\Repository\Tag
	 */
	protected function getTagRepo()
	{
		return $this->repository('XF:Tag');
	}
}