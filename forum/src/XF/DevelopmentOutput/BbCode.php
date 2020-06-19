<?php

namespace XF\DevelopmentOutput;

use XF\Mvc\Entity\Entity;
use XF\Util\Json;

class BbCode extends AbstractHandler
{
	protected function getTypeDir()
	{
		return 'bb_codes';
	}

	public function export(Entity $bbCode)
	{
		if (!$this->isRelevant($bbCode))
		{
			return true;
		}

		$fileName = $this->getFileName($bbCode);

		$keys = [
			'bb_code_mode',
			'has_option',
			'replace_html',
			'replace_html_email',
			'replace_text',
			'callback_class',
			'callback_method',
			'option_regex',
			'trim_lines_after',
			'plain_children',
			'disable_smilies',
			'disable_nl2br',
			'disable_autolink',
			'allow_empty',
			'allow_signature',
			'editor_icon_type',
			'editor_icon_value',
			'active'
		];
		$json = $this->pullEntityKeys($bbCode, $keys);

		return $this->developmentOutput->writeFile($this->getTypeDir(), $bbCode->addon_id, $fileName, Json::jsonEncodePretty($json));
	}

	public function import($name, $addOnId, $contents, array $metadata, array $options = [])
	{
		$json = json_decode($contents, true);

		/** @var \XF\Entity\BbCodeMediaSite $mediaSite */
		$bbCode = $this->getEntityForImport($name, $addOnId, $json, $options);

		$bbCode->bulkSetIgnore($json);
		$bbCode->bb_code_id = $name;
		$bbCode->addon_id = $addOnId;
		$bbCode->save();
		// this will update the metadata itself

		return $bbCode;
	}
}