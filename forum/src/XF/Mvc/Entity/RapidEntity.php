<?php

namespace XF\Mvc\Entity;

abstract class RapidEntity extends Entity
{
	public function getInputFilterer($type)
	{
		switch ($type)
		{
			case self::INT:
				return 'int';

			case self::UINT:
				return 'uint';

			case self::FLOAT:
				return 'float';

			case self::BOOL:
				return 'bool';

			case self::STR:
				return 'str';

			case self::BINARY:
				return 'binary'; // TODO: what should we actually use here?

			case self::SERIALIZED:
			case self::SERIALIZED_ARRAY:
			case self::JSON:
			case self::JSON_ARRAY:
			case self::LIST_LINES:
			case self::LIST_COMMA:
				return 'array';
		}
	}

	public function getLabelPhrase($key)
	{
		$field = $this->assertFieldExists($key);

		return \XF::phrase(isset($field['labelPhrase']) ? $field['labelPhrase'] : $key);
	}

	public function getExplainPhrase($key)
	{
		$field = $this->assertFieldExists($key);

		return isset($field['explainPhrase']) ? \XF::phrase($field['explainPhrase']) : '';
	}

	protected function assertFieldExists($key)
	{
		if ($this->isValidColumn($key))
		{
			return $this->_structure->columns[$key];
		}

		$entityName = __CLASS__;
		throw new \LogicException("Field {$key} does not exist in Entity {$entityName}");
	}
}