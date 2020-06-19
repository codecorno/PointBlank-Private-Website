<?php

namespace XF\Install\Controller;

use XF\Mvc\ParameterBag;
use XF\Mvc\Reply\AbstractReply;
use XF\Mvc\RouteMatch;

class Upgrade extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		if (!$this->getInstallHelper()->isInstalled())
		{
			throw $this->exception($this->redirect('index.php?install/'));
		}

		if (method_exists($this->app, 'setupUpgradeSession'))
		{
			$this->app->setupUpgradeSession();
		}

		if (strtolower($action) !== 'login')
		{
			$visitor = \XF::visitor();
			if (!$visitor->is_admin)
			{
				throw $this->exception($this->rerouteController(__CLASS__, 'login'));
			}
		}
	}

	public function actionIndex()
	{
		\XF\Util\Php::resetOpcache();

		$installHelper = $this->getInstallHelper();
		$upgrader = $this->getUpgrader();

		if ($upgrader->getNewestUpgradeVersionId() > \XF::$versionId)
		{
			return $this->error(\XF::phrase('upgrade_found_newer_than_version'));
		}

		if ($upgrader->isUpgradeComplete() && $this->options()->currentVersionId >= \XF::$versionId)
		{
			return $this->view('XF:Upgrade\Current', 'upgrade_current');
		}

		$xfAddOn = new \XF\AddOn\AddOn('XF', \XF::app()->addOnManager());
		$xfAddOn->passesHealthCheck($missing, $inconsistent);

		$db = $this->app->db();

		$needMigration = $this->app->config('legacyExists');

		$currentVersion = $upgrader->getCurrentVersion();

		$viewParams = [
			'errors' => $installHelper->getRequirementErrors($db),
			'warnings' => $installHelper->getRequirementWarnings($db),
			'currentVersion' => $currentVersion,
			'isSignificantUpgrade' => $upgrader->isSignificantUpgrade(),
			'isCliRecommended' => $upgrader->isCliRecommended(),
			'addOnConflicts' => $upgrader->getAddOnConflicts($currentVersion),
			'fileErrors' => array_merge($missing, $inconsistent),
			'hasHashes' => $xfAddOn->hasHashes(),
			'needsConfigMigration' => $needMigration
		];
		return $this->view('XF:Upgrade\Start', 'upgrade_start', $viewParams);
	}

	public function actionMigrateConfig()
	{
		$this->assertPostOnly();

		if ($this->app->config('exists') || !$this->app->config('legacyExists'))
		{
			return $this->redirect('index.php?upgrade/');
		}

		$installHelper = $this->getUpgrader();

		$config = $installHelper->migrateLegacyConfigIfNeeded($written);

		$viewParams = [
			'config' => $config,
			'written' => $written,
			'configFile' => $this->app->container('config.file')
		];
		return $this->view('XF:Upgrade\MigrateConfig', 'upgrade_migrate_config', $viewParams);
	}

	public function actionRun()
	{
		$this->assertPostOnly();

		if (!$this->app->config('exists'))
		{
			return $this->error(\XF::phrase('config_file_x_could_not_be_found', [
				'file' => $this->app->container('config.file')
			]));
		}

		$upgrader = $this->getUpgrader();
		$upgrader->syncUpgradeLogStructure();

		$lastUpgradeVersion = $upgrader->getLatestUpgradeVersion();

		if ($lastUpgradeVersion['version_id'] === \XF::$versionId && !$lastUpgradeVersion['last_step'])
		{
			return $this->actionRebuild();
		}

		$input = $this->filter([
			'run_version' => 'uint',
			'step' => 'str',
			'position' => 'uint',
			'step_data' => 'json-array'
		]);

		if (!$input['run_version'])
		{
			if ($lastUpgradeVersion['last_step'])
			{
				// pick up from the next step
				$input['run_version'] = $lastUpgradeVersion['version_id'];
				$input['step'] = $lastUpgradeVersion['last_step'] + 1;
			}
			else
			{
				// last upgrade is complete, pick up from the next upgrade
				$input['run_version'] = $upgrader->getNextUpgradeVersionId($lastUpgradeVersion['version_id']);
				$input['step'] = 1;
			}

			// starting a new step (or upgrade script), ignore these
			$input['position'] = 0;
			$input['step_data'] = [];

			if ($input['run_version'])
			{
				if ($input['run_version'] > \XF::$versionId)
				{
					return $this->error(\XF::phrase('upgrade_found_newer_than_version'));
				}

				$upgrade = $upgrader->getUpgrade($input['run_version']);
			}
			else
			{
				$upgrade = false;
			}
		}
		else
		{
			$upgrade = $upgrader->getUpgrade($input['run_version']);
		}

		if (!$upgrade)
		{
			$upgrader->insertUpgradeLog(\XF::$versionId);
			return $this->actionRebuild();
		}

		if (!$input['step'])
		{
			$input['step'] = 1;
		}

		if (method_exists($upgrade, 'step' . $input['step']))
		{
			$result = $upgrade->{'step' . $input['step']}($input['position'], $input['step_data'], $this);
		}
		else
		{
			$result = 'complete';
		}

		if ($result instanceof AbstractReply)
		{
			return $result;
		}

		$stepMessage = '';
		$stepData = false;

		if ($result === 'complete')
		{
			$upgrader->insertUpgradeLog($input['run_version']);

			$viewParams = [
				'newRunVersion' => '',
				'newStep' => '',
				'versionName' => $upgrade->getVersionName(),
				'step' => $input['step']
			];
		}
		else
		{
			if ($result === true || $result === null)
			{
				// step finished
				$upgrader->insertUpgradeLog($input['run_version'], $input['step']);

				$result = $input['step'] + 1;
				$input['position'] = 0;
				$stepData = [];
			}
			else if (is_array($result))
			{
				// step not finished, don't log anything yet
				$input['position'] = $result[0];
				$stepMessage = $result[1];
				if (!empty($result[2]))
				{
					$stepData = $result[2];
				}

				$result = $input['step']; // stay on same step
			}

			$viewParams = [
				'newRunVersion' => $input['run_version'],
				'newStep' => $result,
				'position' => $input['position'],
				'stepMessage' => $stepMessage,
				'stepData' => $stepData,
				'versionName' => $upgrade->getVersionName(),
				'step' => $input['step']
			];
		}

		return $this->view('XF:Upgrade\Run', 'upgrade_run', $viewParams);
	}

	public function actionRebuild()
	{
		$upgrader = $this->getUpgrader();

		$currentVersion = $upgrader->getCurrentVersion();
		$extraJobs = $upgrader->getExtraUpgradeJobsMap();
		$this->getInstallHelper()->insertRebuildJob(null, $extraJobs, true, $currentVersion);

		return $this->rerouteController(__CLASS__, 'runJob');
	}

	public function actionRunJob()
	{
		$redirect = 'index.php?upgrade/complete';

		if (empty($this->options()->collectServerStats['configured']))
		{
			$redirect = 'index.php?upgrade/options';
		}

		$output = $this->manualJobRunner('index.php?upgrade/run-job', $redirect);

		if ($output instanceof \XF\Mvc\Reply\Redirect)
		{
			// if this still exists, it will have a future date which means the process got interrupted
			if ($this->getInstallHelper()->hasRebuildJobPending())
			{
				return $this->view('XF:Upgrade\RebuildErrors', 'upgrade_rebuild_errors');
			}

			$upgrader = $this->getUpgrader();
			if ($upgrader->isUpgradeComplete())
			{
				// all complete
				$upgrader->completeUpgrade();
			}
		}

		return $output;
	}

	public function actionOptions()
	{
		if ($this->isPost())
		{
			$options = \XF\Util\Arr::arrayFilterKeys(
				$this->filter('options', 'array'),
				['collectServerStats'],
				true
			);

			/** @var \XF\Repository\Option $optionRepo */
			$optionRepo = $this->repository('XF:Option');
			$optionRepo->updateOptions($options);

			return $this->redirect('index.php?upgrade/complete');
		}
		else
		{
			return $this->view('XF:Upgrade\Options', 'upgrade_options');
		}
	}

	public function actionComplete()
	{
		if ($this->getInstallHelper()->hasRebuildJobPending())
		{
			return $this->view('XF:Upgrade\RebuildErrors', 'upgrade_rebuild_errors');
		}

		if ($this->options()->currentVersionId == \XF::$versionId)
		{
			$schemaErrors = $this->getUpgrader()->getDefaultSchemaErrors();
			if ($schemaErrors)
			{
				$viewParams = [
					'errors' => $schemaErrors
				];
				return $this->view('XF:Upgrade\Errors', 'upgrade_errors', $viewParams);
			}

			$upgrader = $this->getUpgrader();
			$upgrader->renameLegacyConfigIfNeeded();

			$viewParams = [
				'outdatedTemplates' => $this->app->repository('XF:Template')->countOutdatedTemplates()
			];

			return $this->view('XF:Upgrade\Complete', 'upgrade_complete', $viewParams);
		}
		else
		{
			return $this->error(\XF::phrase('uh_oh_upgrade_did_not_complete'));
		}
	}

	public function actionLogin()
	{
		$error = null;

		if ($this->isPost())
		{
			$input = $this->filter([
				'login' => 'str',
				'password' => 'str'
			]);

			$ip = $this->request->getIp();
			$user = null;
			$error = null;

			try
			{
				/** @var \XF\Service\User\Login $loginService */
				$loginService = $this->service('XF:User\Login', $input['login'], $ip);
				if ($loginService->isLoginLimited($limitType))
				{
					return $this->error(\XF::phrase('your_account_has_temporarily_been_locked_due_to_failed_login_attempts'));
				}

				$loginService->setAllowPasswordUpgrade(false);

				$user = $loginService->validate($input['password'], $error);
			}
			catch (\XF\Db\Exception $e) {}

			if (!$user)
			{
				return $this->notFound($error ?: \XF::phrase('requested_user_not_found'));
			}

			$this->completeLogin($user);

			$visitor = \XF::visitor();
			if (!$visitor->is_admin)
			{
				return $this->error(\XF::phrase('your_account_does_not_have_admin_privileges'));
			}

			return $this->redirect('index.php?upgrade/');
		}

		$viewParams = [
			'error' => $error
		];
		return $this->view('XF:Upgrade\Login','upgrade_login', $viewParams);
	}

	protected function completeLogin(\XF\Entity\User $user)
	{
		$this->session()->changeUser($user);
		\XF::setVisitor($user);

		$ip = $this->request->getIp();

		// Avoid an exception for *very* legacy upgrades
		try
		{
			$this->repository('XF:Ip')->logIp(
				$user->user_id, $ip,
				'user', $user->user_id, 'login_upgrade'
			);
		}
		catch (\XF\Db\Exception $e) {}

		if (!\XF\Util\File::installLockExists())
		{
			\XF\Util\File::writeInstallLock();
		}
	}
}