<?php

namespace XF\Option;

class Phrase extends AbstractOption
{
	public static function renderOption(\XF\Entity\Option $option, array $htmlParams)
	{
		return self::getTemplate('admin:option_template_phrase', $option, $htmlParams, [
			'phrase' => self::getMasterPhrase($option->option_id)
		]);
	}

	public static function verifyOption(&$value, \XF\Entity\Option $option, $optionId)
	{
		$phrase = self::getMasterPhrase($optionId);
		$phrase->phrase_text = $value;
		$phrase->save();

		$value = $phrase->title;
	}

	/**
	 * @param string $optionId
	 * @param integer|null $index Allows overrides of the base phrase for other purposes
	 *
	 * @return string
	 */
	public static function getPhraseName($optionId, $index = 'default')
	{
		return \XF\Util\Php::fromCamelCase($optionId) . '.' . $index;
	}

	/**
	 * @param string $optionId
	 * @param integer|null $index Allows overrides of the base phrase for other purposes
	 *
	 * @return \XF\Finder\Phrase
	 */
	protected static function getPhraseFinder($optionId, $index = 'default')
	{
		return \XF::app()->finder('XF:Phrase')
			->where('title', self::getPhraseName($optionId, $index))
			->where('language_id', 0)
			->where('addon_id', '');
	}

	public static function getMasterPhrase($optionId, $index = 'default')
	{
		$phrase = self::getPhraseFinder($optionId, $index)->fetchOne();
		if (!$phrase)
		{
			$phrase = \XF::app()->em()->create('XF:Phrase');
			$phrase->title = self::getPhraseName($optionId, $index);
			$phrase->addon_id = '';
			$phrase->language_id = 0;
		}

		return $phrase;
	}
}