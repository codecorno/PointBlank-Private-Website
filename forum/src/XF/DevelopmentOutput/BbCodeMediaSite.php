<?php

namespace XF\DevelopmentOutput;

use XF\Mvc\Entity\Entity;
use XF\Util\Json;

class BbCodeMediaSite extends AbstractHandler
{
	protected function getTypeDir()
	{
		return 'bb_code_media_sites';
	}

	public function export(Entity $mediaSite)
	{
		if (!$this->isRelevant($mediaSite))
		{
			return true;
		}

		$fileName = $this->getFileName($mediaSite);

		$keys = [
			'site_title',
			'site_url',
			'match_urls',
			'match_is_regex',
			'match_callback_class',
			'match_callback_method',
			'embed_html',
			'embed_html_callback_class',
			'embed_html_callback_method',
			'oembed_enabled',
			'oembed_api_endpoint',
			'oembed_url_scheme',
			'oembed_retain_scripts',
			'supported',
			'active'
		];
		$json = $this->pullEntityKeys($mediaSite, $keys);

		return $this->developmentOutput->writeFile($this->getTypeDir(), $mediaSite->addon_id, $fileName, Json::jsonEncodePretty($json));
	}

	public function import($name, $addOnId, $contents, array $metadata, array $options = [])
	{
		$json = json_decode($contents, true);

		/** @var \XF\Entity\BbCodeMediaSite $mediaSite */
		$mediaSite = $this->getEntityForImport($name, $addOnId, $json, $options);

		if (isset($json['embed_html']))
		{
			$template = $mediaSite->getMasterTemplate();
			$template->template = $json['embed_html'];
			$mediaSite->addCascadedSave($template);
			unset($json['embed_html']);
		}

		$mediaSite->bulkSetIgnore($json);
		$mediaSite->media_site_id = $name;
		$mediaSite->addon_id = $addOnId;
		$mediaSite->save();
		// this will update the metadata itself

		return $mediaSite;
	}
}