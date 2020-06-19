<?php

namespace XF\Legacy;

class Application
{
	public static function debugMode()
	{
		return \XF::$debugMode;
	}

	public static function get($key)
	{
		switch ($key)
		{
			case 'options': return \XF::options();
			case 'config': return \XF::config();
			case 'db': return \XF::db();
			case 'session': return \XF::session();

			default:
				throw new \InvalidArgumentException("Can't load '$key'");
		}
	}

	/**
	 * @return \XF\Db\AbstractAdapter
	 */
	public static function getDb()
	{
		return self::get('db');
	}

	/**
	 * @return \XF\Session\Session
	 */
	public static function getSession()
	{
		return self::get('session');
	}

	/**
	 * @return array
	 */
	public static function getConfig()
	{
		return self::get('config');
	}

	/**
	 * @return \ArrayObject
	 */
	public static function getOptions()
	{
		return self::get('options');
	}
}