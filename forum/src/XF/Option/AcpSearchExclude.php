<?php

namespace XF\Option;

class AcpSearchExclude extends AbstractOption
{
	public static function renderCheckbox(\XF\Entity\Option $option, array $htmlParams)
	{
		$choices = self::getSearcherChoices();
		$value = [];

		foreach (array_keys($choices) AS $type)
		{
			if (empty($option->option_value[$type]))
			{
				$value[] = $type;
			}
		}

		return self::getCheckboxRow($option, $htmlParams, $choices, $value);
	}

	public static function verifyOption(array &$choices, \XF\Entity\Option $option)
	{
		if ($option->isInsert())
		{
			// insert - just trust the default value
			return true;
		}

		$exclusions = [];

		foreach (array_keys(self::getSearcherChoices()) AS $type)
		{
			if (!in_array($type, $choices))
			{
				$exclusions[$type] = true;
			}
		}

		$choices = $exclusions;

		return true;
	}

	public static function getSearcherChoices()
	{
		$app = \XF::app();

		$types = [];

		foreach ($app->getContentTypeField('admin_search_class') AS $contentType => $className)
		{
			$class = $app->extendClass($className);

			/** @var \XF\AdminSearch\AbstractHandler $obj */
			$obj = new $class($contentType, $app);

			$types[$obj->getDisplayOrder()][$contentType] = $obj;
		}

		ksort($types);
		$choices = [];

		foreach ($types AS $contentTypes)
		{
			foreach ($contentTypes AS $contentType => $obj)
			{
				$choices[$contentType] = $obj->getTypeName();
			}
		}

		return $choices;
	}
}