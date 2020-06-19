<?php

namespace XF\Cron;

class FileCheck
{
	public static function checkFiles()
	{
		$app = \XF::app();

		$fileCheck = $app->em()->create('XF:FileCheck');
		$fileCheck->save();

		$app->jobManager()->enqueueUnique('fileCheck', 'XF:FileCheck', [
			'check_id' => $fileCheck->check_id,
			'automated' => true
		], false);
	}
}