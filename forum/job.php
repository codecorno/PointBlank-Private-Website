<?php

ignore_user_abort(true);

$dir = __DIR__;
require($dir . '/src/XF.php');

XF::start($dir);
$app = XF::setupApp('XF\Pub\App');

$output = ['more' => false];

if (\XF::$versionId == $app->options()->currentVersionId || !$app->config('checkVersion'))
{
	$jobManager = $app->jobManager();
	$maxRunTime = $app->config('jobMaxRunTime');

	$onlyIds = $app->request()->filter('only_ids', 'array-uint');
	if ($onlyIds)
	{
		$multiResult = $jobManager->runByIds($onlyIds, $maxRunTime);
		if ($multiResult['remaining'])
		{
			$output['more'] = true;

			/** @var \XF\Job\JobResult $jobResult */
			$jobResult = $multiResult['result'];
			if ($jobResult)
			{
				$output['status'] = $jobResult->statusMessage;
			}

			$output['ids'] = $multiResult['remaining'];
		}
		else
		{
			$output['moreAuto'] = $jobManager->queuePending(false);
		}
	}
	else
	{
		$jobResult = $jobManager->runQueue(false, $maxRunTime);
		if ($jobResult)
		{
			$output['more'] = $jobManager->queuePending(false);
		}
	}
}
else
{
	$output['skipped'] = true;
}

header('Content-Type: application/json; charset=UTF-8');
header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
echo json_encode($output);