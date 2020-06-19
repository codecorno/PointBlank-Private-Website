<?php

namespace XF\Db;

class Exception extends \Exception
{
	public $sqlStateCode = null;

	public $query = null;

	/**
	 * @var AbstractStatement|null
	 */
	public $statement = null;
}