<?php

namespace XF\Tfa;

use XF\Entity\TfaProvider;

abstract class AbstractProvider
{
	protected $providerId;

	protected $bypassFailedAttemptLog = false;

	abstract public function generateInitialData(\XF\Entity\User $user, array $config = []);
	abstract public function trigger($context, \XF\Entity\User $user, array &$config, \XF\Http\Request $request);
	abstract public function render($context, \XF\Entity\User $user, array $config, array $triggerData);
	abstract public function verify($context, \XF\Entity\User $user, array &$config, \XF\Http\Request $request);

	public function __construct($providerId)
	{
		$this->providerId = $providerId;
	}

	public function getBypassFailedAttemptLog()
	{
		return $this->bypassFailedAttemptLog;
	}

	public function getTitle()
	{
		return \XF::phrase('tfa.' . $this->providerId);
	}

	public function getDescription()
	{
		return \XF::phrase('tfa_desc.' . $this->providerId);
	}

	public function renderOptions(TfaProvider $provider)
	{
		return '';
	}

	public function verifyOptions(\XF\Http\Request $request, array &$options, &$error = null)
	{
		return true;
	}

	public function verifyOptionsValue(array $options, &$error = null)
	{
		$optionsRequest = new \XF\Http\Request(
			\XF::app()->inputFilterer(), $options
		);
		return $this->verifyOptions($optionsRequest, $options, $error);
	}

	public function isUsable()
	{
		return true;
	}

	public function canEnable()
	{
		return true;
	}

	public function meetsRequirements(\XF\Entity\User $user, &$error)
	{
		return true;
	}

	public function canDisable()
	{
		return true;
	}

	public function canManage()
	{
		return false;
	}

	public function handleManage(
		\XF\Mvc\Controller $controller, \XF\Entity\TfaProvider $provider, \XF\Entity\User $user, array $config
	)
	{
		return null;
	}

	public function requiresConfig()
	{
		return false;
	}

	public function handleConfig(
		\XF\Mvc\Controller $controller, \XF\Entity\TfaProvider $provider, \XF\Entity\User $user, array &$config
	)
	{
		return null;
	}

	public function getProviderId()
	{
		return $this->providerId;
	}

	/**
	 * @return null|\XF\Mvc\Entity\Entity|TfaProvider
	 */
	public function getProvider()
	{
		return \XF::em()->find('XF:TfaProvider', $this->providerId);
	}
}