<?php

namespace XF\Admin\Controller;

use XF\Import\Runner;
use XF\Mvc\ParameterBag;

class Import extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertAdminPermission('import');
	}

	public function actionIndex()
	{
		$manager = $this->getImportManager();

		$session = $manager->getCurrentSession();
		if ($session)
		{
			$importer = $manager->getImporter($session->importerId);
			if ($importer)
			{
				$this->triggerSessionRedirect($session);

				$viewParams = [
					'title' => $importer->getSourceTitle()
				];
				return $this->view('XF:Import\Restart', 'import_restart', $viewParams);
			}
		}

		$viewParams = [
			'importersGrouped' => $manager->getImporterListTargetGrouped(),
			'complete' => $this->filter('complete', 'bool')
		];
		return $this->view('XF:Import\Index', 'import_index', $viewParams);
	}

	public function actionCancel()
	{
		$this->assertPostOnly();

		$this->getImportManager()->clearCurrentSession();

		return $this->redirect($this->buildLink('import'));
	}

	public function actionConfig()
	{
		$this->assertPostOnly();

		$manager = $this->getImportManager();
		$importerId = $this->filter('importer', 'str');
		$importer = $manager->getImporter($importerId);

		if (!$importer)
		{
			return $this->redirect($this->buildLink('import'));
		}

		$previousConfig = $this->filter('previous_config', 'json-array');
		$newConfig = $this->filter('config', 'array');
		$baseConfig = array_replace_recursive($previousConfig, $newConfig);

		if ($baseConfig)
		{
			$baseConfig = $importer->prepareBaseConfigFromInput($baseConfig, $this->request);

			$configErrors = [];
			if ($importer->validateBaseConfig($baseConfig, $configErrors))
			{
				return $this->displayStepConfig($importerId, $importer, $baseConfig);
			}
			else if ($configErrors)
			{
				return $this->error($configErrors);
			}
		}

		$viewParams = [
			'importerId' => $importerId,
			'title' => $importer->getSourceTitle(),
			'importer' => $importer,
			'baseConfig' => $baseConfig
		];
		return $this->view('XF:Import\Config', 'import_config', $viewParams);
	}

	protected function displayStepConfig(
		$importerId, \XF\Import\Importer\AbstractImporter $importer, array $baseConfig
	)
	{
		$manager = $this->getImportManager();
		$importer->setBaseConfig($baseConfig);

		$availableSteps = $importer->getSteps();

		$steps = $this->filter('steps', 'array-str');
		if ($steps)
		{
			$steps = $manager->resolveStepDependencies($steps, $availableSteps);

			$retainIds = $this->filter('retain_ids', 'bool');
			$logTable = $this->filter('log_table', 'str');

			if ($this->filter('steps_configured', 'bool'))
			{
				$stepConfig = $this->filter('step_config', 'array');
				$stepConfig['_retainIds'] = $retainIds;
				$stepConfig = $importer->prepareStepConfigFromInput($stepConfig, $this->request);

				$configErrors = [];
				if ($importer->validateStepConfig($steps, $stepConfig, $configErrors))
				{
					$manager->initializeNewImport(
						$importerId, $logTable, $retainIds, $baseConfig, $stepConfig, $steps
					);
					return $this->redirect($this->buildLink('import/start'));
				}
				else if ($configErrors)
				{
					return $this->error($configErrors);
				}
			}
			else
			{
				$log = $manager->getLog($logTable);
				if ($log->isValidTable())
				{
					if (!$log->isEmpty())
					{
						return $this->error(\XF::phrase('log_table_is_valid_but_it_must_not_contain_content_enter_different_name'));
					}
				}
				else if (!$log->canInitialize($logError))
				{
					return $this->error($logError);
				}
			}

			$viewParams = [
				'importerId' => $importerId,
				'title' => $importer->getSourceTitle(),
				'importer' => $importer,
				'baseConfig' => $baseConfig,
				'retainIds' => $retainIds,
				'logTable' => $logTable,
				'steps' => $steps,
				'availableSteps' => $availableSteps
			];
			return $this->view('XF:Import\StepConfig', 'import_step_config', $viewParams);
		}
		else
		{
			$canRetainIds = $importer->canRetainIds();
			$logTable = $importer->getSuggestedLogTableName();

			$viewParams = [
				'importerId' => $importerId,
				'title' => $importer->getSourceTitle(),
				'isCoreImporter' => $importer instanceof \XF\Import\Importer\AbstractCoreImporter,
				'importer' => $importer,
				'baseConfig' => $baseConfig,
				'canRetainIds' => $canRetainIds,
				'logTable' => $logTable,
				'availableSteps' => $availableSteps
			];
			return $this->view('XF:Import\StepChoose', 'import_step_choose', $viewParams);
		}
	}

	public function actionStepConfig()
	{
		$this->assertPostOnly();

		$manager = $this->getImportManager();
		$importerId = $this->filter('importer', 'str');
		$importer = $manager->getImporter($importerId);

		if (!$importer)
		{
			return $this->redirect($this->buildLink('import'));
		}

		$config = $this->filter('config', 'json-array');

		return $this->displayStepConfig($importerId, $importer, $config);
	}

	public function actionStart()
	{
		$manager = $this->getImportManager();
		$session = $manager->getCurrentSession();
		if (!$session)
		{
			return $this->redirect($this->buildLink('import'));
		}

		$this->triggerSessionRedirect($session);

		$viewParams = [
			'title' => $manager->getImporterSourceTitle($session->importerId)
		];
		return $this->view('XF:Import\Start', 'import_start', $viewParams);
	}

	public function actionRun()
	{
		if (!$this->isPost())
		{
			return $this->redirect($this->buildLink('import'));
		}

		$manager = $this->getImportManager();
		$runner = $manager->getRunner();
		if (!$runner)
		{
			return $this->redirect($this->buildLink('import'));
		}

		$session = $runner->getSession();

		$this->triggerSessionRedirect($session);

		if (!$session->canRunVia('web') && !$this->filter('force_run', 'bool'))
		{
			return $this->error(\XF::phrase('this_import_has_been_started_through_another_method'));
		}

		$this->setViewOption('force_page_template', 'PAGE_RUN_JOB');

		$runResult = $runner->run();

		$session->runType = 'web';

		$manager->updateCurrentSession($session);

		//echo \XF::app()->debugger()->getDebugPageHtml(\XF::app()); exit;

		if ($runResult == Runner::STATE_COMPLETE)
		{
			return $this->redirect($this->buildLink('import/finalize'));
		}

		$lastRun = $runner->getLastRun();

		/** @var \XF\Import\StepState $state */
		$state = $lastRun['state'];

		$viewParams = [
			'title' => $runner->getImporter()->getSourceTitle(),
			'stepTitle' => $state->title,
			'stepComplete' => $state->complete,
			'stepCompletion' => $state->getCompletionOutput(),
			'importCompletion' => $runner->getImportCompletionDetails()
		];
		return $this->view('XF:Import\Run', 'import_run', $viewParams);
	}

	public function actionFinalize()
	{
		$manager = $this->getImportManager();
		$session = $manager->getCurrentSession();
		if (!$session || !$session->runComplete)
		{
			return $this->redirect($this->buildLink('import'));
		}

		if ($session->finalized)
		{
			return $this->redirect($this->buildLink('import/complete'));
		}

		$importer = $manager->getImporter($session->importerId);
		if (!$importer)
		{
			$manager->clearCurrentSession();

			return $this->redirect($this->buildLink('import'));
		}

		if ($this->isPost())
		{
			$jobs = $importer->getFinalizeJobs($session->getStepsRun());
			if ($jobs)
			{
				$this->app->jobManager()->enqueueUnique(
					'importFinalize' . $session->logTable,
					'XF:Atomic',
					['execute' => $jobs]
				);
			}

			$session->finalized = true;
			$manager->updateCurrentSession($session);

			return $this->redirect($this->buildLink('import/finalize'));
		}
		else
		{
			$viewParams = [
				'title' => $importer->getSourceTitle(),
				'notes' => $this->getNotesData($importer, $session, 'finalize')
			];
			return $this->view('XF:Import\Finalize', 'import_finalize', $viewParams);
		}
	}

	public function actionComplete()
	{
		$manager = $this->getImportManager();
		$session = $manager->getCurrentSession();
		if (!$session || !$session->finalized)
		{
			return $this->redirect($this->buildLink('import'));
		}

		$importer = $manager->getImporter($session->importerId);
		if (!$importer)
		{
			$manager->clearCurrentSession();

			return $this->redirect($this->buildLink('import'));
		}

		if ($this->isPost())
		{
			$manager->clearCurrentSession();

			return $this->redirect($this->buildLink('import', null, ['complete' => 1]));
		}
		else
		{
			$viewParams = [
				'title' => $importer->getSourceTitle(),
				'notes' => $this->getNotesData($importer, $session, 'complete')
			];
			return $this->view('XF:Import\Complete', 'import_complete', $viewParams);
		}
	}

	protected function getNotesData(
		\XF\Import\Importer\AbstractImporter $importer, \XF\Import\Session $session, $context
	)
	{
		$totals = [];
		$allSteps = $importer->getSteps();
		foreach ($session->stepTotals AS $step => $total)
		{
			if (isset($allSteps[$step]))
			{
				$totals[$step] = [
					'title' => $allSteps[$step]['title'],
					'total' => $total,
					'time' => isset($session->stepTime[$step]) ? $session->stepTime[$step] : null
				];
			}
		}

		return [
			'logTable' => $session->logTable,
			'totals' => $totals,
			'runTime' => $session->getRunTime(),
			'notes' => $importer->getFinalNotes($session, $context)
		];
	}

	/**
	 * @return \XF\Import\Manager
	 */
	protected function getImportManager()
	{
		return $this->app->import()->manager();
	}

	protected function triggerSessionRedirect(\XF\Import\Session $session)
	{
		if ($session->finalized)
		{
			throw $this->exception(
				$this->redirect($this->buildLink('import/complete'))
			);
		}
		if ($session->runComplete)
		{
			throw $this->exception(
				$this->redirect($this->buildLink('import/finalize'))
			);
		}
	}
}