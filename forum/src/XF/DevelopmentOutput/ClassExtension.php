<?php

namespace XF\DevelopmentOutput;

use XF\Mvc\Entity\Entity;
use XF\Util\Json;

class ClassExtension extends AbstractHandler
{
	protected function getTypeDir()
	{
		return 'class_extensions';
	}
	
	public function export(Entity $extension)
	{
		if (!$this->isRelevant($extension))
		{
			return true;
		}

		$fileName = $this->getFileName($extension);

		$keys = [
			'from_class',
			'to_class',
			'execute_order',
			'active'
		];
		$json = $this->pullEntityKeys($extension, $keys);

		$this->queueExtensionCacheRebuild($extension->addon_id);

		return $this->developmentOutput->writeFile(
			$this->getTypeDir(), $extension->addon_id, $fileName, Json::jsonEncodePretty($json)
		);
	}

	public function delete(Entity $entity, $new = true)
	{
		$return = parent::delete($entity, $new);
		if ($return)
		{
			$addOnId = $new ? $entity->getValue('addon_id') : $entity->getExistingValue('addon_id');
			$this->queueExtensionCacheRebuild($addOnId);
		}

		return $return;
	}

	protected function getEntityForImport($name, $addOnId, $json, array $options)
	{
		/** @var \XF\Entity\ClassExtension $extension */
		$extension = \XF::em()->getFinder('XF:ClassExtension')->where([
			'from_class' => $json['from_class'],
			'to_class' => $json['to_class']
		])->fetchOne();
		if (!$extension)
		{
			$extension = \XF::em()->create('XF:ClassExtension');
		}

		$extension = $this->prepareEntityForImport($extension, $options);

		return $extension;
	}

	public function import($name, $addOnId, $contents, array $metadata, array $options = [])
	{
		$json = json_decode($contents, true);

		$extension = $this->getEntityForImport($name, $addOnId, $json, $options);
		
		$extension->bulkSetIgnore($json);
		$extension->addon_id = $addOnId;
		$extension->save();
		// this will update the metadata itself

		$this->queueExtensionCacheRebuild($extension->addon_id);

		return $extension;
	}

	protected function getFileName(Entity $extension, $new = true)
	{
		$fromClass = $new ? $extension->getValue('from_class') : $extension->getExistingValue('from_class');
		$toClass = $new ? $extension->getValue('to_class') : $extension->getExistingValue('to_class');

		$fromClass = ltrim(preg_replace('#[^a-z0-9_-]#i', '-', $fromClass), '-');
		$toClass = ltrim(preg_replace('#[^a-z0-9_-]#i', '-', $toClass), '-');

		return "{$fromClass}_{$toClass}.json";
	}

	protected function queueExtensionCacheRebuild($addOnId)
	{
		\XF::runOnce('classExtensionCacheRebuild-' . $addOnId, function() use($addOnId)
		{
			$this->rebuildExtensionCache($addOnId);
		});
	}

	public function rebuildExtensionCache($addOnId)
	{
		$fileName = 'extension_hint.php';
		$finalOutput = $this->getExtensionCacheFileValue($addOnId);

		if ($finalOutput)
		{
			$this->developmentOutput->writeSpecialFile($addOnId, $fileName, $finalOutput);
		}
		else
		{
			$this->developmentOutput->deleteSpecialFile($addOnId, $fileName);
		}
	}

	public function getExtensionCacheFileValue($addOnId)
	{
		$finder = \XF::finder('XF:ClassExtension')
			->where('addon_id', $addOnId)
			->order('to_class');
		$extensions = $finder->fetch();

		$grouped = [];

		foreach ($extensions AS $extension)
		{
			$parts = explode('\\', $extension->to_class);

			$class = 'XFCP_' . array_pop($parts);
			$namespace = implode('\\', $parts);

			$grouped[ltrim($namespace, '\\')][] = [
				'class' => $class,
				'from_class' => '\\' . ltrim($extension->from_class, '\\')
			];
		}

		$output = "<?php\n\n"
			. "// ################## THIS IS A GENERATED FILE ##################\n"
			. "// DO NOT EDIT DIRECTLY. EDIT THE CLASS EXTENSIONS IN THE CONTROL PANEL.";

		foreach ($grouped AS $namespace => $extensionValues)
		{
			$output .= "\n\nnamespace $namespace\n";
			$output .= "{";
			foreach ($extensionValues AS $extensionValue)
			{
				$output .= "\n\tclass $extensionValue[class] extends $extensionValue[from_class] {}";
			}
			$output .= "\n}";
		}

		return $output;
	}
}