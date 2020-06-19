<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;
use XF\Repository;

/**
 * COLUMNS
 * @property int|null language_id
 * @property int parent_id
 * @property array parent_list
 * @property string title
 * @property string date_format
 * @property string time_format
 * @property string currency_format
 * @property string decimal_point
 * @property string thousands_separator
 * @property array phrase_cache
 * @property string language_code
 * @property string text_direction
 * @property int week_start
 * @property string label_separator
 * @property string comma_separator
 * @property string ellipsis
 * @property string parenthesis_open
 * @property string parenthesis_close
 *
 * RELATIONS
 * @property \XF\Entity\Language Parent
 * @property \XF\Mvc\Entity\AbstractCollection|\XF\Entity\Phrase[] Phrases
 */
class Language extends Entity
{
	public function canEdit()
	{
		if (!$this->language_id && !\XF::$developmentMode)
		{
			return false;
		}

		return true;
	}

	public function isMaster()
	{
		return !$this->language_id;
	}

	protected function verifyParentId($parentId)
	{
		if ($this->isUpdate() && $parentId)
		{
			$parent = $this->_em->find('XF:Language', $parentId);
			if (!$parent || in_array($this->language_id, $parent->parent_list))
			{
				$this->error(\XF::phrase('please_select_valid_parent_language'), 'parent_id');
				return false;
			}
		}

		return true;
	}

	protected function rebuildLanguageCache()
	{
		$repo = $this->getLanguageRepo();

		\XF::runOnce('languageCacheRebuild', function() use ($repo)
		{
			$repo->rebuildLanguageCache();
		});
	}

	protected function _postSave()
	{
		if ($this->isChanged('parent_id'))
		{
			$this->getRebuildLanguageService()->rebuildFullParentList();
			$this->getRebuildPhraseService()->rebuildFullPhraseMap();

			$this->enqueueRebuild();
		}

		$this->rebuildLanguageCache();

		$cssRelated = [
			'decimal_point',
			'thousands_separator',
			'text_direction',
			'label_separator',
			'comma_separator',
			'ellipsis',
			'parenthesis_open',
			'parenthesis_close'
		];

		if ($this->isChanged($cssRelated))
		{
			// this invalidates the CSS cache so changes such LTR/RTL and various separators take effect properly
			\XF::repository('XF:Style')->updateAllStylesLastModifiedDateLater();
		}
	}

	protected function _preDelete()
	{
		$languageCount = $this->finder('XF:Language')->total();
		if ($languageCount <= 1)
		{
			$this->error(\XF::phrase('it_is_not_possible_to_delete_last_language'));
		}

		if ($this->language_id == $this->app()->options()->defaultLanguageId)
		{
			$this->error(\XF::phrase('it_is_not_possible_to_remove_default_language'));
		}
	}

	protected function _postDelete()
	{
		$id = $this->language_id;
		$db = $this->db();

		$db->delete('xf_phrase_map', 'language_id = ?', $id);
		$db->delete('xf_phrase_compiled', 'language_id = ?', $id);

		$hasChildren = (bool)$db->update('xf_language', ['parent_id' => $this->parent_id], 'parent_id = ?', $id);

		$db->update('xf_user',
			['language_id' => $this->app()->options()->defaultLanguageId],
			"language_id = ?", $id
		);

		foreach ($this->Phrases AS $phrase)
		{
			/** @var \XF\Entity\Phrase $phrase */
			$phrase->setOption('recompile', false);
			$phrase->setOption('recompile_include', false);
			$phrase->setOption('rebuild_map', false);
			$phrase->delete();
		}

		$this->deletePhraseGroupCache();
		$this->deleteCompiledTemplates();

		if ($hasChildren)
		{
			$this->getRebuildLanguageService()->rebuildFullParentList();
			$this->getRebuildPhraseService()->rebuildFullPhraseMap();

			$this->enqueueRebuild();
		}

		$this->rebuildLanguageCache();
	}

	protected function enqueueRebuild()
	{
		$this->app()->jobManager()->enqueueUnique('languageRebuild', 'XF:Atomic', [
			'execute' => ['XF:PhraseRebuild', 'XF:TemplateRebuild']
		]);
	}

	protected function deletePhraseGroupCache()
	{
		$path = 'code-cache://phrase_groups/l' . $this->language_id;
		\XF\Util\File::deleteAbstractedDirectory($path);
	}

	protected function deleteCompiledTemplates()
	{
		$path = 'code-cache://templates/l' . $this->language_id;
		\XF\Util\File::deleteAbstractedDirectory($path);
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_language';
		$structure->shortName = 'XF:Language';
		$structure->primaryKey = 'language_id';
		$structure->columns = [
			'language_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'parent_id' => ['type' => self::UINT, 'default' => 0],
			'parent_list' => ['type' => self::LIST_COMMA, 'maxLength' => 100, 'default' => []],
			'title' => ['type' => self::STR, 'maxLength' => 50,
				'required' => 'please_enter_valid_title'
			],
			'date_format' => ['type' => self::STR, 'maxLength' => 30,
				'required' => 'please_enter_valid_date_format'
			],
			'time_format' => ['type' => self::STR, 'maxLength' => 15,
				'required' => 'please_enter_valid_time_format'
			],
			'currency_format' => ['type' => self::STR, 'maxLength' => 20,
				'required' => 'please_enter_valid_currency_format'
			],
			'decimal_point' => ['type' => self::STR, 'maxLength' => 1, 'default' => '.'],
			'thousands_separator' => ['type' => self::STR, 'maxLength' => 1, 'default' => ','],
			'phrase_cache' => ['type' => self::JSON_ARRAY, 'default' => []],
			'language_code' => ['type' => self::STR, 'maxLength' => 25, 'default' => ''],
			'text_direction' => ['type' => self::STR, 'default' => 'LTR',
				'allowedValues' => ['LTR', 'RTL']
			],
			'week_start' => ['type' => self::UINT, 'max' => 6, 'default' => 0],
			'label_separator' => ['type' => self::STR, 'maxLength' => 15, 'default' => ':'],
			'comma_separator' => ['type' => self::STR, 'maxLength' => 15, 'default' => ', '],
			'ellipsis' => ['type' => self::STR, 'maxLength' => 15, 'default' => '...'],
			'parenthesis_open' => ['type' => self::STR, 'maxLength' => 15, 'default' => '('],
			'parenthesis_close' => ['type' => self::STR, 'maxLength' => 15, 'default' => ')'],
		];
		$structure->getters = [];
		$structure->relations = [
			'Parent' => [
				'entity' => 'XF:Language',
				'type' => self::TO_ONE,
				'conditions' => [
					['language_id', '=', '$parent_id']
				],
				'primary' => true
			],
			'Phrases' => [
				'entity' => 'XF:Phrase',
				'type' => self::TO_MANY,
				'conditions' => 'language_id'
			]
		];

		return $structure;
	}

	/**
	 * @return \XF\Service\Language\Rebuild
	 */
	protected function getRebuildLanguageService()
	{
		return $this->app()->service('XF:Language\Rebuild');
	}

	/**
	 * @return \XF\Service\Phrase\Rebuild
	 */
	protected function getRebuildPhraseService()
	{
		return $this->app()->service('XF:Phrase\Rebuild');
	}

	/**
	 * @return Repository\Language
	 */
	protected function getLanguageRepo()
	{
		return $this->_em->getRepository('XF:Language');
	}
}