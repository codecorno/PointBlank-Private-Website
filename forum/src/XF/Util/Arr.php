<?php

namespace XF\Util;

/**
 * Basic array utility functions. These replace or extend primitive-level functions
 * and thus can be called statically.
 */
class Arr
{
	/**
	 * Returns a version of the input $data that contains only the array keys defined in $keys
	 *
	 * Example: arrayFilterKeys(['a' => 1, 'b' => 2, 'c' => 3), ['b', 'c'])
	 * Returns: ['b' => 2, 'c' => 3]
	 *
	 * @param array $data
	 * @param array $keys
	 *
	 * @return array $data
	 */
	public static function arrayFilterKeys(array $data, array $keys, $checkIsSet = false)
	{
		// this version will not warn on undefined indexes:
		// return array_intersect_key($data, array_flip($keys));

		$array = [];

		foreach ($keys AS $key)
		{
			if ($checkIsSet)
			{
				if (!isset($data[$key]))
				{
					continue;
				}
			}
			$array[$key] = $data[$key];
		}

		return $array;
	}

	/**
	 * This is a simplified version of a function similar to array_merge_recursive. It is
	 * designed to recursively merge associative arrays (maps). If each array shares a key,
	 * that key is recursed and the child keys are merged.
	 *
	 * This function does not handle merging of non-associative arrays (numeric keys) as
	 * a special case.
	 *
	 * More than 2 arguments may be passed if desired.
	 *
	 * @param array $first
	 * @param array $second
	 *
	 * @return array
	 */
	public static function mapMerge(array $first, array $second)
	{
		$args = func_get_args();
		unset($args[0]);

		foreach ($args AS $arg)
		{
			if (!is_array($arg) || !$arg)
			{
				continue;
			}
			foreach ($arg AS $key => $value)
			{
				if (is_array($value) && isset($first[$key]) && is_array($first[$key]))
				{
					$first[$key] = self::mapMerge($first[$key], $value);
				}
				else
				{
					$first[$key] = $value;
				}
			}
		}

		return $first;
	}

	/**
	 * Recursively returns the difference between two associative arrays.
	 * Returns any key that is in array 1 but not 2; returns any value (from
	 * array 1) where the value is different in array 2.
	 *
	 * @param array $array1
	 * @param array $array2
	 * @return array
	 */
	public static function mapDiff(array $array1, array $array2)
	{
		$diff = [];

		foreach ($array1 AS $key => $value)
		{
			if (
				!array_key_exists($key, $array2) // not in the other
				|| (is_array($value) && !is_array($array2[$key])) // different type
				|| (!is_array($value) && $value !== $array2[$key]) // not equal
			)
			{
				$diff[$key] = $value;
			}
			else if (is_array($value)) // $array2[$key] will be an array as well
			{
				$result = self::mapDiff($value, $array2[$key]);
				if ($result)
				{
					$diff[$key] = $result;
				}
			}
		}

		return $diff;
	}

	/**
	 * @deprecated No longer required as of XF 2.1.0 as all installations now run PHP 5.5+
	 */
	public static function arrayColumn(array $array, $column, $index = null)
	{
		return array_column($array, $column, $index);
	}

	public static function columnSort(array $values, $column, $cmpFn = null)
	{
		/** @var \Closure|null $cmpFn */

		$f = function($a1, $a2) use ($column, $cmpFn)
		{
			$exists1 = isset($a1[$column]);
			$exists2 = isset($a2[$column]);

			if ($exists1 && !$exists2)
			{
				return 1;
			}
			else if (!$exists1 && $exists2)
			{
				return -1;
			}
			else if (!$exists1 && !$exists2)
			{
				return 0;
			}

			$v1 = $a1[$column];
			$v2 = $a2[$column];

			if ($cmpFn)
			{
				return $cmpFn($v1, $v2);
			}

			if ($v1 == $v2)
			{
				return 0;
			}
			else
			{
				return $v1 > $v2 ? 1 : -1;
			}
		};

		uasort($values, $f);
		return $values;
	}

	/**
	 * Useful if we need to sort an array of strings
	 * alphabetically in a case-insensitive way.
	 *
	 * @param array $values
	 *
	 * @return array
	 */
	public static function deaccentSort(array $values)
	{
		uasort($values, function($a, $b)
		{
			$a = utf8_romanize(utf8_deaccent($a));
			$b = utf8_romanize(utf8_deaccent($b));
			return strcmp($a, $b);
		});

		return $values;
	}

	public static function arrayFilterArgs($array, $callable, $args = null)
	{
		if (!is_callable($callable))
		{
			return $array;
		}

		if ($args === null)
		{
			return array_filter($array, $callable);
		}

		if (!is_array($args))
		{
			$args = array_slice(func_get_args(), 2);
		}

		foreach ($array AS $key => $value)
		{
			if (call_user_func_array($callable, array_merge([$value], $args)) === false)
			{
				unset ($array[$key]);
			}
		}

		return $array;
	}

	public static function arrayGroup($array, $groupers)
	{
		if (!is_array($groupers))
		{
			$groupers = [$groupers];
		}

		if (!$groupers)
		{
			throw new \InvalidArgumentException("Must have at least one grouper");
		}

		$groupBy = array_shift($groupers);

		$grouped = [];
		foreach ($array AS $k => $v)
		{
			if ($groupBy instanceof \Closure)
			{
				$groupValue = $groupBy($v, $k);
			}
			else
			{
				$groupValue = $v[$groupBy];
			}
			$grouped[$groupValue][$k] = $v;
		}

		if ($groupers)
		{
			foreach ($grouped AS $k => $reGroup)
			{
				$grouped[$k] = self::arrayGroup($reGroup, $groupers);
			}
		}

		return $grouped;
	}

	/**
	 * Parses a query string (x=y&a=b&c[]=d) into a structured array format.
	 *
	 * Note that this can handle very long query strings, but it has problems
	 * if there are conflicting elements that split the "chunks" that are made
	 * internally. Workaround this using distinct keys for each input whenever possible.
	 *
	 * @param string $string
	 *
	 * @return array
	 */
	public static function parseQueryString($string)
	{
		$max = intval(@ini_get('max_input_vars'));
		if ($max && substr_count($string, '&') >= $max)
		{
			$partCounter = [];
			$string = preg_replace_callback('/(?<=^|&)([^=&]+)(\\[\\]|%5B%5D)/U', function(array $match) use(&$partCounter)
			{
				$key = $match[1];
				if (!isset($partCounter[$key]))
				{
					$partCounter[$key] = 0;
				}

				$output = $key . '[' . $partCounter[$key] . ']';
				$partCounter[$key]++;

				return $output;
			}, $string);

			$chunks = array_chunk(explode('&', $string), $max, true);

			$output = [];
			foreach ($chunks AS $chunk)
			{
				parse_str(implode('&', $chunk), $values);
				$output = self::mapMerge($output, $values);
			}
		}
		else
		{
			parse_str($string, $output);
		}
		return $output;
	}

	public static function arrayDelete($needles, $haystack)
	{
		if (!$haystack)
		{
			return [];
		}
		foreach ((array)$needles AS $needle)
		{
			if (($key = array_search($needle, $haystack)) !== false)
			{
				unset($haystack[$key]);
			}
		}
		return $haystack;
	}

	public static function arrayKeyIsearch($key, $array)
	{
		$keys = array_keys($array);

		$index = array_search(strtolower($key), array_map('strtolower', $keys));

		if ($index !== false)
		{
			return $keys[$index];
		}

		return false;
	}

	/**
	 * Removes null values from the array.
	 *
	 * @param array $array
	 *
	 * @return array
	 */
	public static function filterNull(array $array)
	{
		return array_filter($array, function($v)
		{
			return ($v !== null);
		});
	}

	/**
	 * Split a string to an array based on pattern. Defaults to space/line break pattern.
	 *
	 * @param $string
	 * @param string $pattern
	 * @param int $limit
	 *
	 * @return array
	 */
	public static function stringToArray($string, $pattern = '/\s+/', $limit = -1)
	{
		return (array)preg_split($pattern, trim($string), $limit, PREG_SPLIT_NO_EMPTY);
	}

	public static function htmlSpecialCharsDecodeArray($value)
	{
		if (is_array($value))
		{
			foreach ($value AS $key => $arrayValue)
			{
				$value[$key] = self::htmlSpecialCharsDecodeArray($arrayValue);
			}
		}
		else if (is_string($value))
		{
			$value = htmlspecialchars_decode($value);
		}

		return $value;
	}
}