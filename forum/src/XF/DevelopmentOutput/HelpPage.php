<?php

namespace XF\DevelopmentOutput;

use XF\Mvc\Entity\Entity;
use XF\Util\Json;

class HelpPage extends AbstractHandler
{
	protected function getTypeDir()
	{
		return 'help_pages';
	}

	public function export(Entity $helpPage)
	{
		if (!$this->isRelevant($helpPage))
		{
			return true;
		}

		$fileName = $this->getFileName($helpPage);

		$keys = [
			'page_name',
			'display_order',
			'callback_class',
			'callback_method',
			'advanced_mode',
			'active'
		];
		$json = $this->pullEntityKeys($helpPage, $keys);

		return $this->developmentOutput->writeFile($this->getTypeDir(), $helpPage->addon_id, $fileName, Json::jsonEncodePretty($json));
	}

	public function import($name, $addOnId, $contents, array $metadata, array $options = [])
	{
		$json = json_decode($contents, true);

		$helpPage = $this->getEntityForImport($name, $addOnId, $json, $options);

		$helpPage->bulkSetIgnore($json);
		$helpPage->page_id = $name;
		$helpPage->addon_id = $addOnId;
		$helpPage->save();
		// this will update the metadata itself

		return $helpPage;
	}
}