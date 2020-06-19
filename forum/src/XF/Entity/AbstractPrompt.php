<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;
use XF\Template\Compiler\Syntax\Str;

/**
 * Class AbstractPrompt
 *
 * @package XF\Entity
 *
 * COLUMNS
 * @property int|null prompt_id
 * @property int prompt_group_id
 * @property int display_order
 * @property int materialized_order
 *
 * GETTERS
 * @property \XF\Phrase|string title
 *
 * RELATIONS
 * @property \XF\Phrase MasterTitle
 * @property \XF\Entity\AbstractPromptGroup PromptGroup
 */
abstract class AbstractPrompt extends Entity
{
	abstract protected function getClassIdentifier();

	protected static function getContentType()
	{
		throw new \LogicException('The phrase group must be overridden.');
	}

	public function getPhraseName()
	{
		return static::getContentType() . '_prompt.' . $this->prompt_id;
	}

	/**
	 * @return \XF\Phrase|string
	 */
	public function getTitle()
	{
		return $this->prompt_id ? \XF::phrase($this->getPhraseName(), [], false) : '';
	}

	public function getMasterPhrase()
	{
		$phrase = $this->MasterTitle;
		if (!$phrase)
		{
			$phrase = $this->_em->create('XF:Phrase');
			$phrase->title = $this->_getDeferredValue(function()  { return $this->getPhraseName(); }, 'save');
			$phrase->language_id = 0;
			$phrase->addon_id = '';
		}

		return $phrase;
	}

	protected function _postSave()
	{
		$this->rebuildPromptCaches();
	}

	protected function _postDelete()
	{
		if ($this->MasterTitle)
		{
			$this->MasterTitle->delete();
		}

		$this->rebuildPromptCaches();
	}

	protected function rebuildPromptCaches()
	{
		$repo = $this->getPromptRepo();

		\XF::runOnce($this->getContentType() . 'PromptCaches', function() use ($repo)
		{
			$repo->rebuildPromptMaterializedOrder();
		});
	}

	protected static function setupDefaultStructure(Structure $structure, $table, $shortName)
	{
		$structure->table = $table;
		$structure->shortName = $shortName;
		$structure->primaryKey = 'prompt_id';
		$structure->columns = [
			'prompt_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'prompt_group_id' => ['type' => self::UINT, 'default' => 0],
			'display_order' => ['type' => self::UINT, 'forced' => true, 'default' => 1],
			'materialized_order' => ['type' => self::UINT, 'forced' => true, 'default' => 0]
		];
		$structure->getters = [
			'title' => true
		];
		$structure->relations = [
			'MasterTitle' => [
				'entity' => 'XF:Phrase',
				'type' => self::TO_ONE,
				'conditions' => [
					['language_id', '=', 0],
					['title', '=', static::getContentType() . '_prompt.', '$prompt_id']
				]
			],
			'PromptGroup' => [
				'entity' => $shortName . 'Group',
				'type' => self::TO_ONE,
				'conditions' => 'prompt_group_id',
				'primary' => true
			]
		];
	}

	/**
	 * @return \XF\Repository\AbstractPrompt
	 */
	protected function getPromptRepo()
	{
		return $this->repository($this->getClassIdentifier());
	}
}