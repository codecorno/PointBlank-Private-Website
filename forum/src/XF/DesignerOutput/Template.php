<?php

namespace XF\DesignerOutput;

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
		$metadata = [
			'addon_id' => $template->addon_id,
			'version_id' => $template->version_id,
			'version_string' => $template->version_string
		];

		$fileName = $this->getFileName($template);
		return $this->designerOutput->writeFile($this->getTypeDir(), $template->Style, $fileName, $template->template, $metadata, true);
	}

	protected function getEntityForImport($parts, $styleId, $json, array $options)
	{
		list($type, $title) = $parts;

		$template = \XF::em()->getFinder('XF:Template')
			->where([
				'type' => $type,
				'title' => $title,
				'style_id' => $styleId
			])->fetchOne();
		if (!$template)
		{
			$template = \XF::em()->create('XF:Template');

			$template->style_id = $styleId;
			$template->type = $type;
			$template->title = $title;

			$parent = $template->ParentTemplate;
			if ($parent)
			{
				$template->addon_id = $parent->addon_id;
			}
		}

		$this->prepareEntityForImport($template, $options);

		return $template;
	}

	public function import($name, $styleId, $contents, array $metadata, array $options = [])
	{
		$parts = preg_split('#[:/\\\\]#', $name, 2);
		if (count($parts) == 1)
		{
			throw new \InvalidArgumentException("Template $name does not contain a type component");
		}

		list($type, $title) = $parts;

		$template = $this->getEntityForImport($parts, $styleId, null, $options);
		$template->setOption('check_duplicate', false);
		$template->setOption('report_modification_errors', false);

		$template->set('title', $title, ['forceSet' => true]);
		$template->set('type', $type, ['forceSet' => true]);
		$template->set('template', $contents, ['forceSet' => true]);

		// ignoring the meta version can be used when you want to just
		// update the version naturally if needed; requires writing back to the
		// designer output so import mode must be disabled
		if (empty($options['ignore_meta_version']))
		{
			if (isset($metadata['addon_id']))
			{
				$template->addon_id = $metadata['addon_id'];
			}
			if (isset($metadata['version_id']))
			{
				$template->version_id = $metadata['version_id'];
			}
			if (isset($metadata['version_string']))
			{
				$template->version_string = $metadata['version_string'];
			}
		}

		$template->save();
		// this will update the metadata itself

		return $template;
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

	protected $metadataWatchCache = [];

	public function watchTemplate(Templater $templater, $type, $name)
	{
		$styleId = $templater->getStyleId();
		if (!$styleId)
		{
			return false;
		}

		$fileName = $this->convertTemplateNameToFile($type, $name);

		if (isset($this->templatesWatched[$styleId][$fileName]))
		{
			return false;
		}

		$typeDir = $this->getTypeDir();

		if (!isset($this->metadataWatchCache[$styleId]))
		{
			$styleCache = \XF::app()->container('style.cache');
			$styleMap = [];
			foreach ($styleCache[$styleId]['parent_list'] AS $styleId)
			{
				if (!$styleId || !$styleCache[$styleId]['designer_mode'])
				{
					continue;
				}
				$styleMap[$styleId] = $styleCache[$styleId]['designer_mode'];
			}

			$metadata = [];
			foreach ($styleMap AS $styleId => $designerMode)
			{
				$metadata[$designerMode] = [
					'style_id' => $styleId,
					'metadata' => $this->designerOutput->getMetadata($typeDir, $designerMode)
				];
			}

			$this->metadataWatchCache[$styleId] = $metadata;
		}

		$actionTaken = false;

		foreach ($this->metadataWatchCache[$styleId] AS $designerMode => $designerMeta)
		{
			$fullPath = $this->designerOutput->getFilePath($typeDir, $designerMode, $fileName);
			if (file_exists($fullPath))
			{
				$contents = file_get_contents($fullPath);
				$hash = $this->designerOutput->hashContents($contents);

				$templateMeta = isset($designerMeta['metadata'][$fileName]) ? $designerMeta['metadata'][$fileName] : [];
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

					$compiledFile = $templater->getTemplateFilePath($type, $name, $designerMeta['style_id']);
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
						"$type:$name", $designerMeta['style_id'], $contents, $templateMeta,
						['ignore_meta_version' => $ignoreMetaVersion]
					);
					$actionTaken = true;
				}
			}
			else if (isset($designerMeta['metadata'][$fileName]))
			{
				$template = \XF::em()->findOne('XF:Template', [
					'style_id' => $designerMeta['style_id'],
					'type' => $type,
					'title' => $name
				]);
				if ($template)
				{
					$template->delete();
				}
				else
				{
					$this->designerOutput->removeMetadata($typeDir, $designerMode, $fileName);
				}

				$actionTaken = true;
			}
		}

		$this->templatesWatched[$styleId][$fileName] = true;

		if ($actionTaken)
		{
			$this->templatesActioned[$styleId][$fileName] = true;
		}

		return $actionTaken;
	}

	public function hasActionedTemplates()
	{
		return !empty($this->templatesActioned);
	}
}