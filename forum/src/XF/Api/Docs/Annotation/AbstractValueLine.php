<?php

namespace XF\Api\Docs\Annotation;

abstract class AbstractValueLine extends AbstractLine
{
	public $name;
	public $description;
	public $types = [];
	public $modifiers = [];

	public function __construct($name, $description, array $types = [], array $modifiers = [])
	{
		foreach ($types AS &$type)
		{
			if (preg_match('#^([a-z0-9_]+)\[#i', $type, $match))
			{
				$testType = $match[1];
				$suffix = substr($type, strlen($testType));
			}
			else
			{
				$testType = $type;
				$suffix = '';
			}

			switch ($testType)
			{
				case 'int': $type = 'integer' . $suffix; break;
				case 'str': $type = 'string' . $suffix; break;
			}
		}

		$this->name = $name;
		$this->description = $description;
		$this->types = $types ?: ['mixed'];
		$this->modifiers = $modifiers;
	}
}