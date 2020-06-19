<?php

namespace XF\Install\Controller;

use XF\Mvc\ParameterBag;

class Install extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		if ($this->getInstallHelper()->isInstalled())
		{
			throw $this->exception(
				$this->error(\XF::phrase('you_have_completed_installation_to_reinstall'))
			);
		}
	}

	public function actionIndex()
	{
		\XF\Util\Php::resetOpcache();

		$installHelper = $this->getInstallHelper();
		$errors = $installHelper->getRequirementErrors();
		$warnings = $installHelper->getRequirementWarnings();

		// TODO: Must compare hashes

		$viewParams = [
			'errors' => $errors,
			'warnings' => $warnings
		];
		return $this->view('XF:Install\Index', 'install_index', $viewParams);
	}

	public function actionStep1()
	{
		if ($this->app->config('exists'))
		{
			return $this->rerouteController(__CLASS__, 'verifyConfig');
		}
		else
		{
			return $this->rerouteController(__CLASS__, 'buildConfig');
		}
	}

	public function actionVerifyConfig()
	{
		$config = $this->app->config();

		$viewParams = [
			'config' => $config
		];
		return $this->view('XF:Install\VerifyConfig', 'install_verify_config', $viewParams);
	}

	public function actionBuildConfig()
	{
		if ($this->isPost())
		{
			$config = $this->filter([
				'config' => 'array'
			]);
			$config = $this->filterArray($config['config'], [
				'db' => 'array',
				'fullUnicode' => 'bool'
			]);
			$this->testConfig($config, $error);
			if ($error)
			{
				return $this->error($error);
			}

			$configFile = $this->app->container('config.file');
			if (!file_exists($configFile) && is_writable(dirname($configFile)))
			{
				try
				{
					file_put_contents($configFile, $this->getInstallHelper()->generateConfig($config));
					\XF\Util\File::makeWritableByFtpUser($configFile);

					$written = true;
				}
				catch (\Exception $e)
				{
					$written = false;
				}
			}
			else
			{
				$written = false;
			}

			$viewParams = [
				'written' => $written,
				'configFile' => $configFile,
				'config' => $config
			];
			return $this->view('XF:Install\ConfigGenerated', 'install_config_generated', $viewParams);
		}
		else
		{
			return $this->view('XF:Install\BuildConfig', 'install_build_config');
		}
	}

	public function actionStep1b()
	{
		$config = $this->app->config();

		if (!$config['exists'])
		{
			return $this->error(\XF::phrase('config_file_x_could_not_be_found', [
				'file' => $this->app->container('config.file')
			]));
		}

		$db = $this->testConfig($config, $error);

		if ($error)
		{
			return $this->error($error);
		}

		$installHelper = $this->getInstallHelper();

		$errors = $installHelper->getRequirementErrors($db);
		if ($errors)
		{
			return $this->error($errors);
		}

		if ($db)
		{
			$db->getConnection()->close();
			$db = $this->app->db();
		}

		$viewParams = [
			'existingInstall' => $installHelper->hasApplicationTables(),
			'warnings' => $installHelper->getRequirementWarnings($db),
			'config' => $config
		];
		return $this->view('XF:Install\Step1b', 'install_step1b', $viewParams);
	}

	public function actionStep2()
	{
		$this->assertPostOnly();

		$installHelper = $this->getInstallHelper();

		$start = $this->filter('start', 'uint');
		if (!$start)
		{
			if ($this->filter('remove', 'bool'))
			{
				$removed = $installHelper->deleteApplicationTables();
			}
			else
			{
				if ($installHelper->hasApplicationTables())
				{
					return $this->error(\XF::phrase('you_cannot_proceed_unless_tables_removed'));
				}
				$removed = [];
			}
		}
		else
		{
			$removed = [];
		}

		$installHelper->createApplicationTables(5, $start, $endOffset);
		if ($endOffset === false)
		{
			$installHelper->insertDefaultData();
			$installHelper->createDirectories();
		}

		$viewParams = array(
			'removed' => $removed,
			'endOffset' => $endOffset
		);
		return $this->view('XF:Install\Step2', 'install_step2', $viewParams);
	}

	public function actionStep2b()
	{
		$this->getInstallHelper()->insertRebuildJob();

		return $this->rerouteController(__CLASS__, 'runJob');
	}

	public function actionRunJob()
	{
		return $this->manualJobRunner('index.php?install/run-job', 'index.php?install/step/3');
	}

	public function actionStep3()
	{
		if ($this->getInstallHelper()->hasRebuildJobPending())
		{
			return $this->view('XF:Install\RebuildErrors', 'install_rebuild_errors');
		}

		return $this->view('XF:Install\Step3', 'install_step3');
	}

	public function actionStep3b()
	{
		$this->assertPostOnly();

		$input = $this->filter([
			'username' => 'str',
			'email' => 'str'
		]);

		$passwords = $this->filter([
			'password' => 'str',
			'password_confirm' => 'str'
		]);
		if (!$passwords['password_confirm'] || $passwords['password'] !== $passwords['password_confirm'])
		{
			return $this->error(\XF::phrase('passwords_did_not_match'));
		}

		$this->getInstallHelper()->createInitialUser($input, $passwords['password']);

		return $this->redirect('index.php?install/step/4');
	}

	public function actionStep4()
	{
		$options = $this->em()->findByIds('XF:Option', [
			'boardTitle', 'boardUrl', 'contactEmailAddress', 'homePageUrl'
		]);

		$request = $this->request;
		$options['boardUrl']->option_value = preg_replace('#(/install)?/?$#i', '', $request->getFullBasePath());
		$options['homePageUrl']->option_value = $request->getProtocol() . '://' . $request->getHost();

		$user = $this->em()->find('XF:User', 1);
		if ($user)
		{
			$options['contactEmailAddress']->option_value = $user->email;
		}

		$viewParams = [
			'options' => $options
		];
		return $this->view('XF:Install\Step4', 'install_step4', $viewParams);
	}

	public function actionStep4b()
	{
		$this->assertPostOnly();

		$options = \XF\Util\Arr::arrayFilterKeys(
			$this->filter('options', 'array'),
			['boardTitle', 'boardUrl', 'contactEmailAddress', 'homePageUrl', 'collectServerStats'],
			true
		);
		if (!empty($options['contactEmailAddress']))
		{
			$options['defaultEmailAddress'] = $options['contactEmailAddress'];
		}
		if (!empty($options['boardUrl']))
		{
			$options['options']['boardUrl'] = rtrim($options['boardUrl'], '/');
		}

		/** @var \XF\Repository\Option $optionRepo */
		$optionRepo = $this->repository('XF:Option');

		// if applicable, updating collectServerStats will enqueue stats collection automatically
		$optionRepo->updateOptions($options);

		return $this->redirect('index.php?install/complete');
	}

	public function actionComplete()
	{
		$this->getInstallHelper()->completeInstallation();

		return $this->view('XF:Install\Complete', 'install_complete');
	}
}