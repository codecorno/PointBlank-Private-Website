<?php

namespace XF\Data;

class Language
{
	public function getLocaleList()
	{
		$output = [];
		foreach ($this->getLanguageCodes() AS $code)
		{
			$output[$code] = \XF::phrase('language_code.' . str_replace('-', '_', $code));
		}

		$output = \XF\Util\Arr::deaccentSort($output);

		return $output;
	}

	public function getLanguageCodes()
	{
		return [
			'af-ZA',
			'am-ET',
			'ar-AR',
			'ay-BO',
			'az-AZ',
			'be-BY',
			'bg-BG',
			'bn-IN',
			'bs-BA',
			'ca-ES',
			'ck-US',
			'cs-CZ',
			'cx-PH',
			'cy-GB',
			'da-DK',
			'de-DE',
			'el-GR',
			'en-GB',
			'en-US',
			'eo-EO',
			'es-CL',
			'es-CO',
			'es-ES',
			'es-LA',
			'es-MX',
			'es-VE',
			'et-EE',
			'eu-ES',
			'fa-IR',
			'ff-NG',
			'fi-FI',
			'fo-FO',
			'fr-CA',
			'fr-FR',
			'ga-IE',
			'gl-ES',
			'gu-IN',
			'ha-NG',
			'he-IL',
			'hi-IN',
			'hr-HR',
			'ht-HT',
			'hu-HU',
			'hy-AM',
			'id-ID',
			'ig-NG',
			'is-IS',
			'it-IT',
			'ja-JP',
			'jv-ID',
			'ka-GE',
			'kk-KZ',
			'km-KH',
			'kn-IN',
			'ko-KR',
			'ku-TR',
			'la-VA',
			'li-NL',
			'lo-LA',
			'lt-LT',
			'lv-LV',
			'mg-MG',
			'mk-MK',
			'ml-IN',
			'mn-MN',
			'mr-IN',
			'ms-MY',
			'mt-MT',
			'my-MM',
			'nb-NO',
			'ne-NP',
			'nl-NL',
			'nn-NO',
			'or-IN',
			'pa-IN',
			'pl-PL',
			'ps-AF',
			'pt-BR',
			'pt-PT',
			'qu-PE',
			'rm-CH',
			'ro-RO',
			'ru-RU',
			'sa-IN',
			'sk-SK',
			'sl-SI',
			'so-SO',
			'sq-AL',
			'sr-RS',
			'sv-SE',
			'sw-KE',
			'sy-SY',
			'ta-IN',
			'te-IN',
			'tg-TJ',
			'th-TH',
			'tl-PH',
			'tl-ST',
			'tr-TR',
			'tt-RU',
			'uk-UA',
			'ur-PK',
			'uz-UZ',
			'vi-VN',
			'xh-ZA',
			'yi-DE',
			'yo-NG',
			'zh-CN',
			'zh-HK',
			'zh-TW',
			'zu-ZA'
		];
	}

	public function getDateFormats()
	{
		return [
			'M j, Y',
			'F j, Y',
			'j M Y',
			'j F Y',
			'j/n/y',
			'n/j/y'
		];
	}

	public function getDateFormatExamples(\XF\Language $language = null)
	{
		$language = $language ?: \XF::language();
		$time = gmmktime(18, 30, 48, 8, 29, date('Y'));

		$dateFormats = [];
		foreach ($this->getDateFormats() AS $dateFormat)
		{
			$dateFormats[$dateFormat] = $language->date($time, $dateFormat);
		}

		return $dateFormats;
	}

	public function getTimeFormats()
	{
		return [
			'g:i A',
			'H:i'
		];
	}

	public function getTimeFormatExamples(\XF\Language $language = null)
	{
		$language = $language ?: \XF::language();
		$time = gmmktime(18, 30, 48, 8, 29, date('Y'));

		$timeFormats = [];
		foreach ($this->getTimeFormats() AS $timeFormat)
		{
			$timeFormats[$timeFormat] = $language->time($time, $timeFormat);
		}

		return $timeFormats;
	}
}