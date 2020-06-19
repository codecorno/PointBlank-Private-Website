<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;
use XF\Util\Arr;

/**
 * COLUMNS
 * @property int|null smilie_id
 * @property string title
 * @property string smilie_text
 * @property int smilie_category_id
 * @property int display_order
 * @property bool display_in_editor
 * @property string image_url
 * @property string image_url_2x
 * @property bool sprite_mode
 * @property array sprite_params
 *
 * GETTERS
 * @property array|bool smilie_text_options
 *
 * RELATIONS
 * @property \XF\Entity\SmilieCategory Category
 */
class Smilie extends Entity
{
	use ImageSpriteTrait;

	/**
	 * @return array|bool
	 */
	public function getSmilieTextOptions()
	{
		return Arr::stringToArray($this->smilie_text, '/\r?\n/');
	}

	protected function verifySmilieText(&$smilieText)
	{
		$smilies = Arr::stringToArray($smilieText, '/\r?\n/');
		foreach ($smilies AS $k => &$v)
		{
			$v = trim($v);
			if (!strlen($v))
			{
				unset($smilies[$k]);
			}
		}
		$smilieText = implode("\n", $smilies);

		if ($this->getOption('check_duplicate'))
		{
			if ($this->isInsert() || $smilieText != $this->getExistingValue('smilie_text'))
			{
				$id = $this->smilie_id;

				$existing = $this->getSmilieRepo()->findSmiliesByText($smilieText);
				foreach ($existing AS $text => $smilie)
				{
					if (!$id || $smilie['smilie_id'] != $id)
					{
						$this->error(\XF::phrase('smilie_replacement_text_must_be_unique_x_in_use', ['text' => $text]), 'smilie_text');
						return false;
					}
				}
			}
		}

		return true;
	}

	protected function verifySmilieCategoryId(&$smilieCategoryId)
	{
		if ($smilieCategoryId > 0)
		{
			$smilieCategory = $this->_em->find('XF:SmilieCategory', $smilieCategoryId);

			if (!$smilieCategory)
			{
				$this->error(\XF::phrase('please_enter_valid_smilie_category_id'), 'smilie_category_id');
				return false;
			}
		}

		return true;
	}

	protected function _postSave()
	{
		$this->rebuildSmilieCache();
	}

	protected function _postDelete()
	{
		$this->rebuildSmilieCache();
	}

	protected function rebuildSmilieCache()
	{
		$repo = $this->getSmilieRepo();

		\XF::runOnce('smilieCache', function() use ($repo)
		{
			$repo->rebuildSmilieCache();
			$repo->rebuildSmilieSpriteCache();
		});
	}

	protected function _setupDefaults()
	{
		$this->sprite_params = ['w' => 22, 'h' => 22, 'x' => 0, 'y' => 0, 'bs' => ''];
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_smilie';
		$structure->shortName = 'XF:Smilie';
		$structure->primaryKey = 'smilie_id';
		$structure->columns = [
			'smilie_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'title' => ['type' => self::STR, 'maxLength' => 50,
				'required' => 'please_enter_valid_title'
			],
			'smilie_text' => ['type' => self::STR,
				'required' => 'please_enter_valid_smilie_text'
			],
			'smilie_category_id' => ['type' => self::UINT],
			'display_order' => ['type' => self::UINT, 'default' => 10],
			'display_in_editor' => ['type' => self::BOOL, 'default' => true]
		];
		$structure->getters = [
			'smilie_text_options' => true
		];
		$structure->relations = [
			'Category' => [
				'type' => self::TO_ONE,
				'entity' => 'XF:SmilieCategory',
				'conditions' => 'smilie_category_id'
			]
		];
		$structure->options = [
			'check_duplicate' => true
		];

		static::addImageSpriteStructureElements($structure);

		return $structure;
	}

	/**
	 * @return \XF\Repository\Smilie
	 */
	protected function getSmilieRepo()
	{
		return $this->repository('XF:Smilie');
	}
}