<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int label_id
 * @property int bookmark_id
 * @property int use_date
 *
 * GETTERS
 * @property string|null label
 *
 * RELATIONS
 * @property \XF\Entity\BookmarkLabel Label
 * @property \XF\Entity\BookmarkItem Bookmark
 */
class BookmarkLabelUse extends Entity
{
	/**
	 * @return string|null
	 */
	public function getLabelName()
	{
		return $this->Label ? $this->Label->label : null;
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_bookmark_label_use';
		$structure->shortName = 'XF:BookmarkLabelUse';
		$structure->primaryKey = ['label_id', 'bookmark_id'];
		$structure->columns = [
			'label_id' => ['type' => self::UINT, 'required' => true],
			'bookmark_id' => ['type' => self::UINT, 'required' => true],
			'use_date' => ['type' => self::UINT, 'default' => 0],
		];
		$structure->getters = [
			'label' => ['getter' => 'getLabelName', 'cache' => false]
		];
		$structure->relations = [
			'Label' => [
				'entity' => 'XF:BookmarkLabel',
				'type' => self::TO_ONE,
				'conditions' => 'label_id',
				'primary' => true
			],
			'Bookmark' => [
				'entity' => 'XF:BookmarkItem',
				'type' => self::TO_ONE,
				'conditions' => 'bookmark_id',
				'primary' => true
			]
		];

		return $structure;
	}
}