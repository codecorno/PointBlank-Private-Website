<?php

namespace XF\Option;

abstract class AbstractOption
{
	/**
	 * @return \XF\Template\Templater
	 */
	protected static function getTemplater()
	{
		return \XF::app()->templater();
	}

	protected static function convertChoicesToTemplaterForm(array $choices)
	{
		return self::getTemplater()->mergeChoiceOptions([], $choices);
	}

	protected static function getControlOptions(\XF\Entity\Option $option, array $htmlParams, $value = null)
	{
		return [
			'name' => $htmlParams['inputName'],
			'value' => $value === null ? $option->option_value : $value
		];
	}

	protected static function getRowOptions(\XF\Entity\Option $option, array $htmlParams)
	{
		return [
			'label' => $option->title,
			'hint' => $htmlParams['hintHtml'],
			'explain' => $htmlParams['explainHtml'],
			'html' => $htmlParams['listedHtml']
		];
	}

	protected static function getSelectRow(\XF\Entity\Option $option, array $htmlParams, array $choices, $value = null)
	{
		$controlOptions = self::getControlOptions($option, $htmlParams, $value);
		$rowOptions = self::getRowOptions($option, $htmlParams);
		$choices = self::convertChoicesToTemplaterForm($choices);

		return self::getTemplater()->formSelectRow($controlOptions, $choices, $rowOptions);
	}

	protected static function getRadioRow(\XF\Entity\Option $option, array $htmlParams, array $choices, $value = null)
	{
		$controlOptions = self::getControlOptions($option, $htmlParams, $value);
		$rowOptions = self::getRowOptions($option, $htmlParams);
		$choices = self::convertChoicesToTemplaterForm($choices);

		return self::getTemplater()->formRadioRow($controlOptions, $choices, $rowOptions);
	}

	protected static function getCheckboxRow(\XF\Entity\Option $option, array $htmlParams, array $choices, $value = null)
	{
		$controlOptions = self::getControlOptions($option, $htmlParams, $value);
		$rowOptions = self::getRowOptions($option, $htmlParams);
		$choices = self::convertChoicesToTemplaterForm($choices);

		return self::getTemplater()->formCheckBoxRow($controlOptions, $choices, $rowOptions);
	}

	protected static function getNumberBoxRow(\XF\Entity\Option $option, array $htmlParams, $value = null)
	{
		$controlOptions = self::getControlOptions($option, $htmlParams, $value);

		foreach (['min', 'max', 'step', 'units'] AS $var)
		{
			if (array_key_exists($var, $htmlParams))
			{
				$controlOptions[$var] = $htmlParams[$var];
			}
		}

		$rowOptions = self::getRowOptions($option, $htmlParams);

		return self::getTemplater()->formNumberBoxRow($controlOptions, $rowOptions);
	}

	protected static function getTemplate($template, \XF\Entity\Option $option, array $htmlParams, array $extraParams = [])
	{
		$params = array_merge([
			'option' => $option,
			'inputName' => $htmlParams['inputName'],
			'explainHtml' => $htmlParams['explainHtml'],
			'hintHtml' => $htmlParams['hintHtml'],
			'listedHtml' => $htmlParams['listedHtml']
		], $extraParams);

		return self::getTemplater()->renderTemplate($template, $params);
	}
}