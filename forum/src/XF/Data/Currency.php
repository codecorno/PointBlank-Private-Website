<?php

namespace XF\Data;

class Currency
{
	protected $popularCurrencies = [
		'AUD', 'CAD', 'EUR', 'GBP', 'USD'
	];

	public function getCurrencyOptions($popular = false)
	{
		$output = [];
		foreach ($this->getCurrencyData() AS $code => $info)
		{
			if ($popular)
			{
				if (in_array($code, $this->popularCurrencies))
				{
					$output[$code] = $info['code'] . ' - ' . \XF::phrase($info['phrase']);
				}
			}
			else
			{
				$output[$code] = $info['code'] . ' - ' . \XF::phrase($info['phrase']);
			}
		}
		return $output;
	}

	public function languageFormat($value, $currencyCode, \XF\Language $language = null, $format = null)
	{
		$language = $language ?: \XF::language();

		if ($currency = $this->getCurrency($currencyCode))
		{
			return $language->currencyFormat($value, $currency['symbol'], $currency['precision'], $format);
		}
		else
		{
			return $language->currencyFormat($value, $currencyCode, 2, $format);
		}
	}

	public function getCurrencyData()
	{
		// TODO: periodically check to see if FA has more icons available

		return [
			'AED' => ['code' => 'AED', 'symbol' => 'AED',	'precision' => 2, 'phrase' => 'currency.aed'],
			'AFN' => ['code' => 'AFN', 'symbol' => 'AFN',	'precision' => 2, 'phrase' => 'currency.afn'],
			'ALL' => ['code' => 'ALL', 'symbol' => 'ALL',	'precision' => 2, 'phrase' => 'currency.all'],
			'AMD' => ['code' => 'AMD', 'symbol' => 'AMD',	'precision' => 2, 'phrase' => 'currency.amd'],
			'AOA' => ['code' => 'AOA', 'symbol' => 'AOA',	'precision' => 2, 'phrase' => 'currency.aoa'],
			'ARS' => ['code' => 'ARS', 'symbol' => 'ARS',	'precision' => 2, 'phrase' => 'currency.ars'],
			'AUD' => ['code' => 'AUD', 'symbol' => 'AU$',	'precision' => 2, 'phrase' => 'currency.aud'],
			'AWG' => ['code' => 'AWG', 'symbol' => 'AWG',	'precision' => 2, 'phrase' => 'currency.awg'],
			'AZN' => ['code' => 'AZN', 'symbol' => 'AZN',	'precision' => 2, 'phrase' => 'currency.azn'],
			'BAM' => ['code' => 'BAM', 'symbol' => 'BAM',	'precision' => 2, 'phrase' => 'currency.bam'],
			'BBD' => ['code' => 'BBD', 'symbol' => 'BBD',	'precision' => 2, 'phrase' => 'currency.bbd'],
			'BDT' => ['code' => 'BDT', 'symbol' => 'BDT',	'precision' => 2, 'phrase' => 'currency.bdt'],
			'BGN' => ['code' => 'BGN', 'symbol' => 'BGN',	'precision' => 2, 'phrase' => 'currency.bgn'],
			'BHD' => ['code' => 'BHD', 'symbol' => 'BHD',	'precision' => 3, 'phrase' => 'currency.bhd'],
			'BIF' => ['code' => 'BIF', 'symbol' => 'BIF',	'precision' => 0, 'phrase' => 'currency.bif'],
			'BMD' => ['code' => 'BMD', 'symbol' => 'BMD',	'precision' => 2, 'phrase' => 'currency.bmd'],
			'BND' => ['code' => 'BND', 'symbol' => 'BND',	'precision' => 2, 'phrase' => 'currency.bnd'],
			'BOB' => ['code' => 'BOB', 'symbol' => 'BOB',	'precision' => 2, 'phrase' => 'currency.bob'],
			'BRL' => ['code' => 'BRL', 'symbol' => 'R$',	'precision' => 2, 'phrase' => 'currency.brl'],
			'BWP' => ['code' => 'BWP', 'symbol' => 'BWP',	'precision' => 2, 'phrase' => 'currency.bwp'],
			'BYN' => ['code' => 'BYN', 'symbol' => 'BYN',	'precision' => 0, 'phrase' => 'currency.byn'],
			'BZD' => ['code' => 'BZD', 'symbol' => 'BZD',	'precision' => 2, 'phrase' => 'currency.bzd'],
			'CAD' => ['code' => 'CAD', 'symbol' => 'CA$',	'precision' => 2, 'phrase' => 'currency.cad'],
			'CDF' => ['code' => 'CDF', 'symbol' => 'CDF',	'precision' => 2, 'phrase' => 'currency.cdf'],
			'CHF' => ['code' => 'CHF', 'symbol' => 'CHF',	'precision' => 2, 'phrase' => 'currency.chf'],
			'CLP' => ['code' => 'CLP', 'symbol' => 'CLP',	'precision' => 0, 'phrase' => 'currency.clp'],
			'CNY' => ['code' => 'CNY', 'symbol' => '¥',		'precision' => 2, 'phrase' => 'currency.cny'],
			'COP' => ['code' => 'COP', 'symbol' => 'COP',	'precision' => 2, 'phrase' => 'currency.cop'],
			'CRC' => ['code' => 'CRC', 'symbol' => 'CRC',	'precision' => 2, 'phrase' => 'currency.crc'],
			'CVE' => ['code' => 'CVE', 'symbol' => 'CVE',	'precision' => 2, 'phrase' => 'currency.cve'],
			'CZK' => ['code' => 'CZK', 'symbol' => 'CZK',	'precision' => 2, 'phrase' => 'currency.czk'],
			'DJF' => ['code' => 'DJF', 'symbol' => 'DJF',	'precision' => 0, 'phrase' => 'currency.djf'],
			'DKK' => ['code' => 'DKK', 'symbol' => 'DKK',	'precision' => 2, 'phrase' => 'currency.dkk'],
			'DOP' => ['code' => 'DOP', 'symbol' => 'DOP',	'precision' => 2, 'phrase' => 'currency.dop'],
			'DZD' => ['code' => 'DZD', 'symbol' => 'DZD',	'precision' => 2, 'phrase' => 'currency.dzd'],
			'EGP' => ['code' => 'EGP', 'symbol' => 'EGP',	'precision' => 2, 'phrase' => 'currency.egp'],
			'ERN' => ['code' => 'ERN', 'symbol' => 'ERN',	'precision' => 2, 'phrase' => 'currency.ern'],
			'ETB' => ['code' => 'ETB', 'symbol' => 'ETB',	'precision' => 2, 'phrase' => 'currency.etb'],
			'EUR' => ['code' => 'EUR', 'symbol' => '€',		'precision' => 2, 'phrase' => 'currency.eur', 'fa' => 'fa-euro-sign'],
			'GBP' => ['code' => 'GBP', 'symbol' => '£',		'precision' => 2, 'phrase' => 'currency.gbp', 'fa' => 'fa-pound-sign'],
			'GEL' => ['code' => 'GEL', 'symbol' => 'GEL',	'precision' => 2, 'phrase' => 'currency.gel'],
			'GHS' => ['code' => 'GHS', 'symbol' => 'GHS',	'precision' => 2, 'phrase' => 'currency.ghs'],
			'GNF' => ['code' => 'GNF', 'symbol' => 'GNF',	'precision' => 0, 'phrase' => 'currency.gnf'],
			'GTQ' => ['code' => 'GTQ', 'symbol' => 'GTQ',	'precision' => 2, 'phrase' => 'currency.gtq'],
			'GYD' => ['code' => 'GYD', 'symbol' => 'GYD',	'precision' => 2, 'phrase' => 'currency.gyd'],
			'HKD' => ['code' => 'HKD', 'symbol' => 'HK$',	'precision' => 2, 'phrase' => 'currency.hkd'],
			'HNL' => ['code' => 'HNL', 'symbol' => 'HNL',	'precision' => 2, 'phrase' => 'currency.hnl'],
			'HRK' => ['code' => 'HRK', 'symbol' => 'HRK',	'precision' => 2, 'phrase' => 'currency.hrk'],
			'HUF' => ['code' => 'HUF', 'symbol' => 'HUF',	'precision' => 2, 'phrase' => 'currency.huf'],
			'IDR' => ['code' => 'IDR', 'symbol' => 'IDR',	'precision' => 2, 'phrase' => 'currency.idr'],
			'ILS' => ['code' => 'ILS', 'symbol' => '₪',     'precision' => 2, 'phrase' => 'currency.ils', 'fa' => 'fa-shekel-sign'],
			'INR' => ['code' => 'INR', 'symbol' => '₹',		'precision' => 2, 'phrase' => 'currency.inr', 'fa' => 'fa-rupee-sign'],
			'IQD' => ['code' => 'IQD', 'symbol' => 'IQD',	'precision' => 3, 'phrase' => 'currency.iqd'],
			'IRR' => ['code' => 'IRR', 'symbol' => 'IRR',	'precision' => 2, 'phrase' => 'currency.irr'],
			'ISK' => ['code' => 'ISK', 'symbol' => 'ISK',	'precision' => 0, 'phrase' => 'currency.isk'],
			'JMD' => ['code' => 'JMD', 'symbol' => 'JMD',	'precision' => 2, 'phrase' => 'currency.jmd'],
			'JOD' => ['code' => 'JOD', 'symbol' => 'JOD',	'precision' => 3, 'phrase' => 'currency.jod'],
			'JPY' => ['code' => 'JPY', 'symbol' => '¥',		'precision' => 0, 'phrase' => 'currency.jpy', 'fa' => 'fa-yen-sign'],
			'KES' => ['code' => 'KES', 'symbol' => 'KES',	'precision' => 2, 'phrase' => 'currency.kes'],
			'KHR' => ['code' => 'KHR', 'symbol' => 'KHR',	'precision' => 2, 'phrase' => 'currency.khr'],
			'KMF' => ['code' => 'KMF', 'symbol' => 'KMF',	'precision' => 0, 'phrase' => 'currency.kmf'],
			'KRW' => ['code' => 'KRW', 'symbol' => '₩',		'precision' => 0, 'phrase' => 'currency.krw', 'fa' => 'fa-won-sign'],
			'KWD' => ['code' => 'KWD', 'symbol' => 'KWD',	'precision' => 3, 'phrase' => 'currency.kwd'],
			'KZT' => ['code' => 'KZT', 'symbol' => '₸',     'precision' => 2, 'phrase' => 'currency.kzt', 'fa' => 'fa-tenge'],
			'LBP' => ['code' => 'LBP', 'symbol' => 'LBP',	'precision' => 2, 'phrase' => 'currency.lbp'],
			'LKR' => ['code' => 'LKR', 'symbol' => 'LKR',	'precision' => 2, 'phrase' => 'currency.lkr'],
			'LRD' => ['code' => 'LRD', 'symbol' => 'LRD',	'precision' => 2, 'phrase' => 'currency.lrd'],
			'LYD' => ['code' => 'LYD', 'symbol' => 'LYD',	'precision' => 3, 'phrase' => 'currency.lyd'],
			'MAD' => ['code' => 'MAD', 'symbol' => 'MAD',	'precision' => 2, 'phrase' => 'currency.mad'],
			'MDL' => ['code' => 'MDL', 'symbol' => 'MDL',	'precision' => 2, 'phrase' => 'currency.mdl'],
			'MGA' => ['code' => 'MGA', 'symbol' => 'MGA',	'precision' => 2, 'phrase' => 'currency.mga'],
			'MKD' => ['code' => 'MKD', 'symbol' => 'MKD',	'precision' => 2, 'phrase' => 'currency.mkd'],
			'MMK' => ['code' => 'MMK', 'symbol' => 'MMK',	'precision' => 2, 'phrase' => 'currency.mmk'],
			'MOP' => ['code' => 'MOP', 'symbol' => 'MOP',	'precision' => 2, 'phrase' => 'currency.mop'],
			'MUR' => ['code' => 'MUR', 'symbol' => 'MUR',	'precision' => 2, 'phrase' => 'currency.mur'],
			'MXN' => ['code' => 'MXN', 'symbol' => 'MX$',	'precision' => 2, 'phrase' => 'currency.mxn'],
			'MYR' => ['code' => 'MYR', 'symbol' => 'MYR',	'precision' => 2, 'phrase' => 'currency.myr'],
			'MZN' => ['code' => 'MZN', 'symbol' => 'MZN',	'precision' => 2, 'phrase' => 'currency.mzn'],
			'NAD' => ['code' => 'NAD', 'symbol' => 'NAD',	'precision' => 2, 'phrase' => 'currency.nad'],
			'NGN' => ['code' => 'NGN', 'symbol' => 'NGN',	'precision' => 2, 'phrase' => 'currency.ngn'],
			'NIO' => ['code' => 'NIO', 'symbol' => 'NIO',	'precision' => 2, 'phrase' => 'currency.nio'],
			'NOK' => ['code' => 'NOK', 'symbol' => 'NOK',	'precision' => 2, 'phrase' => 'currency.nok'],
			'NPR' => ['code' => 'NPR', 'symbol' => 'NPR',	'precision' => 2, 'phrase' => 'currency.npr'],
			'NZD' => ['code' => 'NZD', 'symbol' => 'NZ$',	'precision' => 2, 'phrase' => 'currency.nzd'],
			'OMR' => ['code' => 'OMR', 'symbol' => 'OMR',	'precision' => 3, 'phrase' => 'currency.omr'],
			'PAB' => ['code' => 'PAB', 'symbol' => 'PAB',	'precision' => 2, 'phrase' => 'currency.pab'],
			'PEN' => ['code' => 'PEN', 'symbol' => 'PEN',	'precision' => 2, 'phrase' => 'currency.pen'],
			'PHP' => ['code' => 'PHP', 'symbol' => 'PHP',	'precision' => 2, 'phrase' => 'currency.php'],
			'PKR' => ['code' => 'PKR', 'symbol' => 'PKR',	'precision' => 2, 'phrase' => 'currency.pkr'],
			'PLN' => ['code' => 'PLN', 'symbol' => 'PLN',	'precision' => 2, 'phrase' => 'currency.pln'],
			'PYG' => ['code' => 'PYG', 'symbol' => 'PYG',	'precision' => 0, 'phrase' => 'currency.pyg'],
			'QAR' => ['code' => 'QAR', 'symbol' => 'QAR',	'precision' => 2, 'phrase' => 'currency.qar'],
			'RON' => ['code' => 'RON', 'symbol' => 'RON',	'precision' => 2, 'phrase' => 'currency.ron'],
			'RSD' => ['code' => 'RSD', 'symbol' => 'RSD',	'precision' => 2, 'phrase' => 'currency.rsd'],
			'RUB' => ['code' => 'RUB', 'symbol' => '₽',   	'precision' => 2, 'phrase' => 'currency.rub', 'fa' => 'fa-ruble-sign'],
			'RWF' => ['code' => 'RWF', 'symbol' => 'RWF',	'precision' => 0, 'phrase' => 'currency.rwf'],
			'SAR' => ['code' => 'SAR', 'symbol' => 'SAR',	'precision' => 2, 'phrase' => 'currency.sar'],
			'SDG' => ['code' => 'SDG', 'symbol' => 'SDG',	'precision' => 2, 'phrase' => 'currency.sdg'],
			'SEK' => ['code' => 'SEK', 'symbol' => 'SEK',	'precision' => 2, 'phrase' => 'currency.sek'],
			'SGD' => ['code' => 'SGD', 'symbol' => 'SGD',	'precision' => 2, 'phrase' => 'currency.sgd'],
			'SOS' => ['code' => 'SOS', 'symbol' => 'SOS',	'precision' => 2, 'phrase' => 'currency.sos'],
			'STD' => ['code' => 'STD', 'symbol' => 'STD',	'precision' => 2, 'phrase' => 'currency.std'],
			'SYP' => ['code' => 'SYP', 'symbol' => 'SYP',	'precision' => 2, 'phrase' => 'currency.syp'],
			'THB' => ['code' => 'THB', 'symbol' => '฿',		'precision' => 2, 'phrase' => 'currency.thb'],
			'TND' => ['code' => 'TND', 'symbol' => 'TND',	'precision' => 3, 'phrase' => 'currency.tnd'],
			'TOP' => ['code' => 'TOP', 'symbol' => 'TOP',	'precision' => 2, 'phrase' => 'currency.top'],
			'TRY' => ['code' => 'TRY', 'symbol' => 'TRY',	'precision' => 2, 'phrase' => 'currency.try', 'fa' => 'fa-lira-sign'],
			'TTD' => ['code' => 'TTD', 'symbol' => 'TTD',	'precision' => 2, 'phrase' => 'currency.ttd'],
			'TWD' => ['code' => 'TWD', 'symbol' => 'NT$',	'precision' => 2, 'phrase' => 'currency.twd'],
			'TZS' => ['code' => 'TZS', 'symbol' => 'TZS',	'precision' => 2, 'phrase' => 'currency.tzs'],
			'UAH' => ['code' => 'UAH', 'symbol' => '₴',     'precision' => 2, 'phrase' => 'currency.uah', 'fa' => 'fa-hryvnia'],
			'UGX' => ['code' => 'UGX', 'symbol' => 'UGX',	'precision' => 0, 'phrase' => 'currency.ugx'],
			'USD' => ['code' => 'USD', 'symbol' => '$',		'precision' => 2, 'phrase' => 'currency.usd', 'fa' => 'fa-dollar-sign'],
			'UYU' => ['code' => 'UYU', 'symbol' => 'UYU',	'precision' => 2, 'phrase' => 'currency.uyu'],
			'UZS' => ['code' => 'UZS', 'symbol' => 'UZS',	'precision' => 2, 'phrase' => 'currency.uzs'],
			'VEF' => ['code' => 'VEF', 'symbol' => 'VEF',	'precision' => 2, 'phrase' => 'currency.vef'],
			'VND' => ['code' => 'VND', 'symbol' => '₫',		'precision' => 0, 'phrase' => 'currency.vnd'],
			'XAF' => ['code' => 'XAF', 'symbol' => 'FCFA',	'precision' => 0, 'phrase' => 'currency.xaf'],
			'XOF' => ['code' => 'XOF', 'symbol' => 'CFA',	'precision' => 0, 'phrase' => 'currency.xof'],
			'YER' => ['code' => 'YER', 'symbol' => 'YER',	'precision' => 2, 'phrase' => 'currency.yer'],
			'ZAR' => ['code' => 'ZAR', 'symbol' => 'ZAR',	'precision' => 2, 'phrase' => 'currency.zar'],
			'ZMK' => ['code' => 'ZMK', 'symbol' => 'ZMK',	'precision' => 0, 'phrase' => 'currency.zmk']
		];
	}

	protected function getCurrency(&$currencyCode)
	{
		$currencyCode = strtoupper($currencyCode);
		$data = $this->getCurrencyData();

		return isset($data[$currencyCode]) ? $data[$currencyCode] : null;
	}

	public function getCurrencySymbol($currencyCode)
	{
		$currency = $this->getCurrency($currencyCode);

		return $currency ? $currency['symbol'] : '';
	}

	public function getCurrencyFa($currencyCode)
	{
		$currency = $this->getCurrency($currencyCode);

		return isset($currency['fa']) ? $currency['fa'] : '';
	}

	public function getCurrencyFormats()
	{
		return [
			'{symbol}{value}',
			'{symbol} {value}',
			'{value}{symbol}',
			'{value} {symbol}'
		];
	}

	public function getCurrencyFormatExamples(\XF\Language $language = null)
	{
		$language = $language ?: \XF::language();

		$currencyFormats = [];
		foreach ($this->getCurrencyFormats() AS $currencyFormat)
		{
			$currencyFormats[$currencyFormat] = $this->languageFormat('1234.50', 'USD', $language, $currencyFormat);
		}

		return $currencyFormats;
	}
}