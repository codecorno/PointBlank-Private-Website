<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null promotion_id
 * @property string title
 * @property bool active
 * @property array user_criteria
 * @property array extra_user_group_ids
 */
class UserGroupPromotion extends Entity
{
	public function promote(\XF\Entity\User $user, $state = 'automatic')
	{
		if (!$this->active)
		{
			return;
		}

		if ($state != 'disabled')
		{
			$this->getUserGroupChangeService()->addUserGroupChange($user->user_id, 'ugPromotion' . $this->promotion_id, $this->extra_user_group_ids);
		}

		$userGroupPromotionLog = $this->finder('XF:UserGroupPromotionLog')
			->where('promotion_id', $this->promotion_id)
			->where('user_id', $user->user_id)->fetchOne();

		if (!$userGroupPromotionLog)
		{
			/** @var \XF\Entity\UserGroupPromotionLog $userGroupPromotionLog */
			$userGroupPromotionLog = $this->em()->create('XF:UserGroupPromotionLog');
			$userGroupPromotionLog->promotion_id = $this->promotion_id;
			$userGroupPromotionLog->user_id = $user->user_id;
		}

		$userGroupPromotionLog->promotion_date = \XF::$time;
		$userGroupPromotionLog->promotion_state = $state;
		$userGroupPromotionLog->save();
	}

	public function demote(\XF\Entity\User $user, $disable = false)
	{
		$this->getUserGroupChangeService()->removeUserGroupChange($user->user_id, 'ugPromotion' . $this->promotion_id);

		if ($disable)
		{
			$this->promote($user, 'disabled'); // Log a promotion as disabled. Never re-add it.
		}
		else
		{
			$userGroupPromotionLog = $this->finder('XF:UserGroupPromotionLog')
				->where('promotion_id', $this->promotion_id)
				->where('user_id', $user->user_id)->fetchOne();

			if ($userGroupPromotionLog)
			{
				$userGroupPromotionLog->delete(false); // Remove promotion log. Allow it to be re-added.
			}
		}
	}

	protected function verifyUserCriteria(&$criteria)
	{
		$userCriteria = $this->app()->criteria('XF:User', $criteria);
		$criteria = $userCriteria->getCriteria();
		return true;
	}

	protected function _postDelete()
	{
		// TODO: this doesn't demote users, possibly make that an option?
		$this->db()->delete('xf_user_group_promotion_log',
			'promotion_id = ?', $this->promotion_id
		);

		$this->getUserGroupChangeService()->removeUserGroupChangeLogByKey('ugPromotion' . $this->promotion_id);
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_user_group_promotion';
		$structure->shortName = 'XF:UserGroupPromotion';
		$structure->primaryKey = 'promotion_id';
		$structure->columns = [
			'promotion_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'title' => ['type' => self::STR, 'maxLength' => 100,
				'required' => 'please_enter_valid_title'
			],
			'active' => ['type' => self::BOOL, 'default' => true],
			'user_criteria' => ['type' => self::JSON_ARRAY, 'default' => [],
				'required' => 'please_select_criteria_that_must_be_met'
			],
			'extra_user_group_ids' => ['type' => self::LIST_COMMA, 'default' => [],
				'list' => ['type' => 'posint', 'unique' => true, 'sort' => SORT_NUMERIC],
				'required' => 'please_select_at_least_one_user_group'
			],
		];
		$structure->getters = [];
		$structure->relations = [];

		return $structure;
	}

	/**
	 * @return \XF\Service\User\UserGroupChange
	 */
	protected function getUserGroupChangeService()
	{
		$userGroupChangeService = $this->app()->service('XF:User\UserGroupChange');
		return $userGroupChangeService;
	}
}