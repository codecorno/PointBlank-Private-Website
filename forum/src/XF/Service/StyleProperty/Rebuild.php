<?php

namespace XF\Service\StyleProperty;

class Rebuild extends \XF\Service\AbstractService
{
	/**
	 * @var \XF\Tree
	 */
	protected $styleTree;

	/**
	 * @var null|array
	 */
	protected $masterStyleProperties;

	protected function setupStyleTree()
	{
		if ($this->styleTree)
		{
			return;
		}

		/** @var \XF\Repository\Style $repo */
		$repo = $this->app->em()->getRepository('XF:Style');
		$this->styleTree = $repo->getStyleTree(false);
	}

	public function rebuildFullPropertyMap()
	{
		$this->setupStyleTree();

		$grouped = [];
		$propertyRes = $this->db()->query("
			SELECT property_id, property_name, style_id
			FROM xf_style_property
		");
		while ($property = $propertyRes->fetch())
		{
			$grouped[$property['style_id']][$property['property_name']] = $property['property_id'];
		}

		$this->db()->beginTransaction();
		$this->db()->delete('xf_style_property_map', null); // not using emptyTable for transaction safety
		$this->_rebuildPropertyMap(0, [], $grouped);
		$this->db()->commit();
	}

	public function rebuildPropertyMapForProperty($propertyName)
	{
		$this->setupStyleTree();

		$grouped = [];
		$propertyRes = $this->db()->query("
			SELECT property_id, property_name, style_id
			FROM xf_style_property
			WHERE property_name = ?
		", $propertyName);
		while ($property = $propertyRes->fetch())
		{
			$grouped[$property['style_id']][$property['property_name']] = $property['property_id'];
		}

		$this->db()->beginTransaction();
		$this->db()->delete('xf_style_property_map', 'property_name = ?', $propertyName);
		$this->_rebuildPropertyMap(0, [], $grouped);
		$this->db()->commit();
	}

	protected function _rebuildPropertyMap($styleId, array $map, array $propertyList)
	{
		if (isset($propertyList[$styleId]))
		{
			foreach ($propertyList[$styleId] AS $propertyName => $propertyId)
			{
				if (isset($map[$propertyName]))
				{
					$parentPropertyId = $map[$propertyName]['property_id'];
				}
				else
				{
					$parentPropertyId = null;
				}

				$map[$propertyName] = [
					'property_id' => $propertyId,
					'parent_property_id' => $parentPropertyId
				];
			}
		}

		$sql = [];
		foreach ($map AS $propertyName => $data)
		{
			$sql[] = [
				'style_id' => $styleId,
				'property_name' => $propertyName,
				'property_id' => $data['property_id'],
				'parent_property_id' => $data['parent_property_id']
			];
		}
		if ($sql)
		{
			$this->db()->insertBulk('xf_style_property_map', $sql);
		}

		foreach ($this->styleTree->childIds($styleId) AS $childId)
		{
			$this->_rebuildPropertyMap($childId, $map, $propertyList);
		}
	}

	public function rebuildPropertyStyleCache()
	{
		$this->rebuildPropertyStyleCacheForStyle(0);
		$this->repository('XF:Style')->updateAllStylesLastModifiedDateLater();
	}

	public function rebuildPropertyStyleCacheForStyle($styleId)
	{
		$this->setupStyleTree();

		$properties = $this->finder('XF:StyleProperty')->order(['style_id', 'property_name'])->fetch();
		$byStyle = [];
		foreach ($properties AS $property)
		{
			$byStyle[$property->style_id][$property->property_name] = $property;
		}

		$effectiveProperties = [];

		if ($styleId)
		{
			/** @var \XF\Entity\Style|null $style */
			$style = $this->styleTree->getData($styleId);
			if (!$style)
			{
				// invalid style, nothing to do
				return;
			}

			if ($style->parent_id)
			{
				$baseStyle = $this->styleTree->getData($style->parent_id);
				if ($baseStyle)
				{
					$effectiveProperties = $this->repository('XF:StyleProperty')->getEffectivePropertiesInStyle($baseStyle);
				}
			}
			else if (!empty($byStyle[0]))
			{
				$effectiveProperties = $byStyle[0];
			}

			$masterValues = $this->app->registry()->get('masterStyleProperties');
			if ($masterValues)
			{
				$this->masterStyleProperties = $masterValues;
			}
		}
		// when rebuilding from the master, the first thing we'll do is build masterStyleProperties so don't fetch it

		$this->db()->beginTransaction();
		$this->_rebuildPropertyStyleCacheForStyle($styleId, $byStyle, $effectiveProperties);
		$this->db()->commit();
	}

	protected function _rebuildPropertyStyleCacheForStyle($styleId, array $propertiesByStyle, array $effectiveProperties)
	{
		if (isset($propertiesByStyle[$styleId]))
		{
			foreach ($propertiesByStyle[$styleId] AS $property)
			{
				$effectiveProperties[$property->property_name] = $property;
			}
		}

		$values = [];
		foreach ($effectiveProperties AS $name => $property)
		{
			$values[$name] = $this->getPropertyCacheValue($property, $effectiveProperties);
		}

		if ($styleId)
		{
			// if possible, only store values that differ from the master
			if ($this->masterStyleProperties)
			{
				foreach ($values AS $name => $value)
				{
					if (isset($this->masterStyleProperties[$name]) && $value == $this->masterStyleProperties[$name])
					{
						unset($values[$name]);
					}
				}
			}

			/** @var \XF\Entity\Style|null $style */
			$style = $this->styleTree->getData($styleId);
			if ($style)
			{
				$style->properties = $values;
				$style->saveIfChanged($saved, true, false);
			}
		}
		else
		{
			$this->app->registry()->set('masterStyleProperties', $values);
			$this->repository('XF:Style')->updateAllStylesLastModifiedDateLater();

			$this->masterStyleProperties = $values;
		}

		foreach ($this->styleTree->childIds($styleId) AS $childId)
		{
			$this->_rebuildPropertyStyleCacheForStyle($childId, $propertiesByStyle, $effectiveProperties);
		}
	}

	public function getMasterPropertiesWithHueShift($hueShift)
	{
		/** @var \XF\Entity\StyleProperty[] $effectiveProperties */
		$effectiveProperties = $this->finder('XF:StyleProperty')
			->where('style_id', 0)
			->order('property_name')
			->keyedBy('property_name')
			->fetch()
			->toArray();

		$shiftableColors = [
			'paletteColor1', 'paletteColor2', 'paletteColor3', 'paletteColor4', 'paletteColor5',
			'paletteAccent1', 'paletteAccent2', 'paletteAccent3',
		];
		foreach ($shiftableColors AS $propertyName)
		{
			$property = clone $effectiveProperties[$propertyName];
			$color = \XF\Util\Color::colorToRgb($property->property_value);
			if ($color)
			{
				$hsl = \XF\Util\Color::rgbToHsl($color);
				$hsl[0] = abs(($hsl[0] + $hueShift) % 360);
				$color = \XF\Util\Color::hslToRgb($hsl);

				$property->property_value = "rgb({$color[0]}, {$color[1]}, {$color[2]})";
				$effectiveProperties[$propertyName] = $property;
			}
		}

		$values = [];
		foreach ($effectiveProperties AS $name => $property)
		{
			$values[$name] = $this->getPropertyCacheValue($property, $effectiveProperties);
		}

		return $values;
	}

	public function replacePlaceholdersInProperty($value, array $effectiveProperties, array $seenProperties = [])
	{
		if (is_string($value))
		{
			$replaceMatch = function($propertyName, $subName = null) use ($effectiveProperties, $seenProperties)
			{
				$testName = (strlen($subName) ? "{$propertyName}-{$subName}" : $propertyName);

				if (isset($seenProperties[$testName]))
				{
					return '';
				}

				if (!isset($effectiveProperties[$propertyName]))
				{
					return '';
				}

				$matchProperty = $effectiveProperties[$propertyName];
				$innerValue = $matchProperty->property_value;
				if (is_array($innerValue))
				{
					if ($subName === null || !isset($innerValue[$subName]))
					{
						return '';
					}

					$innerValue = $innerValue[$subName];
				}

				$seenProperties[$testName] = true;

				return $this->replacePlaceholdersInProperty($innerValue, $effectiveProperties, $seenProperties);
			};

			$value = preg_replace_callback(
				'/@xf-([a-z0-9_]+)(?!-[a-z0-9_])(\--([a-z0-9_-]+))?/i',
				function ($match) use ($replaceMatch)
				{
					return $replaceMatch($match[1], isset($match[3]) ? $match[3] : null);
				},
				$value
			);

			return $value;
		}
		else if (is_array($value))
		{
			foreach ($value AS &$subValue)
			{
				$subValue = $this->replacePlaceholdersInProperty($subValue, $effectiveProperties, $seenProperties);
			}

			return $value;
		}
		else
		{
			return $value;
		}
	}

	protected function getPropertyCacheValue(\XF\Entity\StyleProperty $property, array $effectiveProperties)
	{
		$value = $property->property_value;
		$value = $this->replacePlaceholdersInProperty($value, $effectiveProperties);

		if ($property->property_type == 'css')
		{
			$value = $this->standardizeLessCacheValue($value, $property->css_components);
		}

		return $value;
	}

	public function standardizeLessCacheValue(array $values, array $allowedComponents)
	{
		$remove = [];
		$sides = ['top', 'right', 'bottom', 'left'];

		if (!in_array('text', $allowedComponents))
		{
			$remove[] = 'font-size';
			$remove[] = 'color';
			$remove[] = 'font-weight';
			$remove[] = 'font-style';
			$remove[] = 'text-decoration';
		}

		if (!in_array('background', $allowedComponents))
		{
			$remove[] = 'background-color';
			$remove[] = 'background-image';
		}

		$checkSimpleBorder = true;

		if (!in_array('border', $allowedComponents))
		{
			$remove[] = 'border-width';
			$remove[] = 'border-color';

			foreach ($sides AS $side)
			{
				$remove[] = "border-{$side}-width";
				$remove[] = "border-{$side}-color";
			}
		}
		else
		{
			$checkSimpleBorder = false;
		}

		if (!in_array('border_radius', $allowedComponents))
		{
			$remove[] = 'border-radius';
			$remove[] = 'border-top-left-radius';
			$remove[] = 'border-top-right-radius';
			$remove[] = 'border-bottom-right-radius';
			$remove[] = 'border-bottom-left-radius';
		}
		else
		{
			$checkSimpleBorder = false;
		}

		if ($checkSimpleBorder)
		{
			$restoreSimpleProp = function($propName) use (&$remove)
			{
				$skipRemove = array_search($propName, $remove);
				if ($skipRemove !== false)
				{
					unset($remove[$skipRemove]);
				}
			};

			if (in_array('border_color_simple', $allowedComponents))
			{
				$restoreSimpleProp('border-color');
			}
			if (in_array('border_width_simple', $allowedComponents))
			{
				$restoreSimpleProp('border-width');
			}
			if (in_array('border_radius_simple', $allowedComponents))
			{
				$restoreSimpleProp('border-radius');
			}
		}

		if (!in_array('padding', $allowedComponents))
		{
			$remove[] = 'padding';

			foreach ($sides AS $side)
			{
				$remove[] = "padding-{$side}";
			}
		}

		if (!in_array('extra', $allowedComponents))
		{
			$remove[] = 'extra';
		}

		foreach ($remove AS $k)
		{
			unset($values[$k]);
		}

		foreach ($values AS $k => &$value)
		{
			$value = trim($value);
			if ($value === '')
			{
				unset($values[$k]);
			}
		}

		if (isset($values['background-image']))
		{
			$values['background-image'] = preg_replace('/^("|\')(.*)\\1$/', '\\2', $values['background-image']);
			if (!preg_match('#^([a-z0-9-]+\(|@|none$)#i', $values['background-image']))
			{
				$values['background-image'] = 'url("' . $values['background-image'] . '")';
			}
		}

		return $values;
	}
}