<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null warning_id
 * @property string content_type
 * @property int content_id
 * @property string content_title
 * @property int user_id
 * @property int warning_date
 * @property int warning_user_id
 * @property int warning_definition_id
 * @property string title
 * @property string notes
 * @property int points
 * @property int expiry_date
 * @property bool is_expired
 * @property array extra_user_group_ids
 *
 * GETTERS
 * @property Entity|null Content
 * @property \XF\Phrase|string display_title
 *
 * RELATIONS
 * @property \XF\Entity\User User
 * @property \XF\Entity\User WarnedBy
 * @property \XF\Entity\WarningDefinition Definition
 */
class Warning extends Entity
{
	public function canView(&$error = null)
	{
		return \XF::visitor()->canViewWarnings();
	}

	public function canDelete(&$error = null)
	{
		$visitor = \XF::visitor();
		if (!$visitor->user_id)
		{
			return false;
		}
		if ($this->warning_user_id == $visitor->user_id)
		{
			return true;
		}

		return $visitor->hasPermission('general', 'manageWarning');
	}

	public function canEditExpiry(&$error = null)
	{
		$visitor = \XF::visitor();
		if (!$visitor->user_id)
		{
			return false;
		}
		if ($this->is_expired)
		{
			return false;
		}
		if ($this->warning_user_id == $visitor->user_id)
		{
			return true;
		}

		return $visitor->hasPermission('general', 'manageWarning');
	}

	public function getHandler()
	{
		return $this->getWarningRepo()->getWarningHandler($this->content_type);
	}

	/**
	 * @return Entity|null
	 */
	public function getContent()
	{
		$handler = $this->getHandler();
		return $handler ? $handler->getContent($this->content_id) : null;
	}

	public function setContent(Entity $content = null)
	{
		$this->_getterCache['Content'] = $content;
	}

	/**
	 * @return \XF\Phrase|string
	 */
	public function getDisplayTitle()
	{
		$handler = $this->getHandler();
		return $handler ? $handler->getDisplayTitle($this->content_title) : $this->content_title;
	}

	protected function _preSave()
	{
		if ($this->isUpdate() && $this->isChanged(['points', 'extra_user_group_ids']))
		{
			throw new \LogicException("Cannot change points/groups after creation");
		}

		if ($this->expiry_date && $this->expiry_date <= \XF::$time)
		{
			$this->is_expired = true;
		}
	}

	protected function _postSave()
	{
		$content = $this->Content;
		if ($this->isInsert())
		{
			if ($content)
			{
				$this->getHandler()->onWarning($content, $this);
			}
		}

		if (!$this->is_expired && ($this->isInsert() || $this->getExistingValue('is_expired')))
		{
			// new and unexpired or from expired to unexpired
			$this->onApplication();

			if ($content)
			{
				if ($this->getOption('log_moderator'))
				{
					$this->app()->logger()->logModeratorAction($this->content_type, $content, 'warning_given', [], false);
				}
			}
		}
		else if ($this->isUpdate() && $this->is_expired && !$this->getExistingValue('is_expired'))
		{
			// moved from unexpired to expired
			$this->onExpiration(false);

			if ($content)
			{
				if ($this->getOption('log_moderator'))
				{
					$this->app()->logger()->logModeratorAction($this->content_type, $content, 'warning_expired', [], false);
				}
			}
		}
	}

	protected function _postDelete()
	{
		$content = $this->Content;
		if ($content)
		{
			$this->getHandler()->onWarningRemoval($content, $this);

			if ($this->getOption('log_moderator'))
			{
				$this->app()->logger()->logModeratorAction($this->content_type, $content, 'warning_removed', [], false);
			}
		}

		if (!$this->is_expired)
		{
			$this->onExpiration(true);
		}
	}

	protected function onApplication()
	{
		if ($this->extra_user_group_ids)
		{
			$this->getUserGroupChangeService()->addUserGroupChange(
				$this->user_id, 'warning_' . $this->warning_id, $this->extra_user_group_ids
			);
		}

		if ($this->points && $this->User)
		{
			$this->updateUserWarningPoints($this->User, $this->points);
		}
	}

	protected function onExpiration($isDelete)
	{
		if ($this->extra_user_group_ids)
		{
			$this->getUserGroupChangeService()->removeUserGroupChange(
				$this->user_id, 'warning_' . $this->warning_id
			);
		}

		if ($this->points && $this->User)
		{
			$this->updateUserWarningPoints($this->User, -$this->points, $isDelete);
		}
	}

	protected function updateUserWarningPoints(User $user, $adjustment, $isDelete = false)
	{
		if (!$adjustment)
		{
			return;
		}

		/** @var \XF\Service\User\WarningPointsChange $changeService */
		$changeService = $this->app()->service('XF:User\WarningPointsChange', $user);
		$changeService->shiftPoints($adjustment, $isDelete);
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_warning';
		$structure->shortName = 'XF:Warning';
		$structure->primaryKey = 'warning_id';
		$structure->columns = [
			'warning_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'content_type' => ['type' => self::STR, 'maxLength' => 25, 'required' => true],
			'content_id' => ['type' => self::UINT, 'required' => true],
			'content_title' => ['type' => self::STR, 'maxLength' => 255, 'default' => ''],
			'user_id' => ['type' => self::UINT, 'required' => true],
			'warning_date' => ['type' => self::UINT, 'default' => \XF::$time],
			'warning_user_id' => ['type' => self::UINT, 'required' => true],
			'warning_definition_id' => ['type' => self::UINT, 'required' => true],
			'title' => ['type' => self::STR, 'maxLength' => 255, 'forced' => true,
				'required' => 'please_enter_valid_title'
			],
			'notes' => ['type' => self::STR, 'default' => ''],
			'points' => ['type' => self::UINT, 'max' => 65535, 'required' => true],
			'expiry_date' => ['type' => self::UINT, 'default' => 0],
			'is_expired' => ['type' => self::BOOL, 'default' => false],
			'extra_user_group_ids' => ['type' => self::LIST_COMMA, 'default' => [],
				'list' => ['type' => 'posint', 'unique' => true, 'sort' => SORT_NUMERIC]
			],
		];
		$structure->getters = [
			'Content' => true,
			'display_title' => true,
		];
		$structure->relations = [
			'User' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => 'user_id',
				'primary' => true
			],
			'WarnedBy' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => [['user_id', '=', '$warning_user_id']],
				'primary' => true
			],
			'Definition' => [
				'entity' => 'XF:WarningDefinition',
				'type' => self::TO_ONE,
				'conditions' => 'warning_definition_id',
				'primary' => true
			],
		];
		$structure->options = [
			'log_moderator' => true
		];

		return $structure;
	}

	/**
	 * @return \XF\Service\User\UserGroupChange
	 */
	protected function getUserGroupChangeService()
	{
		return $this->app()->service('XF:User\UserGroupChange');
	}

	/**
	 * @return \XF\Repository\Warning
	 */
	protected function getWarningRepo()
	{
		return $this->repository('XF:Warning');
	}
}