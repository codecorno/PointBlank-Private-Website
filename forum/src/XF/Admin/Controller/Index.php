<?php

namespace XF\Admin\Controller;

class Index extends AbstractController
{
	public function actionIndex()
	{
		/** @var \XF\Repository\Template $templateRepo */
		$templateRepo = $this->repository('XF:Template');

		$showUnicodeWarning = (
			\XF::db()->getSchemaManager()->hasUnicodeMismatch($mismatchType)
			&& $mismatchType == 'loose'
		);

		// TODO: put these bits and pieces into configurable / selectable widgets

		/** @var \XF\AdminNavigation $nav */
		$nav = $this->app['navigation.admin'];

		/** @var \XF\Repository\FileCheck $fileCheckRepo */
		$fileCheckRepo = $this->repository('XF:FileCheck');

		/** @var \XF\Repository\SessionActivity $activityRepo */
		$activityRepo = $this->repository('XF:SessionActivity');

		$stats = [];
		if (\XF::visitor()->hasAdminPermission('viewStatistics'))
		{
			/** @var \XF\Stats\Grouper\AbstractGrouper $grouper */
			$grouper = $this->app->create('stats.grouper', 'daily');

			foreach ($this->getDashboardStatGraphs() AS $statDisplayTypes)
			{
				$now = \XF::$time;
				$start = $now - 30*86400;
				$end = $now - ($now % 86400) - 1; // yesterday

				/** @var \XF\Service\Stats\Grapher $grapher */
				$grapher = $this->service('XF:Stats\Grapher', $start, $end, $statDisplayTypes);
				$stats[] = [
					'data' => $grapher->getGroupedData($grouper),
					'phrases' => $this->repository('XF:Stats')->getStatsTypePhrases($statDisplayTypes)
				];
			}
		}

		$logCounts = [];
		if (\XF::visitor()->hasAdminPermission('viewLogs'))
		{
			$cutOffs = [
				'day' => \XF::$time - 86400,
				'week' => \XF::$time - 86400 * 7,
				'month' => \XF::$time - 86400 * 30,
			];
			foreach ($this->getLogSummaryTypes() AS $logKey => $logData)
			{
				$values = [];
				foreach ($cutOffs AS $cutOffType => $cutOffDate)
				{
					$finder = $this->finder($logData['finder'])
						->where($logData['date'], '>=', $cutOffDate);

					if (!empty($logData['where']))
					{
						$finder->where($logData['where']);
					}

					$values[$cutOffType] = $finder->total();
				}

				$logCounts[$logKey] = $values;
			}
		}

		$installed = [];

		foreach ($this->app->addOnManager()->getInstalledAddOns() AS $id => $addOn)
		{
			if ($id == 'XF' || $addOn->canUpgrade() || $addOn->isLegacy())
			{
				continue;
			}

			if ($addOn->isInstalled())
			{
				$installed[$id] = $addOn;
			}
		}

		$installHelper = new \XF\Install\Helper($this->app);
		$requirementErrors = $installHelper->getRequirementErrors();

		/** @var \XF\Repository\UpgradeCheck $upgradeCheckRepo */
		$upgradeCheckRepo = $this->repository('XF:UpgradeCheck');
		$upgradeCheck = $upgradeCheckRepo->canCheckForUpgrades() ? $upgradeCheckRepo->getLatestUpgradeCheck() : null;

		$viewParams = [
			'outdatedTemplates' => $templateRepo->countOutdatedTemplates(),
			'showUnicodeWarning' => $showUnicodeWarning,
			'hasStoppedManualJobs' => $this->app->jobManager()->hasStoppedManualJobs(),
			'serverErrorLogs' => $this->finder('XF:ErrorLog')->total(),
			'legacyConfig' => file_exists($this->app->container('config.legacyFile')),
			'fileChecks' => $fileCheckRepo->findFileChecksForList()->fetch(5),
			'navigation' => $nav->getTree(),
			'installedAddOns' => $installed,
			'hasProcessingAddOn' => $this->repository('XF:AddOn')->hasAddOnsBeingProcessed(),
			'staffOnline' => $activityRepo->getOnlineStaffList(),
			'stats' => $stats,
			'logCounts' => $logCounts,
			'envReport' => \XF\Util\Php::getEnvironmentReport(),
			'requirementErrors' => $requirementErrors,
			'upgradeCheck' => $upgradeCheck
		];
		return $this->view('XF:Index', 'index', $viewParams);
	}

	protected function getDashboardStatGraphs()
	{
		return [
			['post', 'thread'],
			['user_registration', 'user_activity']
		];
	}

	protected function getLogSummaryTypes()
	{
		return [
			'moderator' => [
				'finder' => 'XF:ModeratorLog',
				'date' => 'log_date'
			],
			'spamTrigger' => [
				'finder' => 'XF:SpamTriggerLog',
				'date' => 'log_date'
			],
			'spamCleaner' => [
				'finder' => 'XF:SpamCleanerLog',
				'date' => 'application_date'
			],
			'emailBounce' => [
				'finder' => 'XF:EmailBounceLog',
				'date' => 'log_date'
			],
			'payment' => [
				'finder' => 'XF:PaymentProviderLog',
				'date' => 'log_date',
				'where' => [
					['log_type', '=', 'payment']
				]
			]
		];
	}
}