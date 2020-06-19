<?php

namespace XF\Util;

class Ip
{
	/**
	 * Converts a string based IP (v4 or v6) to a 4 or 16 byte string.
	 * This tries to identify not only 192.168.1.1 and 2001::1:2:3:4 style IPs,
	 * but integer encoded IPv4 and already binary encoded IPs. IPv4
	 * embedded in IPv6 via things like ::ffff:192.168.1.1 is also detected.
	 *
	 * @param string|int $ip
	 *
	 * @return bool|string False on failure, binary data otherwise
	 */
	public static function convertIpStringToBinary($ip)
	{
		$originalIp = $ip;

		if (strlen($ip) == 4)
		{
			// already encoded IPv4
			return $ip;
		}

		if (strlen($ip) == 16 && preg_match('/[^0-9a-f.:]/i', $ip))
		{
			// already encoded IPv6
			return $ip;
		}

		$ip = trim($ip, " \t");

		if (strpos($ip, ':') !== false)
		{
			// IPv6
			if (preg_match('#:(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})$#i', $ip, $match))
			{
				// embedded IPv4, just treat as IPv4
				$long = ip2long($match[1]);
				if (!$long)
				{
					return false;
				}

				return self::convertHexToBin(
					str_pad(dechex($long), 8, '0', STR_PAD_LEFT)
				);
			}

			if (strpos($ip, '::') !== false)
			{
				if (substr_count($ip, '::') > 1)
				{
					// ambiguous
					return false;
				}

				$delims = substr_count($ip, ':');
				if ($delims > 7)
				{
					return false;
				}

				$ip = str_replace('::', str_repeat(':0', 8 - $delims) . ':', $ip);
				if ($ip[0] == ':')
				{
					$ip = '0' . $ip;
				}
			}

			$ip = strtolower($ip);

			$parts = explode(':', $ip);
			if (count($parts) != 8)
			{
				return false;
			}

			foreach ($parts AS &$part)
			{
				$len = strlen($part);
				if ($len > 4 || preg_match('/[^0-9a-f]/', $part))
				{
					return false;
				}

				if ($len < 4)
				{
					$part = str_repeat('0', 4 - $len) . $part;
				}
			}

			$hex = implode('', $parts);
			if (strlen($hex) != 32)
			{
				return false;
			}

			if (preg_match('/^00000000000000000000ffff([0-9a-f]{8})$/', $hex, $match))
			{
				// ::ffff:IPv4 address that was written in pure IPv6 form, treat as an IPv4 address
				return self::convertHexToBin($match[1]);
			}

			return self::convertHexToBin($hex);
		}

		if (strpos($ip, '.'))
		{
			// IPv4
			if (!preg_match('#(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})#', $ip, $match))
			{
				return false;
			}

			$long = ip2long($match[1]);
			if ($long === false)
			{
				return false;
			}

			return self::convertHexToBin(
				str_pad(dechex($long), 8, '0', STR_PAD_LEFT)
			);
		}

		if (strlen($ip) == 4 || strlen($ip) == 16)
		{
			// already binary encoded
			return $ip;
		}

		if (is_numeric($originalIp) && $originalIp < pow(2, 32))
		{
			// IPv4 as integer
			return self::convertHexToBin(
				str_pad(dechex($originalIp), 8, '0', STR_PAD_LEFT)
			);
		}

		return false;
	}

	/**
	 * Converts a hex string to binary
	 *
	 * @param string $hex
	 *
	 * @return string
	 */
	public static function convertHexToBin($hex)
	{
		if (function_exists('hex2bin'))
		{
			return hex2bin($hex);
		}

		$len = strlen($hex);

		if ($len % 2)
		{
			trigger_error('Hexadecimal input string must have an even length', E_USER_WARNING);
		}

		if (strspn($hex, '0123456789abcdefABCDEF') != $len)
		{
			trigger_error('Input string must be hexadecimal string', E_USER_WARNING);
		}

		return pack('H*', $hex);
	}

	/**
	 * Converts a binary string containing IPv4 or v6 data to a printable/human
	 * readable version. If shortening is enabled, IPv6 data will be collapsed
	 * as much as possible.
	 *
	 * @param string $ip Binary IP data
	 * @param bool $shorten
	 *
	 * @return bool|string
	 */
	public static function convertIpBinaryToString($ip, $shorten = true)
	{
		if (strlen($ip) == 4)
		{
			// IPv4
			$parts = [];
			foreach (str_split($ip) AS $char)
			{
				$parts[] = ord($char);
			}

			return implode('.', $parts);
		}

		if (strlen($ip) == 16)
		{
			// IPv6
			$parts = [];
			$chunks = str_split($ip);
			for ($i = 0; $i < 16; $i += 2)
			{
				$char1 = $chunks[$i];
				$char2 = $chunks[$i + 1];

				$part = sprintf('%02x%02x', ord($char1), ord($char2));
				if ($shorten)
				{
					// reduce this to the shortest length possible, but keep 1 zero if needed
					$part = ltrim($part, '0');
					if (!strlen($part))
					{
						$part = '0';
					}
				}
				$parts[] = $part;
			}

			$output = implode(':', $parts);
			if ($shorten)
			{
				$output = preg_replace_callback(
					'/((^0|:0){2,})(.*)$/',
					function($matches)
					{
						return ':' . (strlen($matches[3]) ? $matches[3] : ':');
					},
					$output
				);

				if ($output == ':')
				{
					// correct way of writing an IPv6 address of all zeroes
					$output = '::';
				}

				if (preg_match('/^::ffff:([0-9a-f]{2})([0-9a-f]{2}):([0-9a-f]{2})([0-9a-f]{2})$/i', $output, $match))
				{
					// IPv4-mapped IPv6
					$output = '::ffff:' . hexdec($match[1]) . '.' . hexdec($match[2]) . '.'
						. hexdec($match[3]) . '.' . hexdec($match[4]);
				}
			}

			return strtolower($output);
		}

		if (preg_match('/^[0-9]+$/', $ip))
		{
			return long2ip($ip + 0);
		}

		return false;
	}

	/**
	 * Gets the range of binary-encoded IPs that match the given
	 * CIDR block size. Supports IPv4 and v6. Ranges can be checked
	 * via >= lower and <= upper.
	 *
	 * @param string $ip Binary IP
	 * @param integer $cidr CIDR range
	 *
	 * @return array|string If no CIDR is specified or if the CIDR specifies only one address, a string with that address.
	 * 		Otherwise, an array with a lower and upper bound.
	 */
	public static function getIpCidrMatchRange($ip, $cidr)
	{
		if (!$cidr)
		{
			return $ip;
		}

		$bytes = strlen($ip);
		$bits = $bytes * 8;
		if ($cidr >= $bits)
		{
			return $ip; // exact match
		}

		$prefixBytes = (int)floor($cidr / 8);
		$remainingBits = ($cidr - $prefixBytes * 8);

		$prefix = substr($ip, 0, $prefixBytes);
		if ($remainingBits)
		{
			$partialByteOrd = ord($ip[$prefixBytes]); // first character after full prefix bytes
			$mask = (1 << 8 - $remainingBits) - 1;

			$upperBound = chr($partialByteOrd | $mask);
			$lowerBound = chr($partialByteOrd & ~$mask);
			$boundLength = 1;
		}
		else
		{
			$upperBound = '';
			$lowerBound = '';
			$boundLength = 0;
		}

		$suffixBytes = $bytes - $prefixBytes - $boundLength;
		if ($suffixBytes)
		{
			$lowerSuffix = str_repeat(chr(0), $suffixBytes);
			$upperSuffix = str_repeat(chr(255), $suffixBytes);
		}
		else
		{
			$lowerSuffix = '';
			$upperSuffix = '';
		}

		return [$prefix . $lowerBound . $lowerSuffix, $prefix . $upperBound . $upperSuffix];
	}

	/**
	 * Determines if a particular IP matches a IP CIDR range.
	 * Both IPs must be binary encoded
	 *
	 * @param string $testIp Binary encoded IP to test if within range
	 * @param string $rangeIp Binary encoded IP to make the range from
	 * @param integer $cidr CIDR block size
	 *
	 * @return bool
	 */
	public static function ipMatchesCidrRange($testIp, $rangeIp, $cidr)
	{
		$range = self::getIpCidrMatchRange($rangeIp, $cidr);
		if (is_string($range))
		{
			return ($testIp == $range);
		}
		else
		{
			return self::ipMatchesRange($testIp, $range[0], $range[1]);
		}
	}

	/**
	 * Simplifies checking if an IP is within a range.
	 * All IPs and ranges must be binary encoded.
	 *
	 * @param string $testIp
	 * @param string $lowerBound
	 * @param string $upperBound
	 *
	 * @return bool
	 */
	public static function ipMatchesRange($testIp, $lowerBound, $upperBound)
	{
		return ($testIp >= $lowerBound AND $testIp <= $upperBound AND strlen($testIp) == strlen($lowerBound));
	}

	/**
	 * @param array|string $checkIps List of IPs to check for a match
	 * @param array $firstByteRangeList List of binary IP ranges [start, end] keyed by [binary first byte]
	 *
	 * @return bool
	 */
	public static function checkIpsAgainstBinaryRangeList($checkIps, array $firstByteRangeList)
	{
		if (!is_array($checkIps))
		{
			$checkIps = [$checkIps];
		}

		foreach ($checkIps AS $ip)
		{
			$binary = self::convertIpStringToBinary($ip);
			if (!$binary)
			{
				continue;
			}

			$firstByte = $binary[0];

			if (empty($firstByteRangeList[$firstByte]))
			{
				continue;
			}

			foreach ($firstByteRangeList[$firstByte] AS $range)
			{
				if (self::ipMatchesRange($binary, $range[0], $range[1]))
				{
					return $range;
				}
			}
		}

		return null;
	}

	/**
	 * Parses a human readable IP range string into a machine processable version.
	 * IPv4 can be specified with CIDR or 192.168.* style ranges. IPv6 supports CIDR only.
	 *
	 * @param string $ip Human readable IPv4 or v6 IP
	 *
	 * @return array|bool False on failure, Otherwise array with following keys:
	 * 		- printable: human readable version of the IP range. May be adjusted to standardize display slightly
	 * 		- binary: binary version of provided IP. IPv4 missing octets are considered to be 0.
	 * 		- cidr: the final CIDR range found/used. If a IPv4 partial is provided, this will be determined from number of missing octets. 0 for exact.
	 * 		- isRange: true if the provided IP actually spans a range
	 *		- startRange: binary version of lower bound IP
	 *		- endRange: binary version of upper bound IP
	 */
	public static function parseIpRangeString($ip)
	{
		$ip = trim($ip);
		$niceIp = $ip;

		if (preg_match('#/(\d+)$#', $ip, $match))
		{
			$ip = substr($ip, 0, -strlen($match[0]));
			$cidr = $match[1];
			if ($cidr && $cidr < 8)
			{
				$cidr = 8;
				$niceIp = $ip . "/$cidr";
			}
		}
		else
		{
			$cidr = 0;
		}

		if (strpos($ip, ':') !== false)
		{
			// IPv6 -- no partials, only CIDR
			$binary = self::convertIpStringToBinary($ip);
			if ($binary === false)
			{
				return false;
			}
		}
		else
		{
			$ip = preg_replace('/\.+$/', '', $ip);
			if (!preg_match('/^\d+(\.\d+){0,2}(\.\d+|\.\*)?$/', $ip))
			{
				return false;
			}

			if (substr($ip, -2) == '.*')
			{
				$ip = substr($ip, 0, -2);
			}

			$ipParts = explode('.', $ip);
			foreach ($ipParts AS $part)
			{
				if ($part < 0 || $part > 255)
				{
					return false;
				}
			}

			$localCidr = 32;
			while (count($ipParts) < 4)
			{
				$ipParts[] = 0;
				$localCidr -= 8;
			}

			if (!$cidr && $localCidr != 32)
			{
				$cidr = $localCidr;
				$niceIp = $ip . '.*';
			}

			$binary = self::convertIpStringToBinary(implode('.', $ipParts));
			if (!$binary)
			{
				return false;
			}
		}

		$range = self::getIpCidrMatchRange($binary, $cidr);

		return [
			'printable' => $niceIp,
			'binary' => $binary,
			'cidr' => $cidr,
			'isRange' => is_array($range),
			'startRange' => is_string($range) ? $range : $range[0],
			'endRange' => is_string($range) ? $range : $range[1]
		];
	}

	protected static $lookupCache = [];

	/**
	 * Resolves the host name of an IP address
	 *
	 * @param string $ip
	 *
	 * @return string
	 */
	public static function getHost($ip)
	{
		if (isset(self::$lookupCache[$ip]))
		{
			return self::$lookupCache[$ip];
		}

		$decompressed = self::convertIpBinaryToString($ip, false);
		if ($decompressed !== false)
		{
			$ip = $decompressed;
		}

		if (strpos($ip, ':') !== false)
		{
			// we need to uncompress the hex address to split this up
			$binary = self::convertIpStringToBinary($ip);
			if (!$binary)
			{
				return '';
			}
			$checkIp = self::convertIpBinaryToString($binary, false);
			if (!$checkIp)
			{
				return false;
			}

			$checkIp = str_replace(':', '', $checkIp);
			$parts = str_split($checkIp);
			$dnsRecord = implode('.', array_reverse($parts)) . '.ip6.arpa';
		}
		else
		{
			$parts = explode('.', $ip);
			if (count($parts) != 4)
			{
				return '';
			}

			$dnsRecord = implode('.', array_reverse($parts)) . '.in-addr.arpa';
		}

		$lookup = false;

		try
		{
			if (function_exists('dns_get_record'))
			{
				$host = @dns_get_record($dnsRecord, DNS_PTR);
				if (isset($host[0]['target']))
				{
					$lookup = $host[0]['target'];
				}
			}
			else
			{
				$lookup = gethostbyaddr($ip);
			}
		}
		catch (\Exception $e) { } // bad lookup

		if (!$lookup)
		{
			$lookup = $ip;
		}

		self::$lookupCache[$ip] = $lookup;
		return $lookup;
	}
}