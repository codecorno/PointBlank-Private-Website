<?php

namespace XF\Service\Style;

use XF\Entity\Style;

class Import extends \XF\Service\AbstractService
{
	/**
	 * @var Style|null
	 */
	protected $overwriteStyle;

	/**
	 * @var Style|null
	 */
	protected $parentStyle;

	public function setOverwriteStyle(Style $style)
	{
		$this->overwriteStyle = $style;
		$this->parentStyle = null;
	}

	public function getOverwriteStyle()
	{
		return $this->overwriteStyle;
	}

	public function setParentStyle(Style $style = null)
	{
		$this->parentStyle = $style;
		$this->overwriteStyle = null;
	}

	public function getParentStyle()
	{
		return $this->parentStyle;
	}

	public function isValidXml($rootElement, &$error = null)
	{
		if (!($rootElement instanceof \SimpleXMLElement))
		{
			$error = \XF::phrase('please_upload_valid_style_xml_file');
			return false;
		}

		if ($rootElement->getName() != 'style' || (string)$rootElement['title'] === '')
		{
			$error = \XF::phrase('please_upload_valid_style_xml_file');
			return false;
		}

		if ((string)$rootElement['export_version'] != (string)Export::EXPORT_VERSION_ID)
		{
			$error = \XF::phrase('this_style_xml_file_was_not_built_for_this_version_of_xenforo');
			return false;
		}

		return true;
	}

	public function isValidConfiguration(\SimpleXMLElement $document, &$errors = null)
	{
		$errors = [];

		$addOnId = (string)$document['addon_id'];

		if ($addOnId && $addOnId != 'XF')
		{
			$addOn = $this->app->addOnManager()->getById($addOnId);
			if (!$addOn)
			{
				$errors['addon_id'] = \XF::phrase('xml_file_relates_add_on_not_installed_install_first');
				$expectedVersionId = null;
			}
			else
			{
				$expectedVersionId = $addOn->version_id;
			}
		}
		else
		{
			$expectedVersionId = \XF::$versionId;
		}

		$baseVersionId = (int)$document['base_version_id'];

		if ($expectedVersionId && $baseVersionId)
		{
			if ($baseVersionId > $expectedVersionId)
			{
				$errors['version_id'] = \XF::phrase('xml_file_based_on_newer_version_than_installed');
			}
		}

		if ($this->overwriteStyle)
		{
			$title = (string)$document['title'];
			if ($title != $this->overwriteStyle->title)
			{
				$errors['title'] = \XF::phrase('title_of_style_importing_differs_overwriting_is_correct');
			}
		}

		return (count($errors) == 0);
	}

	public function importFromXml(\SimpleXMLElement $document)
	{
		$db = $this->db();
		$db->beginTransaction();

		$addOnId = (string)$document['addon_id'];

		$style = $this->getTargetStyle($document);

		$this->importPropertyGroups($style, $document->properties, $addOnId);
		$this->importProperties($style, $document->properties, $addOnId);
		$this->importTemplates($style, $document->templates, $addOnId);

		/** @var \XF\Repository\Style $styleRepo */
		$styleRepo = $this->repository('XF:Style');
		$styleRepo->triggerStyleDataRebuild();

		$db->commit();

		return $style;
	}

	public function importTemplates(Style $style, \SimpleXMLElement $container, $addOnId)
	{
		$styleId = $style->style_id;
		$existingTemplates = $this->getExistingTemplates($style);

		foreach ($container->template AS $xmlTemplate)
		{
			$title = (string)$xmlTemplate['title'];
			$type = (string)$xmlTemplate['type'];
			$key = "$type-$title";

			$template = isset($existingTemplates[$key])
				? $existingTemplates[$key]
				: $this->em()->create('XF:Template');

			$template->title = $title;
			$template->style_id = $styleId;
			$template->type = $type;
			$this->setupTemplateImport($template, $xmlTemplate);

			$template->save(true, false);

			unset($existingTemplates[$key]);
		}

		// removed templates
		foreach ($existingTemplates AS $existingTemplate)
		{
			if ($addOnId && $existingTemplate->addon_id !== $addOnId)
			{
				// wouldn't be covered so leave it
				continue;
			}

			$this->setTemplateOptions($existingTemplate);
			$existingTemplate->delete(true, false);
		}
	}

	/**
	 * @param Style $style
	 *
	 * @return \XF\Entity\Template[]
	 */
	protected function getExistingTemplates(Style $style)
	{
		/** @var \XF\Finder\Template $templateFinder */
		$templateFinder = $this->finder('XF:Template');
		$templateFinder->where('style_id', $style->style_id)
			->orderTitle();

		$output = [];
		foreach ($templateFinder->fetch() AS $template)
		{
			$output["{$template->type}-{$template->title}"] = $template;
		}

		return $output;
	}

	protected function setupTemplateImport(\XF\Entity\Template $template, \SimpleXMLElement $xmlTemplate)
	{
		$this->setTemplateOptions($template);

		$template->template = \XF\Util\Xml::processSimpleXmlCdata($xmlTemplate);
		$template->addon_id = (string)$xmlTemplate['addon_id'];
		$template->version_id = (int)$xmlTemplate['version_id'];
		$template->version_string = (string)$xmlTemplate['version_string'];
	}

	protected function setTemplateOptions(\XF\Entity\Template $template)
	{
		$template->setOption('recompile', false);
		$template->setOption('test_compile', false);
		$template->setOption('rebuild_map', false);
		$template->setOption('check_duplicate', false);

		$template->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', false);
	}

	public function importPropertyGroups(Style $style, \SimpleXMLElement $container, $addOnId)
	{
		$styleId = $style->style_id;
		$existingGroups = $this->getExistingGroups($style);

		foreach ($container->group AS $xmlGroup)
		{
			$groupName = (string)$xmlGroup['group_name'];

			$group = isset($existingGroups[$groupName])
				? $existingGroups[$groupName]
				: $this->em()->create('XF:StylePropertyGroup');

			$group->group_name = $groupName;
			$group->style_id = $styleId;
			$this->setupPropertyGroupImport($group, $xmlGroup);

			$group->save(true, false);

			unset($existingGroups[$groupName]);
		}

		// removed groups
		foreach ($existingGroups AS $existingGroup)
		{
			if ($addOnId && $existingGroup->addon_id !== $addOnId)
			{
				// wouldn't be covered so leave it
				continue;
			}

			$existingGroup->delete(true, false);
		}
	}

	protected function setupPropertyGroupImport(\XF\Entity\StylePropertyGroup $group, \SimpleXMLElement $xmlGroup)
	{
		$group->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', false);

		$group->title = (string)$xmlGroup['title'];
		$group->description = (string)$xmlGroup['description'];
		$group->display_order = (int)$xmlGroup['display_order'];
		$group->addon_id = (string)$xmlGroup['addon_id'];
	}

	/**
	 * @param Style $style
	 *
	 * @return \XF\Entity\StylePropertyGroup[]
	 */
	protected function getExistingGroups(Style $style)
	{
		$finder = $this->finder('XF:StylePropertyGroup')
			->where('style_id', $style->style_id)
			->keyedBy('group_name');

		return $finder->fetch();
	}

	public function importProperties(Style $style, \SimpleXMLElement $container, $addOnId)
	{
		$styleId = $style->style_id;
		$existingProperties = $this->getExistingProperties($style);

		foreach ($container->property AS $xmlProperty)
		{
			$propertyName = (string)$xmlProperty['property_name'];

			$property = isset($existingProperties[$propertyName])
				? $existingProperties[$propertyName]
				: $this->em()->create('XF:StyleProperty');

			$property->property_name = $propertyName;
			$property->style_id = $styleId;
			$this->setupPropertyImport($property, $xmlProperty);

			$property->save(true, false);

			unset($existingProperties[$propertyName]);
		}

		// removed properties
		foreach ($existingProperties AS $existingProperty)
		{
			if ($addOnId && $existingProperty->addon_id !== $addOnId)
			{
				// wouldn't be covered so leave it
				continue;
			}

			$this->setPropertyOptions($existingProperty);
			$existingProperty->delete(false, false);
		}
	}

	protected function setupPropertyImport(\XF\Entity\StyleProperty $property, \SimpleXMLElement $xmlProperty)
	{
		$this->setPropertyOptions($property);

		$property->group_name = (string)$xmlProperty['group_name'];
		$property->title = (string)$xmlProperty['title'];
		$property->description = (string)$xmlProperty['description'];
		$property->property_type = (string)$xmlProperty['property_type'];
		$property->value_type = (string)$xmlProperty['value_type'];
		$property->depends_on = (string)$xmlProperty['depends_on'];
		$property->value_group = (string)$xmlProperty['value_group'];
		$property->display_order = (int)$xmlProperty['display_order'];
		$property->addon_id = (string)$xmlProperty['addon_id'];

		$cssComponents = (string)$xmlProperty['css_components'];
		$property->css_components = $cssComponents ? explode(',', $cssComponents) : [];

		$property->value_parameters = (string)$xmlProperty->value_parameters;

		$value = (string)$xmlProperty->value;
		$property->property_value = json_decode($value, true);
	}

	protected function setPropertyOptions(\XF\Entity\StyleProperty $property)
	{
		$property->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', false);

		$property->setOption('rebuild_map', false);
		$property->setOption('rebuild_style', false);
	}

	/**
	 * @param Style $style
	 *
	 * @return \XF\Entity\StyleProperty[]
	 */
	protected function getExistingProperties(Style $style)
	{
		$finder = $this->finder('XF:StyleProperty')
			->where('style_id', $style->style_id)
			->keyedBy('property_name');

		return $finder->fetch();
	}

	protected function getTargetStyle(\SimpleXMLElement $document)
	{
		if ($this->overwriteStyle)
		{
			return $this->overwriteStyle;
		}
		else
		{
			$style = $this->em()->create('XF:Style');
			$style->title = (string)$document['title'];
			$style->description = (string)$document['description'];
			$style->parent_id = $this->parentStyle ? $this->parentStyle->style_id : 0;
			$style->user_selectable = (string)$document['user_selectable'];

			$style->save(true, false);

			return $style;
		}
	}
}