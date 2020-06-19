<?php

namespace XF\Search;

class MetadataStructure
{
	const INT = 'int';
	const FLOAT = 'float';
	const STR = 'str';
	const KEYWORD = 'keyword';
	const BOOL = 'bool';

	protected $fields;

	public function __construct(array $fields = [])
	{
		$this->fields = $fields;
	}

	public function addField($name, $type, array $config = [])
	{
		$config['type'] = $type;
		$this->fields[$name] = $config;
	}

	public function getFields()
	{
		return $this->fields;
	}
}