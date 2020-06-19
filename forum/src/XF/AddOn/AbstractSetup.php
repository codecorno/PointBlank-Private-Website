<?php

namespace XF\AddOn;

use XF\Install\InstallHelperTrait;

abstract class AbstractSetup
{
	use InstallHelperTrait;

	/**
	 * @var \XF\AddOn\AddOn
	 */
	protected $addOn;

	/**
	 * @param array $stepParams
	 *
	 * @return null|StepResult
	 */
	abstract public function install(array $stepParams = []);

	/**
	 * @param array $stepParams
	 *
	 * @return null|StepResult
	 */
	abstract public function upgrade(array $stepParams = []);

	/**
	 * @param array $stepParams
	 *
	 * @return null|StepResult
	 */
	abstract public function uninstall(array $stepParams = []);

	public function __construct(\XF\AddOn\AddOn $addOn, \XF\App $app)
	{
		$this->addOn = $addOn;
		$this->app = $app;
	}

	/**
	 * Perform additional requirement checks.
	 *
	 * @param array $errors Errors will block the setup from continuing
	 * @param array $warnings Warnings will be displayed but allow the user to continue setup
	 *
	 * @return void
	 */
	public function checkRequirements(&$errors = [], &$warnings = [])
	{
		return;
	}

	public function postInstall(array &$stateChanges)
	{
	}

	public function postUpgrade($previousVersion, array &$stateChanges)
	{
	}

	public function onActiveChange($newActive, array &$jobList)
	{
	}

	public function prepareForAction($action)
	{
		if ($action == 'uninstall')
		{
			\XF::db()->ignoreLegacyTableWriteError(true);
		}
	}

	/**
	 * @param $sql
	 * @param array $bind
	 * @param bool $suppressAll
	 *
	 * @return bool|\XF\Db\AbstractStatement
	 * @throws \XF\Db\Exception
	 */
	protected function query($sql, $bind = [], $suppressAll = false)
	{
		return $this->executeUpgradeQuery($sql, $bind, $suppressAll);
	}
}