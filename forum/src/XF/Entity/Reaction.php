<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null reaction_id
 * @property string text_color
 * @property int reaction_score
 * @property int display_order
 * @property bool active
 * @property string image_url
 * @property string image_url_2x
 * @property bool sprite_mode
 * @property array sprite_params
 *
 * GETTERS
 * @property \XF\Phrase title
 * @property mixed reaction_type
 * @property mixed score_title
 * @property mixed is_custom_score
 *
 * RELATIONS
 * @property \XF\Entity\Phrase MasterTitle
 */
class Reaction extends Entity
{
	use ImageSpriteTrait;

	public function canDelete(&$error = null)
	{
		if ($this->isDefaultReaction())
		{
			$error = \XF::phrase('it_is_not_possible_to_delete_default_reaction');
			return false;
		}

		return true;
	}

	public function canToggle(&$error = null)
	{
		if ($this->isDefaultReaction())
		{
			$error = \XF::phrase('it_is_not_possible_to_disable_default_reaction');
			return false;
		}

		return true;
	}

	public function canExport(&$error = null)
	{
		if ($this->isDefaultReaction())
		{
			$error = \XF::phrase('it_is_not_possible_to_export_default_reaction');
			return false;
		}

		return true;
	}

	public function getReactionType()
	{
		if ($this->reaction_score > 0)
		{
			return 'positive';
		}
		else if ($this->reaction_score < 0)
		{
			return 'negative';
		}
		else
		{
			return 'neutral';
		}
	}

	public function getScoreTitle()
	{
		return \XF::phrase('reaction_score.' . $this->reaction_type);
	}

	public function isDefaultReaction()
	{
		return ($this->reaction_id == 1);
	}

	public function isCustomScore()
	{
		return ($this->reaction_score > 1 || $this->reaction_score < -1);
	}

	/**
	 * @return \XF\Phrase
	 */
	public function getTitle()
	{
		return \XF::phrase($this->getPhraseName());
	}

	public function getPhraseName()
	{
		return 'reaction_title.' . $this->reaction_id;
	}

	public function getMasterPhrase()
	{
		$phrase = $this->MasterTitle;
		if (!$phrase)
		{
			$phrase = $this->_em->create('XF:Phrase');
			$phrase->title = $this->_getDeferredValue(function() { return $this->getPhraseName(); }, 'save');
			$phrase->addon_id = '';
			$phrase->language_id = 0;
		}

		return $phrase;
	}

	protected function _preSave()
	{
		if ($this->isChanged('active') && !$this->active && !$this->canToggle($error))
		{
			$this->error($error);
		}
	}

	protected function _postSave()
	{
		$this->rebuildReactionCache();
	}

	protected function _preDelete()
	{
		if (!$this->canDelete($error))
		{
			$this->error($error);
		}
	}

	protected function _postDelete()
	{
		if ($this->MasterTitle)
		{
			$this->MasterTitle->delete();
		}

		$this->app()->jobManager()->enqueueUnique('reactionDelete' . $this->reaction_id, 'XF:ReactionDelete', [
			'reaction_id' => $this->reaction_id,
			'reaction_score' => $this->reaction_score
		]);

		$this->rebuildReactionCache();
	}

	protected function rebuildReactionCache()
	{
		$repo = $this->getReactionRepo();

		\XF::runOnce('reactionCache', function() use ($repo)
		{
			$repo->rebuildReactionCache();
			$repo->rebuildReactionSpriteCache();
		});
	}

	protected function _setupDefaults()
	{
		$this->sprite_params = ['w' => 32, 'h' => 32, 'x' => 0, 'y' => 0, 'bs' => ''];
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_reaction';
		$structure->shortName = 'XF:Reaction';
		$structure->primaryKey = 'reaction_id';
		$structure->columns = [
			'reaction_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'text_color' => ['type' => self::STR, 'maxLength' => 100, 'default' => ''],
			'reaction_score' => ['type' => self::INT, 'default' => 1],
			'display_order' => ['type' => self::UINT, 'default' => 10],
			'active' => ['type' => self::BOOL, 'default' => true]
		];
		$structure->getters = [
			'title' => true,
			'reaction_type' => true,
			'score_title' =>true,
			'is_custom_score' => ['getter' => 'isCustomScore', 'cache' => false],
		];
		$structure->relations = [
			'MasterTitle' => [
				'entity' => 'XF:Phrase',
				'type' => self::TO_ONE,
				'conditions' => [
					['language_id', '=', 0],
					['title', '=', 'reaction_title.', '$reaction_id']
				]
			]
		];
		$structure->options = [];

		static::addImageSpriteStructureElements($structure);

		return $structure;
	}

	/**
	 * @return \XF\Repository\Reaction
	 */
	protected function getReactionRepo()
	{
		return $this->repository('XF:Reaction');
	}
}