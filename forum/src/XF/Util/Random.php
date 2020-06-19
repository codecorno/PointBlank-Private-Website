<?php

namespace XF\Util;

class Random
{
	protected static $sources = null;
	protected static $lastUsed = null;

	public static function getRandomBytes($length)
	{
		if (self::$sources === null)
		{
			self::$sources = self::getAvailableSources();
		}

		$length = intval($length);
		if ($length < 1)
		{
			throw new \LogicException("Must fetch 1 or more random bytes");
		}

		$output = '';
		$remaining = $length;
		$lastUsed = null;

		foreach (self::$sources AS $type => $fn)
		{
			$result = self::$fn($remaining);
			if (is_string($result) && $added = strlen($result))
			{
				$lastUsed = $type;

				$output .= $result;
				$remaining -= $added;
				if ($remaining <= 0)
				{
					break;
				}
			}
		}

		if (strlen($output) < $length)
		{
			throw new \ErrorException("Could not generate random bytes of significant length");
		}

		self::$lastUsed = $lastUsed;

		return substr($output, 0, $length);
	}

	public static function getRandomString($length)
	{
		$random = self::getRandomBytes($length);
		$string = strtr(base64_encode($random), [
			'=' => '',
			"\r" => '',
			"\n" => '',
			'+' => '-',
			'/' => '_'
		]);

		return substr($string, 0, $length);
	}

	/**
	 * Returns the name of the last used source of random data.
	 *
	 * @return string
	 */
	public static function getLastUsedSource()
	{
		if (self::$lastUsed === null)
		{
			self::getRandomBytes(1);
		}

		return self::$lastUsed;
	}

	protected static function _genRandomBytes($length)
	{
		return random_bytes($length);
	}

	protected static $urandomFp;

	protected static function _genUrandom($length)
	{
		if (!self::$urandomFp)
		{
			$fp = @fopen('/dev/urandom', 'rb');
			if (!$fp)
			{
				return false;
			}

			stream_set_read_buffer($fp, 8);
			stream_set_chunk_size($fp, 8);

			self::$urandomFp = $fp;
		}

		return fread(self::$urandomFp, $length);
	}

	protected static function _genMcrypt($length)
	{
		return mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);
	}

	protected static function _genOpenSsl($length)
	{
		$random = openssl_random_pseudo_bytes($length);
		// mixing for fork safety https://wiki.openssl.org/index.php/Random_fork-safety
		return self::mixWithInternal($random);
	}

	protected static function _genInternal($length)
	{
		$data = '';
		do
		{
			$data .= self::getInternalRandomData();
		}
		while (strlen($data) < $length);

		return substr($data, 0, $length);
	}

	protected static function mixWithInternal($random)
	{
		$length = strlen($random);
		$internal = self::_genInternal($length);
		$blockSize = 20; // length of the hash, change if hash changed

		$randomParts = str_split($random, $blockSize);
		$internalParts = str_split($internal, $blockSize);

		$output = '';
		foreach ($randomParts AS $i => $randomPart)
		{
			$internalPart = $internalParts[$i];
			if ($i % 2 == 0)
			{
				$output .= hash_hmac('sha1', $internalPart, $randomPart, true);
			}
			else
			{
				$output .= hash_hmac('sha1', $randomPart, $internalPart, true);
			}
		}

		return substr($output, 0, $length);
	}

	protected static $internalRandomState;

	protected static function getInternalRandomData()
	{
		if (!self::$internalRandomState)
		{
			self::$internalRandomState = sha1(
				(
					memory_get_usage()
					. getmypid()
					. serialize($_ENV)
					. serialize($_SERVER)
					. mt_rand()
					. microtime()
					. spl_object_hash(new \stdClass)
				),
				true
			);
		}

		gc_collect_cycles();
		$parts = mt_rand()
			. memory_get_usage()
			. microtime()
			. self::$internalRandomState;
		self::$internalRandomState = sha1($parts, true);

		return substr(self::$internalRandomState, 0, 10);
	}

	public static function getAvailableSources()
	{
		$available = [];

		// we need to restrict to PHP 7.0 here so we don't unintentionally
		// use the random_compat library used by some other dependencies which
		// will throw an exception in cases where we may be able to fallback
		// to other random source.
		if (PHP_VERSION_ID >= 70000 && function_exists('random_bytes'))
		{
			$available['random_bytes'] = '_genRandomBytes';
		}

		if (function_exists('mcrypt_create_iv'))
		{
			$available['mcrypt'] = '_genMcrypt';
		}

		if (\XF::$DS === '/')
		{
			$baseDir = @ini_get('open_basedir');
			if ($baseDir)
			{
				$uRandomAllowed = false;
				$dirs = explode(':', $baseDir);
				foreach (['/dev', '/dev/', '/dev/urandom'] AS $c)
				{
					if (in_array($c, $dirs))
					{
						$uRandomAllowed = true;
						break;
					}
				}
			}
			else
			{
				$uRandomAllowed = true;
			}

			if ($uRandomAllowed && @is_readable('/dev/urandom'))
			{
				$available['urandom'] = '_genUrandom';
			}
		}

		if (function_exists('openssl_random_pseudo_bytes'))
		{
			$available['openssl'] = '_genOpenSsl';
		}

		$available['internal'] = '_genInternal';

		return $available;
	}

	public static function removeSource($source)
	{
		if (self::$sources === null)
		{
			self::$sources = self::getAvailableSources();
		}

		unset(self::$sources[$source]);
	}
}