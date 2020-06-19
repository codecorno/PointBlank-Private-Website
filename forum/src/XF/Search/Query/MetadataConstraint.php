<?php

namespace XF\Search\Query;

class MetadataConstraint
{
	protected $key = '';
	protected $values = [];
	protected $matchType = 1;

	const MATCH_ANY = 1;
	const MATCH_ALL = 2;
	const MATCH_NONE = 3;

	public function __construct($key, $values, $matchType = self::MATCH_ANY)
	{
		$this->key = $key;
		$this->setValues($values);
		$this->setMatchType($matchType);
	}

	public function getKey()
	{
		return $this->key;
	}

	public function setValues($values)
	{
		if (!is_array($values))
		{
			$values = [$values];
		}

		if (!count($values))
		{
			throw new \LogicException("Must provide at least 1 metadata value");
		}

		$this->values = $values;
	}

	public function getValues()
	{
		return $this->values;
	}

	public function setMatchType($match)
	{
		switch ($match)
		{
			case self::MATCH_ANY:
			case 'any':
				$this->matchType = self::MATCH_ANY;
				break;

			case self::MATCH_ALL:
			case 'all':
				$this->matchType = self::MATCH_ALL;
				break;

			case self::MATCH_NONE:
			case 'none':
				$this->matchType = self::MATCH_NONE;
				break;

			default:
				throw new \LogicException("Invalid match type '$match'");
		}
	}

	public function getMatchType()
	{
		return $this->matchType;
	}
}