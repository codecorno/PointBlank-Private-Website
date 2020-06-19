<?php

namespace XF\Mvc\Entity;

class Structure
{
	public $shortName;
	public $contentType;

	public $table;
	public $primaryKey;
	public $columns = [];
	public $relations = [];
	public $getters = [];
	public $defaultWith = [];
	public $options = [];
	public $behaviors = [];

	public $columnAliases = []; // note that aliases are mostly designed as a stopgap to reduce BC issues; not applied when writing
	public $withAliases = [];

	// column props: autoIncrement, writeOnce, readOnly, type, nullable, verify, default, required
	// column validations based on type: min, max, forced, maxLength, allowedValues
}