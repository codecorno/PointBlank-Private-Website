<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null tag_id
 * @property string tag
 * @property string tag_url
 * @property int use_count
 * @property int last_use_date
 * @property bool permanent
 */
class Tag extends Entity
{
	protected function verifyTag(&$tag)
	{
		$tagRepo = $this->getTagRepo();
		$tag = $tagRepo->normalizeTag($tag);

		if ($this->getOption('admin_edit'))
		{
			$isValid = strlen($tag) > 0;
		}
		else
		{
			$isValid = strlen($tag) && $tagRepo->isValidTag($tag);
		}

		if (!$isValid)
		{
			$this->error(\XF::phrase('please_enter_valid_tag_name'), 'tag');
			return false;
		}

		return true;
	}

	protected function _preSave()
	{
		if (!$this->tag_url)
		{
			$this->setTrusted('tag_url', $this->getTagRepo()->generateTagUrlVersion($this->tag));
		}
	}

	protected function _postSave()
	{
		if ($this->isUpdate() && $this->isChanged(['tag', 'tag_url']))
		{
			$this->app()->jobManager()->enqueueUnique('tagUpdate' . $this->tag_id, 'XF:TagRecache', [
				'tagId' => $this->tag_id
			]);
		}
	}

	protected function _postDelete()
	{
		$hasContent = $this->db()->fetchOne("
			SELECT 1
			FROM xf_tag_content
			WHERE tag_id = ?
			LIMIT 1
		", $this->tag_id);

		if ($hasContent)
		{
			$this->app()->jobManager()->enqueueUnique('tagDelete' . $this->tag_id, 'XF:TagRecache', [
				'tagId' => $this->tag_id,
				'deleteFirst' => true
			]);
		}
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_tag';
		$structure->shortName = 'XF:Tag';
		$structure->primaryKey = 'tag_id';
		$structure->columns = [
			'tag_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'tag' => ['type' => self::STR, 'required' => true, 'maxLength' => 100,
				'unique' => 'tags_must_be_unique'
			],
			'tag_url' => ['type' => self::STR, 'required' => true, 'maxLength' => 100,
				'unique' => 'tag_url_versions_must_be_unique',
				'match' => 'alphanumeric_hyphen'
			],
			'use_count' => ['type' => self::UINT, 'forced' => true, 'default' => 0],
			'last_use_date' => ['type' => self::UINT, 'default' => 0],
			'permanent' => ['type' => self::BOOL, 'default' => false],
		];
		$structure->getters = [];
		$structure->relations = [];
		$structure->options = [
			'admin_edit' => false
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