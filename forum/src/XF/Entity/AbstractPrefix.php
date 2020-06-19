<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;
use XF\Template\Compiler\Syntax\Str;

/**
 * Class AbstractPrefix
 *
 * @package XF\Entity
 *
 * COLUMNS
 * @property int|null prefix_id
 * @property int prefix_group_id
 * @property int display_order
 * @property int materialized_order
 * @property string css_class
 * @property array allowed_user_group_ids
 *
 * GETTERS
 * @property \XF\Phrase|string title
 *
 * RELATIONS
 * @property \XF\Phrase MasterTitle
 * @property \XF\Entity\AbstractPrefixGroup PrefixGroup
 */
abstract class AbstractPrefix extends Entity
{
	abstract protected function getClassIdentifier();

	protected static function getContentType()
	{
		throw new \LogicException('The phrase group must be overridden.');
	}

	public function isUsableByUser(\XF\Entity\User $user = null)
	{
		$user = $user ?: \XF::visitor();

		foreach ($this->allowed_user_group_ids AS $userGroupId)
		{
			if ($userGroupId == -1 || $user->isMemberOf($userGroupId))
			{
				return true;
			}
		}

		return false;
	}

	public function getPhraseName()
	{
		return static::getContentType() . '_prefix.' . $this->prefix_id;
	}

	/**
	 * @return \XF\Phrase|string
	 */
	public function getTitle()
	{
		return $this->prefix_id ? \XF::phrase($this->getPhraseName(), [], false) : '';
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
		$this->rebuildPrefixCaches();
	}

	protected function _postDelete()
	{
		if ($this->MasterTitle)
		{
			$this->MasterTitle->delete();
		}

		$this->rebuildPrefixCaches();
	}

	protected function rebuildPrefixCaches()
	{
		$repo = $this->getPrefixRepo();

		\XF::runOnce($this->getContentType() . 'PrefixCaches', function() use ($repo)
		{
			$repo->rebuildPrefixMaterializedOrder();
			$repo->rebuildPrefixCache();
		});
	}

	/**
	 * @param \XF\Api\Result\EntityResult $result
	 * @param int $verbosity
	 * @param array $options
	 *
	 * @api-out int $prefix_id
	 * @api-out str $title
	 * @api-out bool $is_usable True if the acting user can use (select) this prefix.
	 * @api-out int $prefix_group_id
	 * @api-out int $display_order
	 * @api-out int $materialized_order Effective order, taking group ordering into account.
	 */
	protected function setupApiResultData(
		\XF\Api\Result\EntityResult $result, $verbosity = self::VERBOSITY_NORMAL, array $options = []
	)
	{
		$result->title = $this->title;
		$result->is_usable = $this->isUsableByUser();
	}

	protected static function setupDefaultStructure(Structure $structure, $table, $shortName)
	{
		$structure->table = $table;
		$structure->shortName = $shortName;
		$structure->primaryKey = 'prefix_id';
		$structure->columns = [
			'prefix_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true, 'api' => true],
			'prefix_group_id' => ['type' => self::UINT, 'default' => 0, 'api' => true],
			'display_order' => ['type' => self::UINT, 'forced' => true, 'default' => 1, 'api' => true],
			'materialized_order' => ['type' => self::UINT, 'forced' => true, 'default' => 0, 'api' => true],
			'css_class' => ['type' => self::STR, 'maxLength' => 50, 'default' => 'label label--primary'],
			'allowed_user_group_ids' => ['type' => self::LIST_COMMA, 'default' => [-1]]
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
					['title', '=', static::getContentType() . '_prefix.', '$prefix_id']
				]
			],
			'PrefixGroup' => [
				'entity' => $shortName . 'Group',
				'type' => self::TO_ONE,
				'conditions' => 'prefix_group_id',
				'primary' => true
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