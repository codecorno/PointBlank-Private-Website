<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * Class AbstractPrefixGroup
 *
 * @package XF\Entity
 *
 * COLUMNS
 * @property int|null prefix_group_id
 * @property int display_order
 *
 * GETTERS
 * @property \XF\Phrase|string title
 *
 * RELATIONS
 * @property \XF\Phrase MasterTitle
 * @property \XF\Entity\AbstractPrefix[] Prefixes
 */
abstract class AbstractPrefixGroup extends Entity
{
	abstract protected function getClassIdentifier();

	protected static function getContentType()
	{
		throw new \LogicException('The content type must be overridden.');
	}

	public function getPhraseName()
	{
		return static::getContentType() . '_prefix_group.' . $this->prefix_group_id;
	}

	/**
	 * @return \XF\Phrase|string
	 */
	public function getTitle()
	{
		return $this->prefix_group_id ? \XF::phrase($this->getPhraseName(), [], false) : '';
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

	protected function _postSave()
	{
		if ($this->isChanged('display_order'))
		{
			$this->rebuildPrefixCaches();
		}
	}

	protected function _postDelete()
	{
		if ($this->MasterTitle)
		{
			$this->MasterTitle->delete();
		}

		if ($this->Prefixes)
		{
			foreach ($this->Prefixes AS $prefix)
			{
				$prefix->prefix_group_id = 0;
				$prefix->save();
			}
		}

		$this->rebuildPrefixCaches();
	}

	protected function rebuildPrefixCaches()
	{
		$repo = $this->getPrefixRepo();

		\XF::runOnce($this->getContentType() . 'PrefixGroupCaches', function() use ($repo)
		{
			$repo->rebuildPrefixMaterializedOrder();
			$repo->rebuildPrefixCache();
		});
	}

	protected static function setupDefaultStructure(Structure $structure, $table, $shortName, $prefixShortName)
	{
		$structure->table = $table;
		$structure->shortName = $shortName;
		$structure->primaryKey = 'prefix_group_id';
		$structure->columns = [
			'prefix_group_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'display_order' => ['type' => self::UINT, 'forced' => true, 'default' => 1]
		];
		$structure->getters = [
			'title' => true
		];
		$contentType = static::getContentType();
		$structure->relations = [
			'MasterTitle' => [
				'entity' => 'XF:Phrase',
				'type' => self::TO_ONE,
				'conditions' => [
					['language_id', '=', 0],
					['title', '=', $contentType . '_prefix_group.', '$prefix_group_id']
				]
			],
			'Prefixes' => [
				'entity' => $prefixShortName,
				'type' => self::TO_MANY,
				'conditions' => 'prefix_group_id'
			]
		];
	}

	/**
	 * @return \XF\Repository\AbstractPrefix
	 */
	protected function getPrefixRepo()
	{
		return $this->repository($this->getClassIdentifier());
	}
}