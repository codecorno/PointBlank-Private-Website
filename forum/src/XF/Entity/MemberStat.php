<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null member_stat_id
 * @property string member_stat_key
 * @property array|null criteria
 * @property string callback_class
 * @property string callback_method
 * @property string sort_order
 * @property string sort_direction
 * @property string permission_limit_
 * @property bool show_value
 * @property int user_limit
 * @property int display_order
 * @property string addon_id
 * @property bool overview_display
 * @property bool active
 * @property int cache_lifetime
 * @property int cache_expiry
 * @property array|null cache_results
 *
 * GETTERS
 * @property \XF\Phrase title
 * @property \XF\Service\MemberStat\Preparer preparer
 * @property array|null permission_limit
 * @property array|null Results
 *
 * RELATIONS
 * @property \XF\Entity\Phrase MasterTitle
 * @property \XF\Entity\AddOn AddOn
 */
class MemberStat extends Entity
{
	public function canView()
	{
		$permLimit = $this->permission_limit;

		if ($permLimit && !\XF::visitor()->hasPermission($permLimit['permission_group_id'], $permLimit['permission_id']))
		{
			return false;
		}

		return $this->active;
	}

	public function canEdit()
	{
		if (!$this->addon_id || $this->isInsert())
		{
			return true;
		}
		else
		{
			return \XF::$developmentMode;
		}
	}

	/**
	 * @return \XF\Phrase
	 */
	public function getTitle()
	{
		return \XF::phrase($this->getPhraseName());
	}

	/**
	 * @return \XF\Service\MemberStat\Preparer
	 */
	public function getPreparer()
	{
		return $this->app()->service('XF:MemberStat\Preparer', $this);
	}

	/**
	 * @return array|null
	 */
	public function getPermissionLimit()
	{
		if (!$this->permission_limit_)
		{
			return null;
		}

		list ($groupId, $permId) = explode('-', $this->permission_limit_, 2);
		return [
			'permission_group_id' => $groupId,
			'permission_id' => $permId
		];
	}

	/**
	 * @param bool $forOverview
	 *
	 * @return array|null
	 */
	public function getResults($forOverview = false)
	{
		$preparer = $this->preparer;
		$this->setResults($preparer->getResults($forOverview));

		return $this->_getterCache['Results'];
	}

	public function setResults(array $results)
	{
		$this->_getterCache['Results'] = $results;
	}

	public function getPhraseName()
	{
		return 'member_stat.' . $this->member_stat_key;
	}

	public function getMasterPhrase()
	{
		$phrase = $this->MasterTitle;
		if (!$phrase)
		{
			$phrase = $this->_em->create('XF:Phrase');
			$phrase->title = $this->_getDeferredValue(function() { return $this->getTitle(); });
			$phrase->language_id = 0;
			$phrase->addon_id = $this->_getDeferredValue(function() { return $this->addon_id; });
		}

		return $phrase;
	}

	protected function verifyCriteria(&$criteria)
	{
		if (!$criteria)
		{
			return true;
		}

		if (!empty($criteria['user_field']))
		{
			if (!is_array($criteria['user_field']))
			{
				unset($criteria['user_field']);
			}
			else
			{
				foreach ($criteria['user_field'] AS $k => $v)
				{
					if ($v === '' || (is_array($v) && !$v))
					{
						unset($criteria['user_field'][$k]);
					}
				}
				if (!$criteria['user_field'])
				{
					unset($criteria['user_field']);
				}
			}
		}

		return true;
	}

	protected function _preSave()
	{
		if ($this->callback_class || $this->callback_method)
		{
			if (!\XF\Util\Php::validateCallbackPhrased($this->callback_class, $this->callback_method, $error))
			{
				$this->error($error, 'callback_method');
			}
		}

		if ($this->isUpdate()
			&& $this->isChanged(['criteria', 'sort_order', 'sort_direction', 'callback_class', 'callback_method', 'show_value', 'cache_lifetime', 'user_limit'])
		)
		{
			// Invalidate cache so that these changes can take effect immediately
			$this->cache_expiry = 0;
			$this->cache_results = null;
		}
	}

	protected function _postSave()
	{
		if ($this->isUpdate())
		{
			if ($this->isChanged(['addon_id', 'member_stat_key']))
			{
				/** @var Phrase $phrase */
				$phrase = $this->getExistingRelation('MasterTitle');
				if ($phrase)
				{
					$writeDevOutput = $this->getBehavior('XF:DevOutputWritable')->getOption('write_dev_output');
					$phrase->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', $writeDevOutput);

					$phrase->addon_id = $this->addon_id;
					$phrase->title = $this->getPhraseName();
					$phrase->save();
				}
			}
		}
	}

	protected function _postDelete()
	{
		$phrase = $this->MasterTitle;
		if ($phrase)
		{
			$writeDevOutput = $this->getBehavior('XF:DevOutputWritable')->getOption('write_dev_output');
			$phrase->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', $writeDevOutput);

			$phrase->delete();
		}
	}

	protected function _setupDefaults()
	{
		/** @var \XF\Repository\AddOn $addOnRepo */
		$addOnRepo = $this->_em->getRepository('XF:AddOn');
		$this->addon_id = $addOnRepo->getDefaultAddOnId();
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_member_stat';
		$structure->shortName = 'XF:MemberStat';
		$structure->primaryKey = 'member_stat_id';
		$structure->columns = [
			'member_stat_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'member_stat_key' => ['type' => self::STR, 'maxLength' => 50,
				'required' => 'please_enter_valid_member_stat_key',
				'unique' => 'member_stat_keys_must_be_unique',
				'match' => 'alphanumeric'
			],
			'criteria' => ['type' => self::JSON_ARRAY, 'nullable' => true],
			'callback_class' => ['type' => self::STR, 'maxLength' => 100, 'default' => ''],
			'callback_method' => ['type' => self::STR, 'maxLength' => 75, 'default' => ''],
			'sort_order' => ['type' => self::STR, 'maxLength' => 50, 'default' => 'message_count'],
			'sort_direction' => ['type' => self::STR, 'maxLength' => 5, 'default' => 'desc',
				'allowedValues' => ['asc', 'desc']
			],
			'permission_limit' => ['type' => self::STR, 'maxLength' => 51, 'default' => ''],
			'show_value' => ['type' => self::BOOL, 'default' => true],
			'user_limit' => ['type' => self::UINT, 'default' => 20],
			'display_order' => ['type' => self::UINT, 'default' => 10],
			'addon_id' => ['type' => self::BINARY, 'maxLength' => 50, 'default' => ''],
			'overview_display' => ['type' => self::BOOL, 'default' => true],
			'active' => ['type' => self::BOOL, 'default' => true],
			'cache_lifetime' => ['type' => self::UINT, 'default' => 60],
			'cache_expiry' => ['type' => self::UINT, 'default' => 0],
			'cache_results' => ['type' => self::JSON_ARRAY, 'default' => null, 'nullable' => true]
		];
		$structure->behaviors = [
			'XF:DevOutputWritable' => []
		];
		$structure->getters = [
			'title' => true,
			'preparer' => true,
			'permission_limit' => true,
			'Results' => true
		];
		$structure->relations = [
			'MasterTitle' => [
				'entity' => 'XF:Phrase',
				'type' => self::TO_ONE,
				'conditions' => [
					['language_id', '=', 0],
					['title', '=', 'member_stat.', '$member_stat_key']
				]
			],
			'AddOn' => [
				'entity' => 'XF:AddOn',
				'type' => self::TO_ONE,
				'conditions' => 'addon_id',
				'primary' => true
			]
		];
		$structure->options = [];

		return $structure;
	}
}