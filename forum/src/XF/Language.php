<?php

namespace XF;

use XF\Db\AbstractAdapter;

class Language implements \ArrayAccess
{
	protected $id;
	protected $options = [
		'title' => '',
		'language_code' => '',
		'date_format' => 'M j, Y',
		'time_format' => 'g:i A',
		'currency_format' => '{symbol}{value}',
		'decimal_point' => '.',
		'thousands_separator' => ',',
		'text_direction' => 'LTR',
		'week_start' => 0,
		'label_separator' => ':',
		'comma_separator' => ', ',
		'ellipsis' => '...',
		'parenthesis_open' => '(',
		'parenthesis_close' => ')',
	];

	protected static $modifierMap = [
		':' => 'label_separator',
		',' => 'comma_separator',
		'...' => 'ellipsis',
		'(' => 'parenthesis_open',
		')' => 'parenthesis_close'
	];

	/**
	 * @var Db\AbstractAdapter
	 */
	protected $db;

	protected $groupPath;

	/**
	 * @var array
	 */
	protected $phraseCache = [];
	protected $phrasesToLoad = [];
	protected $groupsCached = [];

	/**
	 * @var \DateTime
	 */
	protected $date;

	protected $dayStartTimestamps = null;

	/**
	 * Translate a numeric day of the week to representation that will be used in phrases.
	 *
	 * @var array
	 */
	protected $dowTranslation = [
		0 => 'sunday',
		1 => 'monday',
		2 => 'tuesday',
		3 => 'wednesday',
		4 => 'thursday',
		5 => 'friday',
		6 => 'saturday'
	];

	public function __construct($id, array $options, AbstractAdapter $db, $groupPath, array $phrases = null)
	{
		if (isset($options['phrase_cache']))
		{
			$cache = $options['phrase_cache'];
			if (is_array($cache) && $phrases === null)
			{
				$phrases = $cache;
			}
			unset($options['phrase_cache']);
		}

		$this->id = $id;
		$this->options = array_merge($this->options, $options);
		$this->db = $db;
		$this->groupPath = $groupPath;

		if (is_array($phrases))
		{
			$this->phraseCache = $phrases;
		}

		$this->date = new \DateTime('now', new \DateTimeZone('UTC'));
	}

	public function getId()
	{
		return $this->id;
	}

	public function phrase($name, array $params = [], $preLoad = true, $allowHtml = true)
	{
		if ($preLoad
			&& !isset($this->phraseCache[$name])
			&& !isset($this->phrasesToLoad[$name])
			&& strpos($name, '.') === false
		)
		{
			$this->phrasesToLoad[$name] = true;
		}

		return new Phrase($this, $name, $params, $allowHtml);
	}

	public function renderPhrase($name, array $params = [], $context = 'html', array $options = [])
	{
		$options = array_replace([
			'fallback' => null,
			'fallbackRaw' => false,
			'nameOnInvalid' => true
		], $options);

		$originalName = $name;

		// Process phrase name prefixes/suffixes.
		// Note that there is very similar code in XF\Template\Compiler\Func\Phrase. It should correspond.
		$prefixes = [];
		$suffixes = [];

		if ($name[0] == '(')
		{
			$prefixes[] = $this->options['parenthesis_open'];
			$name = substr($name, 1);
		}

		do
		{
			$matchedSuffix = false;

			if (substr($name, -3) == '...')
			{
				$suffixes[] = $this->options['ellipsis'];
				$name = substr($name, 0, -3);
				$matchedSuffix = true;
			}
			else
			{
				$lastChar = substr($name, -1);
				switch ($lastChar)
				{
					case ':':
					case ',':
					case ')':
					case '(':
						if (isset(self::$modifierMap[$lastChar]))
						{
							$suffixes[] = $this->options[self::$modifierMap[$lastChar]];
						}
						$matchedSuffix = true;
						$name = substr($name, 0, -1);
						break;
				}
			}
		}
		while ($matchedSuffix);

		$text = $this->getPhraseText($name);
		if ($text === false)
		{
			// phrase not found
			if ($options['fallback'] !== null)
			{
				if ($options['fallbackRaw'])
				{
					$text = $options['fallback'];
				}
				else
				{
					$text = \XF::escapeString($options['fallback'], $context);
				}
			}
			else if ($options['nameOnInvalid'])
			{
				$text = \XF::escapeString($name, $context);
			}
			else
			{
				// if not returning anything, don't apply any prefixes/suffixes
				return '';
			}
		}
		else if ($params)
		{
			$text = preg_replace_callback('/\{([a-z0-9_-]+)\}/i', function(array $match) use ($params, $context)
			{
				$paramName = $match[1];

				if (!array_key_exists($paramName, $params))
				{
					return $match[0];
				}

				$param = $params[$paramName];
				if ($param instanceof Phrase)
				{
					return $param->render($context);
				}
				else
				{
					return \XF::escapeString($param, $context);
				}
			}, $text);
		}

		if ($prefixes)
		{
			$text = implode('', $prefixes) . $text;
		}
		if ($suffixes)
		{
			// we process these right to left so invert them
			$suffixes = array_reverse($suffixes);
			$text .= implode('', $suffixes);
		}

		return $text;
	}

	public function getPhraseText($name)
	{
		if (isset($this->phraseCache[$name]))
		{
			return $this->phraseCache[$name];
		}

		$parts = explode('.', $name, 2);
		if (isset($parts[1]) && $this->loadPhraseGroup($parts[0]))
		{
			// group has been cached so everything should be in it - if it's not, it's invalid
			if (!isset($this->phraseCache[$name]))
			{
				$this->phraseCache[$name] = false;
			}

			return $this->phraseCache[$name];
		}

		$this->phrasesToLoad[$name] = true;
		$this->loadPhrases();

		if (!isset($this->phraseCache[$name]))
		{
			$this->phraseCache[$name] = false;
		}

		return $this->phraseCache[$name];
	}

	protected function loadPhraseGroup($group)
	{
		if (isset($this->groupsCached[$group]))
		{
			return $this->groupsCached[$group];
		}

		if (preg_match('/[^a-z0-9_]/i', $group))
		{
			throw new \InvalidArgumentException("Phrase group $group is not a valid format");
		}

		$file = $this->groupPath . "/l$this->id/$group.php";
		if ($this->groupPath && file_exists($file) && is_readable($file))
		{
			$this->phraseCache = array_merge($this->phraseCache, include($file));
			$this->groupsCached[$group] = true;
		}
		else
		{
			$this->groupsCached[$group] = false;
		}

		return $this->groupsCached[$group];
	}

	protected function getPhraseCacheRaw($name)
	{
		if (isset($this->phraseCache[$name]))
		{
			return $this->phraseCache[$name];
		}
		else
		{
			return $name;
		}
	}

	public function cachePhrase($name, $value)
	{
		$this->phraseCache[$name] = $value;
	}

	public function cachePhrases(array $phrases)
	{
		$this->phraseCache = array_merge($this->phraseCache, $phrases);
	}

	public function uncachePhrase($name)
	{
		if (strpos($name, '.') === false)
		{
			unset($this->phraseCache[$name]);
		}
	}

	public function isGroupCached($group)
	{
		return isset($this->groupsCached[$group]);
	}

	public function uncacheGroup($group)
	{
		unset($this->groupsCached[$group]);
	}

	public function requirePhrases(array $phrases)
	{
		foreach ($phrases AS $name)
		{
			if (!isset($this->phraseCache[$name])
				&& !isset($this->phrasesToLoad[$name])
				&& strpos($name, '.') === false
			)
			{
				$this->phrasesToLoad[$name] = true;
			}
		}
	}

	protected function loadPhrases()
	{
		if (!$this->phrasesToLoad)
		{
			return;
		}

		$phrases = $this->db->fetchPairs("
			SELECT title, phrase_text
			FROM xf_phrase_compiled
			WHERE language_id = ?
				AND title IN (" . $this->db->quote(array_keys($this->phrasesToLoad)) . ")
		", $this->id);

		foreach ($phrases AS $title => $text)
		{
			$this->phraseCache[$title] = $text;
		}

		$this->phrasesToLoad = [];
	}

	public function getTitle()
	{
		return $this->options['title'];
	}

	public function getLanguageCode()
	{
		return $this->options['language_code'];
	}

	public function getTextDirection()
	{
		return $this->options['text_direction'];
	}

	public function isRtl()
	{
		return ($this->options['text_direction'] == 'RTL');
	}

	public function offsetGet($key)
	{
		return $this->options[$key];
	}

	public function __get($key)
	{
		return $this->offsetGet($key);
	}

	public function offsetExists($key)
	{
		return isset($this->options[$key]);
	}

	public function __isset($key)
	{
		return $this->offsetExists($key);
	}

	public function offsetSet($key, $value)
	{
		throw new \LogicException("Language object cannot be written to.");
	}

	public function offsetUnset($key)
	{
		throw new \LogicException("Language object cannot be written to.");
	}

	public function setTimeZone($tz)
	{
		if (!($tz instanceof \DateTimeZone))
		{
			try
			{
				$tz = new \DateTimeZone($tz);
			}
			catch (\Exception $e)
			{
				return false;
			}
		}

		$this->date->setTimezone($tz);
		return true;
	}

	/**
	 * @return \DateTimeZone
	 */
	public function getTimeZone()
	{
		return $this->date->getTimezone();
	}

	protected function formatDateTime(\DateTime $date, $format)
	{
		if (!$date)
		{
			$date = $this->date;
		}

		$dateParts = explode('|', $date->format('j|w|W|n|Y|G|i|s|S'));
		list($dayOfMonth, $dayOfWeek, $weekOfYear, $month, $year, $hour, $minute, $second, $ordinalSuffix) = $dateParts;

		$output = '';

		$formatters = str_split($format);
		$formatterCount = count($formatters);
		for ($i = 0; $i < $formatterCount; $i++)
		{
			$identifier = $formatters[$i];

			switch ($identifier)
			{
				// day of month
				case 'd': $output .= sprintf('%02d', $dayOfMonth); break;
				case 'j': $output .= $dayOfMonth; break;

				// day of week
				case 'D': $output .= $this->getPhraseCacheRaw('day_' . $this->dowTranslation[$dayOfWeek] . '_short'); break;
				case 'l': $output .= $this->getPhraseCacheRaw('day_' . $this->dowTranslation[$dayOfWeek]); break;

				// week
				case 'w': $output .= $dayOfWeek; break;
				case 'W': $output .= $weekOfYear; break;

				// month
				case 'm': $output .= sprintf('%02d', $month); break;
				case 'n': $output .= $month; break;
				case 'F': $output .= $this->getPhraseCacheRaw('month_' . $month); break;
				case 'M': $output .= $this->getPhraseCacheRaw('month_' . $month . '_short'); break;

				// year
				case 'Y': $output .= $year; break;
				case 'y': $output .= substr($year, 2); break;

				// am/pm
				case 'a': $output .= $this->getPhraseCacheRaw(($hour >= 12 ? 'time_pm_lower' : 'time_am_lower')); break;
				case 'A': $output .= $this->getPhraseCacheRaw(($hour >= 12 ? 'time_pm_upper' : 'time_am_upper')); break;

				// hour
				case 'H': $output .= sprintf('%02d', $hour); break;
				case 'h': $output .= sprintf('%02d', $hour % 12 ? $hour % 12 : 12); break;
				case 'G': $output .= $hour; break;
				case 'g': $output .= ($hour % 12 ? $hour % 12 : 12); break;

				// minute
				case 'i': $output .= $minute; break;

				// second
				case 's': $output .= $second; break;

				// ordinal
				case 'S': $output .= $ordinalSuffix; break;

				case '\\':
					$i++;
					if ($i < $formatterCount)
					{
						$output .= $formatters[$i];
					}
					break;

				// fallback to PHP formatter directly - shouldn't be used regularly
				case 'N':
				case 'z':
				case 't':
				case 'L':
				case 'o':
				case 'B':
				case 'u':
				case 'v':
				case 'e':
				case 'I':
				case 'O':
				case 'P':
				case 'T':
				case 'Z':
				case 'c':
				case 'r':
				case 'U':
					$output .= $date->format($identifier);
				break;

				// anything else is printed
				default: $output .= $identifier;
			}
		}

		return $output;
	}

	public function date($timestamp, $format = null)
	{
		if ($timestamp instanceof \DateTime)
		{
			$date = $timestamp;
		}
		else
		{
			$date = $this->date->setTimestamp($timestamp);
		}

		switch ($format)
		{
			case 'year':
				$dateFormat = 'Y';
				break;

			case 'monthDay':
				$dateFormat = 'F j';
				break;

			case 'picker':
				$dateFormat = 'Y-m-d';
				break;

			case 'absolute':
			case '':
				$dateFormat = $this->options['date_format'];
				break;

			default:
				$dateFormat = $format;
		}

		return $this->formatDateTime($date, $dateFormat);
	}

	public function time($timestamp, $format = null)
	{
		if ($timestamp instanceof \DateTime)
		{
			$date = $timestamp;
		}
		else
		{
			$date = $this->date->setTimestamp($timestamp);
		}

		switch ($format)
		{
			case 'absolute':
			case '':
				$dateFormat = $this->options['time_format'];
				break;

			default:
				$dateFormat = $format;
		}

		return $this->formatDateTime($date, $dateFormat);
	}

	public function dateTime($timestamp)
	{
		list($date, $time) = $this->getDateTimeParts($timestamp);
		return $this->getDateTimeOutput($date, $time);
	}

	public function getDateTimeParts($timestamp)
	{
		if ($timestamp instanceof \DateTime)
		{
			$date = $timestamp;
		}
		else
		{
			$date = $this->date->setTimestamp($timestamp);
		}

		$dateTimeFormat = $this->options['date_format'] . '|' . $this->options['time_format'];
		return explode('|', $this->formatDateTime($date, $dateTimeFormat));
	}

	public function getDateTimeOutput($date, $time)
	{
		return strtr($this->getPhraseCacheRaw('date_x_at_time_y'), [
			'{date}' => $date,
			'{time}' => $time
		]);
	}

	public function getRelativeDateTimeOutput($timestamp, $date, $time, $getFullDate = false)
	{
		$timeRef = $this->getDayStartTimestamps();

		$interval = $timeRef['now'] - $timestamp;

		if ($interval < -2)
		{
			// future date
			$futureInterval = $timestamp - $timeRef['now'];

			if ($futureInterval < 60)
			{
				return $this->getPhraseCacheRaw('in_a_moment');
			}
			else if ($futureInterval < 120)
			{
				return $this->getPhraseCacheRaw('in_a_minute');
			}
			else if ($futureInterval < 3600)
			{
				return strtr($this->getPhraseCacheRaw('in_x_minutes'), [
					'{minutes}' => floor($futureInterval / 60)
				]);
			}
			else if ($timestamp < $timeRef['tomorrow'])
			{
				// today
				return strtr($this->getPhraseCacheRaw('later_today_at_x'), [
					'{time}' => $time
				]);
			}
			else if ($timestamp < $timeRef['tomorrow'] + 86400)
			{
				// tomorrow
				return strtr($this->getPhraseCacheRaw('tomorrow_at_x'), [
					'{time}' => $time
				]);
			}
			else if ($futureInterval < (7 * 86400))
			{
				// after tomorrow
				return $this->getDateTimeOutput($date, $time);
			}
			else if ($getFullDate)
			{
				return $this->getDateTimeOutput($date, $time);
			}
			else
			{
				// after the next 7 days
				return $date;
			}
		}
		else if ($interval <= 60)
		{
			return $this->getPhraseCacheRaw('a_moment_ago');
		}
		else if ($interval <= 120)
		{
			return $this->getPhraseCacheRaw('one_minute_ago');
		}
		else if ($interval < 3600)
		{
			return strtr($this->getPhraseCacheRaw('x_minutes_ago'), [
				'{minutes}' => floor($interval / 60)
			]);
		}
		else if ($timestamp >= $timeRef['today'])
		{
			return strtr($this->getPhraseCacheRaw('today_at_x'), [
				'{time}' => $time
			]);
		}
		else if ($timestamp >= $timeRef['yesterday'])
		{
			return strtr($this->getPhraseCacheRaw('yesterday_at_x'), [
				'{time}' => $time
			]);
		}
		else if ($timestamp >= $timeRef['week'])
		{
			$dow = $timeRef['todayDow'] - ceil(($timeRef['today'] - $timestamp) / 86400);
			if ($dow < 0)
			{
				$dow += 7;
			}

			$day = $this->getPhraseCacheRaw('day_' . $this->dowTranslation[$dow]);

			return strtr($this->getPhraseCacheRaw('day_x_at_time_y'), [
				'{day}' => $day,
				'{time}' => $time
			]);
		}
		else if ($getFullDate)
		{
			return $this->getDateTimeOutput($date, $time);
		}
		else
		{
			return $date;
		}
	}

	public function getDayStartTimestamps()
	{
		if (!$this->dayStartTimestamps)
		{
			$date = new \DateTime('@' . \XF::$time);
			$date->setTimezone($this->getTimeZone());
			$date->setTime(0, 0, 0);

			list($todayStamp, $todayDow) = explode('|', $date->format('U|w'));

			$date->modify('+1 day');
			$tomorrowStamp = $date->format('U');

			$date->modify('-2 days');
			$yesterdayStamp = $date->format('U');

			$date->modify('-5 days');
			$weekStamp = $date->format('U');

			$this->dayStartTimestamps = [
				'tomorrow'  => $tomorrowStamp,
				'now'       => \XF::$time,
				'today'     => $todayStamp,
				'todayDow'  => $todayDow,
				'yesterday' => $yesterdayStamp,
				'week'      => $weekStamp
			];
		}

		return $this->dayStartTimestamps;
	}

	protected function _getDatePresets()
	{
		return [
			'1day' => ['-1 day', '1_day_ago'],
			'1week' => ['-1 week', '1_week_ago'],
			'2weeks' => ['-2 weeks', '2_weeks_ago'],
			'1month' => ['-1 month', '1_month_ago'],
			'3months' => ['-3 months', '3_months_ago'],
			'6months' => ['-6 months', '6_months_ago'],
			'9months' => ['-9 months', '9_months_ago'],
			'1year' => ['-1 year', '1_year_ago'],
			'2years' => ['-2 years', '2_years_ago'],
		];
	}

	public function getDatePresets($timeStamp = null)
	{
		if (is_null($timeStamp))
		{
			$timeStamp = \XF::$time;
		}

		$presets = [];

		foreach ($this->_getDatePresets() AS $period => $presetData)
		{
			$date = new \DateTime('@' . $timeStamp);
			$date->modify($presetData[0]);
			$presets[$date->format('Y-m-d')] = \XF::phrase($presetData[1]);
		}

		return $presets;
	}

	/**
	 * Formats the given number for a language/locale. Also used for file size formatting.
	 *
	 * @param float|integer $number Number to format
	 * @param integer|string $precision Number of places to show after decimal point or word "size" for file size
	 *
	 * @return string Formatted number
	 */
	public function numberFormat($number, $precision = 0)
	{
		return number_format($number, $precision,
			$this->options['decimal_point'], $this->options['thousands_separator']
		);
	}

	public function shortNumberFormat($number, $precision = 0)
	{
		$originalNumber = $number;
		$decimalSep = $this->options['decimal_point'];
		$thousandsSep = $this->options['thousands_separator'];

		if ($number >= 1000)
		{
			// Round number to the nearest 1000 with relevant precision
			$basePrecision = -3;
			$number = round($number, $basePrecision + $precision);
		}

		$phrase = null;

		if ($number >= 1000000000) // 1B
		{
			$number = number_format($number / 1000000000, $precision, $decimalSep, $thousandsSep);
			$phrase = 'x_b';
		}
		elseif ($number >= 1000000) // 1M - 999M
		{
			$number = number_format($number / 1000000, $precision, $decimalSep, $thousandsSep);
			$phrase = 'x_m';
		}
		elseif ($number >= 1000) // 1K - 999K
		{
			$number = number_format($number / 1000, $precision, $decimalSep, $thousandsSep);
			$phrase = 'x_k';
		}

		// This is the original full-form formatted number
		// we can return this if the number is unphrased anyway and also
		// pass it into the phrase to allow user to opt out of the short format.
		$default = $this->numberFormat($originalNumber, $precision);

		// return $number, not $number.0 when the decimal is 0.
		if (substr($number, -2) === "{$decimalSep}0")
		{
			$number = substr($number, 0, -2);
		}
		if (substr($default, -2) === "{$decimalSep}0")
		{
			$default = substr($default, 0, -2);
		}

		if ($phrase)
		{
			return str_replace(['{number}', '{default}'], [$number, $default], $this->getPhraseCacheRaw($phrase));
		}
		else
		{
			return $default;
		}
	}

	public function currencyFormat($value, $symbol, $precision = 2, $format = null)
	{
		$format = $format ?: $this->options['currency_format'];
		return strtr($format, [
			'{symbol}' => $symbol,
			'{value}' => $this->numberFormat($value, $precision)
		]);
	}

	public function fileSizeFormat($number)
	{
		$decimalSep = $this->options['decimal_point'];
		$thousandsSep = $this->options['thousands_separator'];

		if ($number >= 1099511627776) // 1 TB
		{
			$number = number_format($number / 1099511627776, 1, $decimalSep, $thousandsSep);
			$phrase = 'x_tb';
		}
		else if ($number >= 1073741824) // 1 GB
		{
			$number = number_format($number / 1073741824, 1, $decimalSep, $thousandsSep);
			$phrase = 'x_gb';
		}
		else if ($number >= 1048576) // 1 MB
		{
			$number = number_format($number / 1048576, 1, $decimalSep, $thousandsSep);
			$phrase = 'x_mb';
		}
		else if ($number >= 1024) // 1 KB
		{
			$number = number_format($number / 1024, 1, $decimalSep, $thousandsSep);
			$phrase = 'x_kb';
		}
		else
		{
			$number = number_format($number, 0, $decimalSep, $thousandsSep);
			$phrase = 'x_bytes';
		}

		// return $number, not $number.0 when the decimal is 0.
		if (substr($number, -2) === "{$decimalSep}0")
		{
			$number = substr($number, 0, -2);
		}

		return str_replace('{size}', $number, $this->getPhraseCacheRaw($phrase));
	}
}