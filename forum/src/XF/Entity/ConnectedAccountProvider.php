<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property string provider_id
 * @property string provider_class
 * @property int display_order
 * @property array options
 *
 * GETTERS
 * @property \XF\Phrase|string title
 * @property \XF\Phrase|string description
 * @property string|null icon_url
 * @property \XF\ConnectedAccount\Provider\AbstractProvider|null handler
 */
class ConnectedAccountProvider extends Entity
{
	public function isUsable()
	{
		$handler = $this->handler;
		if (!$handler)
		{
			return false;
		}
		return $handler->isUsable($this);
	}

	public function canBeTested()
	{
		$handler = $this->handler;
		if (!$handler)
		{
			return false;
		}
		return $handler->canBeTested();
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

	/**
	 * @return string|null
	 */
	public function getIconUrl()
	{
		$handler = $this->handler;
		return $handler ? $handler->getIconUrl() : null;
	}

	public function renderConfig()
	{
		$handler = $this->handler;
		return $handler ? $handler->renderConfig($this) : '';
	}


	public function renderAssociated(User $user = null)
	{
		$user = $user ?: \XF::visitor();
		$handler = $this->handler;
		return $handler ? $handler->renderAssociated($this, $user) : '';
	}

	public function isValidForRegistration()
	{
		$handler = $this->handler;
		if (!$handler)
		{
			return false;
		}
		return $handler->isValidForRegistration();
	}

	public function isAssociated(User $user)
	{
		return isset($user->Profile->connected_accounts[$this->provider_id]);
	}

	/**
	 * @return \XF\ConnectedAccount\ProviderData\AbstractProviderData|null
	 */
	public function getUserInfo($user = null)
	{
		$handler = $this->handler;
		if (!$handler)
		{
			return null;
		}
		$storageState = $handler->getStorageState($this, $user ?: \XF::visitor());
		return $handler->getProviderData($storageState);
	}

	/**
	 * @return \XF\ConnectedAccount\Provider\AbstractProvider|null
	 */
	public function getHandler()
	{
		$class = \XF::stringToClass($this->provider_class, '%s\ConnectedAccount\%s');
		if (!class_exists($class))
		{
			return null;
		}

		$class = \XF::extendClass($class);
		return new $class($this->provider_id);
	}

	protected function verifyOptions(&$options)
	{
		if (!is_array($options))
		{
			$options = [];
		}
		if (!$options)
		{
			// this is deactivating
			return true;
		}

		$handler = $this->handler;
		if ($handler && !$handler->verifyConfig($options, $error))
		{
			$this->error($error, 'options');
			return false;
		}

		return true;
	}

	protected function _postSave()
	{
		$this->rebuildConnectedAccountProviderCount();
	}

	protected function _postDelete()
	{
		$this->rebuildConnectedAccountProviderCount();
	}

	protected function rebuildConnectedAccountProviderCount()
	{
		\XF::runOnce('connectedAccountProviderCountRebuild', function()
		{
			$this->getConnectedAccountRepo()->rebuildProviderCount();
		});
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_connected_account_provider';
		$structure->shortName = 'XF:ConnectedAccountProvider';
		$structure->primaryKey = 'provider_id';
		$structure->columns = [
			'provider_id' => ['type' => self::STR, 'maxLength' => 25, 'match' => 'alphanumeric', 'required' => true],
			'provider_class' => ['type' => self::STR, 'maxLength' => 100, 'required' => true],
			'display_order' => ['type' => self::UINT, 'default' => 100],
			'options' => ['type' => self::JSON_ARRAY, 'default' => []]
		];
		$structure->getters = [
			'title' => false,
			'description' => false,
			'icon_url' => false,
			'handler' => true
		];
		$structure->relations = [];

		return $structure;
	}

	/**
	 * @return \XF\Repository\ConnectedAccount
	 */
	protected function getConnectedAccountRepo()
	{
		return $this->repository('XF:ConnectedAccount');
	}
}