<?php

namespace XF\DevelopmentOutput;

use XF\Mvc\Entity\Entity;

class Phrase extends AbstractHandler
{
	protected function getTypeDir()
	{
		return 'phrases';
	}
	
	public function export(Entity $phrase)
	{
		if (!$this->isRelevant($phrase))
		{
			return true;
		}

		$fileName = $this->getFileName($phrase);
		$metadata = [
			'global_cache' => $phrase->global_cache,
			'version_id' => $phrase->version_id,
			'version_string' => $phrase->version_string
		];

		return $this->developmentOutput->writeFile($this->getTypeDir(), $phrase->addon_id, $fileName, $phrase->phrase_text, $metadata);
	}

	protected function getEntityForImport($name, $addOnId, $json, array $options)
	{
		$phrase = \XF::em()->getFinder('XF:Phrase')->where('title', $name)->where('language_id', 0)->fetchOne();
		if (!$phrase)
		{
			$phrase = \XF::em()->create('XF:Phrase');
		}

		$phrase = $this->prepareEntityForImport($phrase, $options);

		return $phrase;
	}

	public function import($name, $addOnId, $contents, array $metadata, array $options = [])
	{
		$phrase = $this->getEntityForImport($name, $addOnId, null, $options);
		$phrase->setOption('check_duplicate', false);

		$phrase->title = $name;
		$phrase->language_id = 0;
		$phrase->addon_id = $addOnId;
		$phrase->phrase_text = $contents;

		if (isset($metadata['global_cache']))
		{
			$phrase->global_cache = $metadata['global_cache'];
		}

		// ignoring the meta version can be used when you want to just
		// update the version naturally if needed; requires writing back to the
		// dev output so import mode must be disabled
		if (empty($options['ignore_meta_version']))
		{
			if (isset($metadata['version_id']))
			{
				$phrase->version_id = $metadata['version_id'];
			}
			if (isset($metadata['version_string']))
			{
				$phrase->version_string = $metadata['version_string'];
			}
		}

		$phrase->save();
		// this will update the metadata itself

		return $phrase;
	}

	protected function isRelevant(Entity $entity, $new = true)
	{
		$languageId = $new ? $entity->getValue('language_id') : $entity->getExistingValue('language_id');
		return parent::isRelevant($entity, $new) && !$languageId;
	}

	public function getFileName(Entity $phrase, $new = true)
	{
		$title = $new ? $phrase->getValue('title') : $phrase->getExistingValue('title');
		return $title . '.txt';
	}
}