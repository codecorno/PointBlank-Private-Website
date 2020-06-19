<?php

namespace XF;

class MemoryFile
{
	protected static $buckets = [];

	protected $bucket;
	protected $position;

	public function stream_open($path, $mode, $options, &$opened_path)
	{
		$this->bucket = $bucket = self::getBucketFromUri($path);
		$this->position = 0;

		if (!isset(self::$buckets[$this->bucket]))
		{
			self::$buckets[$this->bucket] = '';
		}

		return true;
	}

	public function stream_stat()
	{
		return self::getBucketStat($this->bucket);
	}

	public function stream_read($count)
	{
		$data = substr(self::$buckets[$this->bucket], $this->position, $count);
		$this->position += strlen($data);

		return $data;
	}

	public function stream_write($data)
	{
		$length = strlen($data);
		$before = substr(self::$buckets[$this->bucket], 0, $this->position);
		$after = substr(self::$buckets[$this->bucket], $this->position + $length);

		self::$buckets[$this->bucket] = $before . $data . $after;
		$this->position += $length;

		return $length;
	}

	public function stream_truncate($new_size)
	{
		self::$buckets[$this->bucket] = $new_size ? substr(self::$buckets[$this->bucket], 0, $new_size) : '';
		return true;
	}

	public function stream_tell()
	{
		return $this->position;
	}

	public function stream_eof()
	{
		return $this->position >= strlen(self::$buckets[$this->bucket]);
	}

	public function stream_seek($offset, $whence)
	{
		switch ($whence)
		{
			case SEEK_SET:
				if ($offset < strlen(self::$buckets[$this->bucket]) && $offset >= 0)
				{
					$this->position = $offset;
					return true;
				}
				else
				{
					return false;
				}

			case SEEK_CUR:
				if ($offset >= 0)
				{
					$this->position += $offset;
					return true;
				}
				else
				{
					return false;
				}

			case SEEK_END:
				$newOffset = strlen(self::$buckets[$this->bucket]) + $offset;
				if ($newOffset >= 0)
				{
					$this->position = $newOffset;
					return true;
				}
				else
				{
					return false;
				}

			default:
				return false;
		}
	}

	public function stream_metadata($path, $option, $var)
	{
		if ($option == STREAM_META_TOUCH)
		{
			$bucket = self::getBucketFromUri($path);
			if (!isset(self::$buckets[$bucket]))
			{
				self::$buckets[$bucket] = '';
			}
		}
	}

	public function url_stat($path, $flags)
	{
		$bucket = self::getBucketFromUri($path);
		return self::getBucketStat($bucket);
	}

	protected static function getBucketFromUri($uri)
	{
		$url = parse_url($uri);
		return $url['host'];
	}

	protected static function getBucketStat($bucket)
	{
		$size = isset(self::$buckets[$bucket]) ? strlen(self::$buckets[$bucket]) : 0;
		$now = time();

		return [
			'dev' => 0,
			'ino' => 0,
			'mode' => 0666,
			'nlink' => 0,
			'uid' => 0,
			'gid' => 0,
			'rdev' => 0,
			'size' => $size,
			'atime' => $now,
			'mtime' => $now,
			'ctime' => $now,
			'blksize' => -1,
			'blocks' => -1
		];
	}

	public static function clear($bucket)
	{
		self::$buckets[$bucket] = '';
	}

	public static function clearAll()
	{
		self::$buckets = [];
	}

	public static function register($protocol)
	{
		stream_wrapper_register($protocol, __CLASS__);
	}

	public static function unregister($protocol)
	{
		stream_wrapper_unregister($protocol);
	}
}