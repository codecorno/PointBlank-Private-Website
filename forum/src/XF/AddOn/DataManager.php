<?php

namespace XF\AddOn;

use XF\AddOn\DataType\AbstractDataType;

class DataManager
{
	/**
	 * @var \XF\Mvc\Entity\Manager
	 */
	protected $em;

	/**
	 * @var DataType\AbstractDataType[]|null
	 */
	protected $types;

	public function __construct(\XF\Mvc\Entity\Manager $em)
	{
		$this->em = $em;
	}

	public function exportAddOn(AddOn $addOn, array &$containers = [], array &$emptyContainers = [])
	{
		$document = new \DOMDocument('1.0', 'utf-8');
		$document->formatOutput = true;

		$root = $document->createElement('addon');
		$document->appendChild($root);

		$addOnId = $addOn->addon_id;

		foreach ($this->getDataTypeHandlers() AS $handler)
		{
			$containerName = $handler->getContainerTag();
			$container = $document->createElement($containerName);
			$handler->exportAddOnData($addOnId, $container);
			$root->appendChild($container);
			$containers[] = $containerName;
		}

		return $document;
	}

	public function enqueueImportAddOnData(AddOn $addOn)
	{
		return \XF::app()->jobManager()->enqueueUnique($this->getImportDataJobId($addOn), 'XF:AddOnData', [
			'addon_id' => $addOn->addon_id
		]);
	}

	public function getImportDataJobId(AddOn $addOn)
	{
		return 'addOnData-' . $addOn->addon_id;
	}

	public function rebuildActiveAddOnCache()
	{
		$data = [];
		$composerData = [];

		// cached add-on entities can end up being saved here so clear entity cache
		$this->em->clearEntityCache('XF:AddOn');

		$addOnManager = \XF::app()->addOnManager();

		$addOns = $this->em->getFinder('XF:AddOn')->where('active', 1)->fetch();
		foreach ($addOns AS $addOn)
		{
			$data[$addOn->addon_id] = $addOn->version_id;

			$addOnClass = $addOnManager->getById($addOn->addon_id);
			if ($addOnClass)
			{
				$composerData[$addOn->addon_id] = $addOnClass->composer_autoload;
			}
		}

		\XF::registry()->set('addOns', $data);
		\XF::registry()->set('addOnsComposer', $composerData);

		return $data;
	}

	public function triggerRebuildActiveChange(\XF\Entity\AddOn $addOn)
	{
		$atomicJobs = [];

		foreach ($this->getDataTypeHandlers() AS $handler)
		{
			$handler->rebuildActiveChange($addOn, $atomicJobs);
		}

		$addOnHandler = new AddOn($addOn, \XF::app()->addOnManager());
		$addOnHandler->onActiveChange($addOn->active, $atomicJobs);

		if ($atomicJobs)
		{
			\XF::app()->jobManager()->enqueueUnique(
				'addOnActive' . $addOn->addon_id,
				'XF:Atomic', ['execute' => $atomicJobs]
			);
		}
	}

	public function triggerRebuildProcessingChange(\XF\Entity\AddOn $addOn)
	{
		// Note: These rebuilds will not take effect until the next request.

		$this->em->getRepository('XF:ClassExtension')->rebuildExtensionCache();
		$this->em->getRepository('XF:CodeEventListener')->rebuildListenerCache();
		$this->em->getRepository('XF:Route')->rebuildRouteCaches();
	}

	public function updateRelatedIds(\XF\Entity\AddOn $addOn, $oldId)
	{
		if ($oldId == $addOn->addon_id)
		{
			return;
		}

		$newId = $addOn->addon_id;

		$db = $this->em->getDb();
		$db->beginTransaction();

		foreach ($this->getDataTypeHandlers() AS $handler)
		{
			$handler->updateAddOnId($oldId, $newId);
		}

		$db->commit();
	}

	public function enqueueRemoveAddOnData($id)
	{
		return \XF::app()->jobManager()->enqueueUnique($id . 'AddOnUnInstall', 'XF:AddOnUninstallData', [
			'addon_id' => $id
		]);
	}

	public function finalizeRemoveAddOnData($addOnId)
	{
		$simpleCache = \XF::app()->simpleCache();
		$simpleCache->deleteSet($addOnId);

		$this->rebuildActiveAddOnCache();
	}

	/**
	 * @return AbstractDataType
	 */
	public function getDataTypeHandler($class)
	{
		$class = \XF::stringToClass($class, '%s\AddOn\DataType\%s');
		$class = \XF::extendClass($class);

		return new $class($this->em);
	}

	/**
	 * @return AbstractDataType[]
	 */
	public function getDataTypeHandlers()
	{
		if ($this->types)
		{
			return $this->types;
		}

		$objects = [];
		foreach ($this->getDataTypeClasses() AS $typeClass)
		{
			$class = \XF::stringToClass($typeClass, '%s\AddOn\DataType\%s');
			$class = \XF::extendClass($class);
			$objects[$typeClass] = new $class($this->em);
		}

		$this->types = $objects;

		return $objects;
	}

	public function getDataTypeClasses()
	{
		return [
			'XF:AdminNavigation',
			'XF:AdminPermission',
			'XF:AdvertisingPosition',
			'XF:ApiScope',
			'XF:BbCode',
			'XF:BbCodeMediaSite',
			'XF:ClassExtension',
			'XF:CodeEvent',
			'XF:CodeEventListener',
			'XF:ContentTypeField',
			'XF:CronEntry',
			'XF:HelpPage',
			'XF:MemberStat',
			'XF:Navigation',
			'XF:Option',
			'XF:OptionGroup',
			'XF:Permission',
			'XF:PermissionInterfaceGroup',
			'XF:Phrase',
			'XF:Route',
			'XF:StyleProperty',
			'XF:StylePropertyGroup',
			'XF:Template',
			'XF:TemplateModification',
			'XF:WidgetDefinition',
			'XF:WidgetPosition'
		];
	}
}