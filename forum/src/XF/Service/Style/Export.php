<?php

namespace XF\Service\Style;

use XF\Entity\Style;
use XF\Util\Xml;

class Export extends \XF\Service\AbstractService
{
	const EXPORT_VERSION_ID = 2;

	/**
	 * @var Style
	 */
	protected $style;

	/**
	 * @var \XF\Entity\AddOn
	 */
	protected $addOn;

	protected $independent = false;

	public function __construct(\XF\App $app, Style $style)
	{
		parent::__construct($app);
		$this->setStyle($style);
	}

	public function setStyle(Style $style)
	{
		$this->style = $style;
	}

	public function getStyle()
	{
		return $this->style;
	}

	public function setAddOn(\XF\Entity\AddOn $addOn = null)
	{
		$this->addOn = $addOn;
	}

	public function getAddOn()
	{
		$this->addOn;
	}

	public function setIndependent($independent)
	{
		$this->independent = (bool)$independent;
	}

	public function getIndependent()
	{
		return $this->independent;
	}

	public function exportToXml()
	{
		$document = $this->createXml();
		$styleNode = $this->getStyleNode($document);
		$document->appendChild($styleNode);

		$templatesNode = $document->createElement('templates');
		$styleNode->appendChild($templatesNode);

		foreach ($this->getExportableTemplates() AS $template)
		{
			$templatesNode->appendChild($this->getTemplateNode($document, $template));
		}

		$propertiesNode = $document->createElement('properties');
		$styleNode->appendChild($propertiesNode);

		foreach ($this->getExportablePropertyGroups() AS $group)
		{
			$propertiesNode->appendChild($this->getPropertyGroupNode($document, $group));
		}

		foreach ($this->getExportableProperties() AS $data)
		{
			$propertiesNode->appendChild($this->getPropertyNode($document, $data['property'], $data['addon_id']));
		}

		return $document;
	}

	public function getExportFileName()
	{
		$title = str_replace(' ', '-', utf8_romanize(utf8_deaccent($this->style->title)));
		$addOnLimit = $this->addOn ? '-' . $this->addOn->addon_id : '';

		return "style-{$title}{$addOnLimit}.xml";
	}

	/**
	 * @return \DOMDocument
	 */
	protected function createXml()
	{
		$document = new \DOMDocument('1.0', 'utf-8');
		$document->formatOutput = true;

		return $document;
	}

	protected function getStyleNode(\DOMDocument $document)
	{
		$style = $this->style;

		$styleNode = $document->createElement('style');
		$styleNode->setAttribute('title', $style->title);
		$styleNode->setAttribute('description', $style->description);
		$styleNode->setAttribute('user_selectable', $style->user_selectable);
		if ($this->addOn)
		{
			$styleNode->setAttribute('addon_id', $this->addOn->addon_id);
			$styleNode->setAttribute('base_version_id', $this->addOn->version_id);
		}
		else
		{
			$styleNode->setAttribute('base_version_id', \XF::$versionId);
		}
		$styleNode->setAttribute('export_version', self::EXPORT_VERSION_ID);

		return $styleNode;
	}

	protected function getTemplateNode(\DOMDocument $document, array $template)
	{
		$templateNode = $document->createElement('template');
		$templateNode->setAttribute('title', $template['title']);
		$templateNode->setAttribute('type', $template['type']);
		$templateNode->setAttribute('addon_id', $template['addon_id']);
		$templateNode->setAttribute('version_id', $template['version_id']);
		$templateNode->setAttribute('version_string', $template['version_string']);
		$templateNode->appendChild(
			\XF\Util\Xml::createDomCdataSection($document, $template['template'])
		);

		return $templateNode;
	}

	protected function getPropertyGroupNode(\DOMDocument $document, \XF\Entity\StylePropertyGroup $group)
	{
		$mapped = [
			'group_name',
			'title',
			'description',
			'display_order',
			'addon_id'
		];

		$groupNode = $document->createElement('group');
		foreach ($mapped AS $attr)
		{
			$groupNode->setAttribute($attr, $group->getValue($attr));
		}

		return $groupNode;
	}

	protected function getPropertyNode(\DOMDocument $document, \XF\Entity\StyleProperty $property, $addOnId)
	{
		$mapped = [
			'property_name',
			'group_name',
			'title',
			'description',
			'property_type',
			'value_type',
			'depends_on',
			'value_group',
			'display_order'
		];

		$propertyNode = $document->createElement('property');
		foreach ($mapped AS $attr)
		{
			$propertyNode->setAttribute($attr, $property->getValue($attr));
		}

		if ($property->css_components)
		{
			$propertyNode->setAttribute('css_components', implode(',', $property->css_components));
		}

		$propertyNode->setAttribute('addon_id', $addOnId);

		if ($property->value_parameters !== '')
		{
			$propertyNode->appendChild(Xml::createDomElement($document, 'value_parameters', $property->value_parameters));
		}

		$propertyNode->appendChild(
			Xml::createDomElement($document, 'value', \XF\Util\Json::jsonEncodePretty($property->property_value))
		);

		return $propertyNode;
	}

	/**
	 * @return array
	 */
	protected function getExportableTemplates()
	{
		$style = $this->style;

		$db = $this->db();

		$addonLimitSql = ($this->addOn ? " AND master.addon_id = " . $db->quote($this->addOn->addon_id) : '');

		if ($this->independent)
		{
			return $db->fetchAll("
				SELECT template.*,
					IF(template.addon_id, template.addon_id, COALESCE(master.addon_id, '')) AS addon_id
				FROM xf_template_map AS map
				INNER JOIN xf_template AS template ON (map.template_id = template.template_id)
				LEFT JOIN xf_template AS master ON (master.title = template.title AND master.type = template.type AND master.style_id = 0)
				WHERE map.style_id = ?
					AND template.style_id <> 0
					$addonLimitSql
				ORDER BY map.title
			", $style->style_id);
		}
		else
		{
			return $db->fetchAll("
				SELECT template.*,
					IF(template.addon_id, template.addon_id, COALESCE(master.addon_id, '')) AS addon_id
				FROM xf_template AS template
				LEFT JOIN xf_template AS master ON (master.title = template.title AND master.type = template.type AND master.style_id = 0)
				WHERE template.style_id = ?
					$addonLimitSql
				ORDER BY template.title
			", $style->style_id);
		}
	}

	/**
	 * @return array
	 */
	protected function getExportableProperties()
	{
		$style = $this->style;

		$db = $this->db();

		$addonLimitSql = ($this->addOn ? " AND master.addon_id = " . $db->quote($this->addOn->addon_id) : '');

		if ($this->independent)
		{
			$results = $db->fetchAllKeyed("
				SELECT property.property_id,
					IF(property.addon_id, property.addon_id, COALESCE(master.addon_id, '')) AS addon_id
				FROM xf_style_property_map AS map
				INNER JOIN xf_style_property AS property ON (map.property_id = property.property_id)
				LEFT JOIN xf_style_property AS master ON (master.property_name = property.property_name AND master.style_id = 0)
				WHERE map.style_id = ?
					AND property.style_id <> 0
					$addonLimitSql
				ORDER BY map.property_name
			", 'property_id', $style->style_id);
		}
		else
		{
			$results = $db->fetchAllKeyed("
				SELECT property.property_id,
					IF(property.addon_id, property.addon_id, COALESCE(master.addon_id, '')) AS addon_id
				FROM xf_style_property AS property
				LEFT JOIN xf_style_property AS master ON (master.property_name = property.property_name AND master.style_id = 0)
				WHERE property.style_id = ?
					$addonLimitSql
				ORDER BY property.property_name
			", 'property_id', $style->style_id);
		}

		$properties = $this->em()->findByIds('XF:StyleProperty', array_keys($results));

		foreach ($results AS $id => $result)
		{
			if (!isset($properties[$id]))
			{
				unset($results[$id]);
			}

			$results[$id]['property'] = $properties[$id];
		}

		return $results;
	}

	/**
	 * @return array
	 */
	protected function getExportablePropertyGroups()
	{
		$propertyRepo = $this->repository('XF:StyleProperty');
		$groups = $propertyRepo->getEffectivePropertyGroupsInStyle($this->style);
		$output = [];
		foreach ($groups AS $group)
		{
			if ($this->independent)
			{
				if ($group->style_id == 0)
				{
					continue;
				}
			}
			else
			{
				if ($group->style_id != $this->style->style_id)
				{
					continue;
				}
			}

			if ($this->addOn && $this->addOn->addon_id != $group->addon_id)
			{
				continue;
			}

			$output[$group->group_name] = $group;
		}

		return $output;
	}
}