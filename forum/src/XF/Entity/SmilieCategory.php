<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null smilie_category_id
 * @property int display_order
 *
 * GETTERS
 * @property \XF\Phrase title
 *
 * RELATIONS
 * @property \XF\Mvc\Entity\AbstractCollection|\XF\Entity\Smilie[] Smilies
 * @property \XF\Entity\Phrase MasterTitle
 */
class SmilieCategory extends Entity
{
	/**
	 * @return \XF\Phrase
	 */
	public function getTitle()
	{
		return \XF::phrase($this->getPhraseName());
	}

	public function getPhraseName()
	{
		if ($this->smilie_category_id === 0)
		{
			return 'smilie_category_title.uncategorized';
		}
		else
		{
			return 'smilie_category_title.' . $this->smilie_category_id;
		}
	}

	public function getMasterPhrase()
	{
		$phrase = $this->MasterTitle;

		if (!$phrase)
		{
			$phrase = $this->_em->create('XF:Phrase');
			$phrase->title = $this->_getDeferredValue(function() { return $this->getPhraseName(); }, 'save');
			$phrase->language_id = 0;
			$phrase->addon_id = '';
		}

		return $phrase;
	}

	protected function _postDelete()
	{
		if ($this->MasterTitle)
		{
			$this->MasterTitle->delete();
		}

		$this->db()->update('xf_smilie',
			['smilie_category_id' => 0],
			'smilie_category_id = ?',
			$this->smilie_category_id
		);
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_smilie_category';
		$structure->shortName = 'XF:SmilieCategory';
		$structure->primaryKey = 'smilie_category_id';
		$structure->columns = [
			'smilie_category_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true,
				'unique' => 'smilie_category_ids_must_be_unique'
			],
			'display_order' => ['type' => self::UINT, 'default' => 0]
		];
		$structure->getters = [
			'title' => true
		];
		$structure->relations = [
			'Smilies' => [
				'entity' => 'XF:Smilie',
				'type' => self::TO_MANY,
				'conditions' => [
					['smilie_category_id', '=', '$smilie_category_id']
				]
			],
			'MasterTitle' => [
				'entity' => 'XF:Phrase',
				'type' => self::TO_ONE,
				'conditions' => [
					['language_id', '=', 0],
					['title', '=', 'smilie_category_title.', '$smilie_category_id']
				]
			]
		];

		return $structure;
	}
}