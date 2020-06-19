<?php

namespace XF\DevelopmentOutput;

use XF\Mvc\Entity\Entity;
use XF\Template\Templater;

class Template extends AbstractHandler implements \XF\Template\WatcherInterface
{
	protected $templatesWatched = [];
	protected $templatesActioned = [];

	protected function getTypeDir()
	{
		return 'templates';
	}

	public function export(Entity $template)
	{
		if (!$this->isRelevant($template))
		{
			return true;
		}

		$metadata = [
			'version_id' => $template->version_id,
			'version_string' => $template->version_string
		];

		$fileName = $this->getFileName($template);
		return $this->developmentOutput->writeFile($this->getTypeDir(), $template->addon_id, $fileName, $template->template, $metadata, true);
	}

	protected function getEntityForImport($parts, $addOnId, $json, array $options)
	{
		list($type, $title) = $parts;

		$template = \XF::em()->getFinder('XF:Template')
			->where([
				'type' => $type,
				'title' => $title,
				'style_id' => 0
			])->fetchOne();
		if (!$template)
		{
			$template = \XF::em()->create('XF:Template');
		}

		$this->prepareEntityForImport($template, $options);

		return $template;
	}

	public function import($name, $addOnId, $contents, array $metadata, array $options = [])
	{
		$parts = preg_split('#[:/\\\\]#', $name, 2);
		if (count($parts) == 1)
		{
			throw new \InvalidArgumentException("Template $name does not contain a type component");
		}

		list($type, $title) = $parts;

		$template = $this->getEntityForImport($parts, $addOnId, null, $options);
		$template->setOption('check_duplicate', false);
		$template->setOption('report_modification_errors', false);

		$template->set('title', $title, ['forceSet' => true]);
		$template->set('type', $type, ['forceSet' => true]);
		$template->set('style_id', 0, ['forceSet' => true]);
		$template->set('addon_id', $addOnId, ['forceSet' => true]);
		$template->set('template', $contents, ['forceSet' => true]);

		$contentsChanged = (
			!isset($metadata['hash'])
			|| $this->developmentOutput->hashContents($contents) != $metadata['hash']
		);

		// We only use the metadata version if we're told we can or if it appears to match our version of the template.
		// If our version of the template differs, then we potentially need to update the template version. This approach
		// should avoid situations where templates get updated but their version does not.
		$useMetaVersion = (empty($options['ignore_meta_version']) && !$contentsChanged);
		$usedMetaVersion = false;

		if ($useMetaVersion && isset($metadata['version_id']) && isset($metadata['version_string']))
		{
			$template->version_id = $metadata['version_id'];
			$template->version_string = $metadata['version_string'];

			$usedMetaVersion = true;
		}

		$template->preSave();

		if ($template->isChanged('version_id') && !$usedMetaVersion)
		{
			// we need to write this back to the dev output
			$template->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', true);
		}

		$template->save();
		// this will update the metadata itself

		return $template;
	}

	protected function isRelevant(Entity $entity, $new = true)
	{
		$styleId = $new ? $entity->getValue('style_id') : $entity->getExistingValue('style_id');
		return parent::isRelevant($entity, $new) && !$styleId;
	}

	public function getFileName(Entity $template, $new = true)
	{
		$title = $new ? $template->getValue('title') : $template->getExistingValue('title');
		$type = $new ? $template->getValue('type') : $template->getExistingValue('type');

		return $this->convertTemplateNameToFile($type, $title);
	}

	public function convertTemplateFileToName($name)
	{
		if (substr($name, -5) == '.html')
		{
			$name = substr($name, 0, -5);
		}

		$name = str_replace('/', ':', $name);

		return $name;
	}

	public function convertTemplateNameToFile($type, $name)
	{
		if (!strpos($name, '.'))
		{
			$name = "$name.html";
		}

		return $type . '/' . $name;
	}

	public function watchTemplate(Templater $templater, $type, $name)
	{
		$fileName = $this->convertTemplateNameToFile($type, $name);

		if (isset($this->templatesWatched[$fileName]))
		{
			return false;
		}

		$typeDir = $this->getTypeDir();
		$actionTaken = false;

		foreach ($this->developmentOutput->getTypeMetadata($typeDir) AS $addOnId => $addOnMeta)
		{
			$fullPath = $this->developmentOutput->getFilePath($typeDir, $addOnId, $fileName);
			if (file_exists($fullPath))
			{
				$contents = file_get_contents($fullPath);
				$hash = $this->developmentOutput->hashContents($contents);

				$templateMeta = isset($addOnMeta[$fileName]) ? $addOnMeta[$fileName] : [];
				$metaHash = $templateMeta ? $templateMeta['hash'] : null;

				if (!$metaHash || $hash !== $metaHash)
				{
					// metadata doesn't exist or has a different hash - template has been added
					// or edited so the version needs to be updated
					$recompile = true;
					$ignoreMetaVersion = true;
				}
				else
				{
					// metadata matches -- we can accept the metadata hash but might still need to recompile
					$recompile = false;
					$ignoreMetaVersion = false;

					$compiledFile = $templater->getTemplateFilePath($type, $name, 0);
					if (!file_exists($compiledFile))
					{
						$recompile = true;
					}
					else
					{
						$compiledData = file_get_contents($compiledFile);
						if (preg_match('#<?php\s+// FROM HASH: ([^\s]+)#s', $compiledData, $match))
						{
							if ($match[1] !== $hash)
							{
								$recompile = true;
							}
						}
						else
						{
							$recompile = true;
						}
					}
				}

				if ($recompile)
				{
					$this->import(
						"$type:$name", $addOnId, $contents, $templateMeta,
						['ignore_meta_version' => $ignoreMetaVersion]
					);
					$actionTaken = true;
				}
			}
			else if (isset($addOnMeta[$fileName]))
			{
				$this->developmentOutput->removeMetadata($typeDir, $addOnId, $fileName);
				$actionTaken = true;
			}
		}

		$this->templatesWatched[$fileName] = true;

		if ($actionTaken)
		{
			$this->templatesActioned[$fileName] = true;
		}

		return $actionTaken;
	}

	public function hasActionedTemplates()
	{
		return !empty($this->templatesActioned);
	}
}