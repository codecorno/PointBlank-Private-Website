<?php

namespace XF\DevelopmentOutput;

use XF\Mvc\Entity\Entity;
use XF\Util\Json;

class CodeEventListener extends AbstractHandler
{
	protected function getTypeDir()
	{
		return 'code_event_listeners';
	}
	
	public function export(Entity $listener)
	{
		if (!$this->isRelevant($listener))
		{
			return true;
		}

		$fileName = $this->getFileName($listener);

		$keys = [
			'event_id',
			'execute_order',
			'callback_class',
			'callback_method',
			'active',
			'hint',
			'description',
		];
		$json = $this->pullEntityKeys($listener, $keys);

		$written = $this->developmentOutput->writeFile($this->getTypeDir(), $listener->addon_id, $fileName, Json::jsonEncodePretty($json));

		if ($written)
		{
			// pre-2.0 RC2, the file name was different (didn't change based on hint). We want to remove this when we
			// write the new file.
			$legacyFileName = $this->getLegacyFileName($listener);
			$this->developmentOutput->deleteFile($this->getTypeDir(), $listener->addon_id, $legacyFileName);
		}

		return $written;
	}

	protected function getEntityForImport($name, $addOnId, $json, array $options)
	{
		/** @var \XF\Entity\CodeEventListener $listener */
		$listener = \XF::em()->getFinder('XF:CodeEventListener')->where([
			'addon_id' => $addOnId,
			'event_id' => $json['event_id'],
			'callback_class' => $json['callback_class'],
			'callback_method' => $json['callback_method'],
			'hint' => $json['hint']
		])->fetchOne();
		if (!$listener)
		{
			$listener = \XF::em()->create('XF:CodeEventListener');
		}

		$listener = $this->prepareEntityForImport($listener, $options);

		return $listener;
	}

	public function import($name, $addOnId, $contents, array $metadata, array $options = [])
	{
		$json = json_decode($contents, true);

		$listener = $this->getEntityForImport($name, $addOnId, $json, $options);
		$listener->setOption('check_duplicate', false);

		$listener->bulkSetIgnore($json);
		$listener->addon_id = $addOnId;
		$listener->save();
		// this will update the metadata itself

		return $listener;
	}

	protected function getFileName(Entity $listener, $new = true)
	{
		$eventId = $new ? $listener->getValue('event_id') : $listener->getExistingValue('event_id');

		$callbackClass = $new ? $listener->getValue('callback_class') : $listener->getExistingValue('callback_class');
		$callbackMethod = $new ? $listener->getValue('callback_method') : $listener->getExistingValue('callback_method');
		$eventHint = $new ? $listener->getValue('hint') : $listener->getExistingValue('hint');

		$hashSuffix = md5("$callbackClass-$callbackMethod-$eventHint");

		return "{$eventId}_{$hashSuffix}.json";
	}

	protected function getLegacyFileName(Entity $listener, $new = true)
	{
		$eventId = $new ? $listener->getValue('event_id') : $listener->getExistingValue('event_id');

		$callbackClass = $new ? $listener->getValue('callback_class') : $listener->getExistingValue('callback_class');
		$callbackMethod = $new ? $listener->getValue('callback_method') : $listener->getExistingValue('callback_method');

		$cleanClass = preg_replace('#[^a-z0-9_-]#i', '-', $callbackClass);
		$cleanClass = ltrim($cleanClass, '-');

		return "{$eventId}_{$cleanClass}_{$callbackMethod}.json";
	}
}