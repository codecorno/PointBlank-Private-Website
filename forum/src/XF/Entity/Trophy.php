<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null trophy_id
 * @property int trophy_points
 * @property array user_criteria
 *
 * GETTERS
 * @property \XF\Phrase title
 * @property \XF\Phrase description
 *
 * RELATIONS
 * @property \XF\Entity\Phrase MasterTitle
 * @property \XF\Entity\Phrase MasterDescription
 */
class Trophy extends Entity
{
	/**
	 * @return \XF\Phrase
	 */
	public function getTitle()
	{
		return \XF::phrase($this->getPhraseName(true));
	}

	/**
	 * @return \XF\Phrase
	 */
	public function getDescription()
	{
		return \XF::phrase($this->getPhraseName(false));
	}

	public function getPhraseName($title)
	{
		return 'trophy_' . ($title ? 'title' : 'description') . '.' . $this->trophy_id;
	}

	public function getMasterPhrase($title)
	{
		$phrase = $title ? $this->MasterTitle : $this->MasterDescription;
		if (!$phrase)
		{
			$phrase = $this->_em->create('XF:Phrase');
			$phrase->title = $this->_getDeferredValue(function() use ($title) { return $this->getPhraseName($title); }, 'save');
			$phrase->language_id = 0;
			$phrase->addon_id = '';
		}

		return $phrase;
	}

	protected function verifyUserCriteria(&$criteria)
	{
		$userCriteria = $this->app()->criteria('XF:User', $criteria);
		$criteria = $userCriteria->getCriteria();
		return true;
	}

	protected function _postSave()
	{
		if ($this->isUpdate() && $this->isChanged('trophy_points'))
		{
			$this->updateTrophyPoints($this->trophy_id, $this->getExistingValue('trophy_points'), $this->trophy_points);
		}
	}

	protected function _postDelete()
	{
		if ($this->MasterTitle)
		{
			$this->MasterTitle->delete();
		}
		if ($this->MasterDescription)
		{
			$this->MasterDescription->delete();
		}

		$this->updateTrophyPoints($this->trophy_id, $this->trophy_points, 0);

		$this->db()->delete('xf_user_trophy', 'trophy_id = ?', $this->trophy_id);

		$this->db()->delete('xf_user_alert',
			"content_type = 'trophy' AND action = 'award' AND extra_data LIKE '%i:" . intval($this->trophy_id) . ";%'"
		);
	}

	protected function updateTrophyPoints($trophyId, $oldPoints, $newPoints)
	{
		$adjust = $oldPoints - $newPoints;

		$this->db()->query('
			UPDATE xf_user SET
				trophy_points = IF(trophy_points > ?, trophy_points - ?, 0)
			WHERE user_id IN (
				SELECT user_id
				FROM xf_user_trophy
				WHERE trophy_id = ?
			)
		', [$adjust, $adjust, $trophyId]);
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_trophy';
		$structure->shortName = 'XF:Trophy';
		$structure->primaryKey = 'trophy_id';
		$structure->columns = [
			'trophy_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'trophy_points' => ['type' => self::UINT, 'required' => true],
			'user_criteria' => ['type' => self::JSON_ARRAY, 'default' => [],
				'required' => 'please_select_criteria_that_must_be_met'
			]
		];
		$structure->getters = [
			'title' => true,
			'description' => true
		];
		$structure->relations = [
			'MasterTitle' => [
				'entity' => 'XF:Phrase',
				'type' => self::TO_ONE,
				'conditions' => [
					['language_id', '=', 0],
					['title', '=', 'trophy_title.', '$trophy_id']
				]
			],
			'MasterDescription' => [
				'entity' => 'XF:Phrase',
				'type' => self::TO_ONE,
				'conditions' => [
					['language_id', '=', 0],
					['title', '=', 'trophy_description.', '$trophy_id']
				]
			],
		];

		return $structure;
	}
}