<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property string provider_id
 * @property string provider_class
 * @property int priority
 * @property bool active
 * @property array options
 *
 * GETTERS
 * @property \XF\Phrase|string title
 * @property \XF\Phrase|string description
 * @property \XF\Tfa\AbstractProvider|null handler
 *
 * RELATIONS
 * @property \XF\Mvc\Entity\AbstractCollection|\XF\Entity\UserTfa[] UserEntries
 */
class TfaProvider extends Entity
{
	public function canEdit()
	{
		return \XF::$developmentMode;
	}

	/**
	 * @return \XF\Phrase|string
	 */
	public function getTitle()
	{
		$handler = $this->handler;
		return $handler ? $handler->getTitle() : '';
	}

	/**
	 * @return \XF\Phrase|string
	 */
	public function getDescription()
	{
		$handler = $this->handler;
		return $handler ? $handler->getDescription() : '';
	}

	public function isValid()
	{
		$handler = $this->handler;
		if (!$handler)
		{
			return false;
		}

		if (!$this->active)
		{
			return false;
		}

		if (!$handler->isUsable())
		{
			return false;
		}

		if (!$handler->verifyOptionsValue($this->options ?: [], $error))
		{
			return false;
		}

		return true;
	}

	public function isEnabled($userId = null)
	{
		$userId = $userId === null ? \XF::visitor()->user_id : $userId;
		return ($userId && $this->UserEntries[$userId]);
	}

	public function getUserProviderConfig($userId = null)
	{
		$userId = $userId === null ? \XF::visitor()->user_id : $userId;

		if ($userId && $this->UserEntries[$userId])
		{
			return $this->UserEntries[$userId]->provider_data;
		}
		else
		{
			return null;
		}
	}

	public function canEnable($userId = null)
	{
		$handler = $this->handler;
		if ($handler && $handler->canEnable())
		{
			return $this->isEnabled($userId) ? false : true;
		}

		return false;
	}

	public function canDisable($userId = null)
	{
		$handler = $this->handler;
		if ($handler && $handler->canDisable())
		{
			return $this->isEnabled($userId);
		}

		return false;
	}

	public function canManage($userId = null)
	{
		$handler = $this->handler;
		if ($handler && $handler->canManage())
		{
			return $this->isEnabled($userId);
		}

		return false;
	}

	public function renderOptions()
	{
		$handler = $this->handler;
		if (!$handler)
		{
			return '';
		}

		return $handler->renderOptions($this);
	}

	public function render($context, User $user, array $config, array $triggerData)
	{
		$handler = $this->handler;
		if (!$handler)
		{
			return '';
		}

		return $handler->render($context, $user, $config, $triggerData);
	}

	/**
	 * @return \XF\Tfa\AbstractProvider|null
	 */
	public function getHandler()
	{
		$class = \XF::stringToClass($this->provider_class, '%s\Tfa\%s');
		if (!class_exists($class))
		{
			return null;
		}

		$class = \XF::extendClass($class);
		return new $class($this->provider_id);
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_tfa_provider';
		$structure->shortName = 'XF:TfaProvider';
		$structure->primaryKey = 'provider_id';
		$structure->columns = [
			'provider_id' => ['type' => self::STR, 'maxLength' => 25, 'match' => 'alphanumeric', 'required' => true],
			'provider_class' => ['type' => self::STR, 'maxLength' => 100, 'required' => true],
			'priority' => ['type' => self::UINT, 'default' => 100],
			'active' => ['type' => self::BOOL, 'default' => true],
			'options' => ['type' => self::JSON_ARRAY, 'default' => []]
		];
		$structure->getters = [
			'title' => false,
			'description' => false,
			'handler' => true
		];
		$structure->relations = [
			'UserEntries' => [
				'entity' => 'XF:UserTfa',
				'type' => self::TO_MANY,
				'conditions' => 'provider_id',
				'key' => 'user_id'
			],
		];

		return $structure;
	}

	protected function getTfaRepo()
	{
		return $this->repository('XF:Tfa');
	}
}