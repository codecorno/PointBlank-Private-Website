<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null phrase_map_id
 * @property string title
 * @property int language_id
 * @property int phrase_id
 * @property string phrase_group
 *
 * RELATIONS
 * @property \XF\Entity\Language Language
 * @property \XF\Entity\Phrase Phrase
 */
class PhraseMap extends Entity
{
	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_phrase_map';
		$structure->shortName = 'XF:PhraseMap';
		$structure->primaryKey = 'phrase_map_id';
		$structure->columns = [
			'phrase_map_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'title' => ['type' => self::STR, 'maxLength' => 100, 'required' => true],
			'language_id' => ['type' => self::UINT, 'required' => true],
			'phrase_id' => ['type' => self::UINT, 'required' => true],
			'phrase_group' => ['type' => self::STR, 'maxLength' => 50, 'default' => null]
		];
		$structure->getters = [];
		$structure->relations = [
			'Language' => [
				'type' => self::TO_ONE,
				'entity' => 'XF:Language',
				'conditions' => 'language_id',
				'primary' => true
			],
			'Phrase' => [
				'type' => self::TO_ONE,
				'entity' => 'XF:Phrase',
				'conditions' => 'phrase_id',
				'primary' => true
			]
		];

		return $structure;
	}

	/**
	 * @return \XF\Repository\Phrase
	 */
	protected function getPhraseRepo()
	{
		return $this->repository('XF:Phrase');
	}
}