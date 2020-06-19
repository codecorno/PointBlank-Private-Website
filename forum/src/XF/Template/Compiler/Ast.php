<?php

namespace XF\Template\Compiler;

use XF\Template\Compiler\Syntax;

class Ast
{
	/**
	 * @var Syntax\AbstractSyntax[]
	 */
	public $children = [];

	public function __construct(array $children)
	{
		$this->children = $children;
	}

	public function analyze(\Closure $handler)
	{
		$this->_analyze($this->children, $handler);
	}

	protected function _analyze($element, \Closure $handler)
	{
		if ($element instanceof Syntax\AbstractSyntax)
		{
			$handler($element);

			$reflection = new \ReflectionObject($element);
			$properties = $reflection->getProperties();
			foreach ($properties AS $property)
			{
				$value = $property->getValue($element);
				$this->_analyze($value, $handler);
			}
		}
		else if (is_array($element))
		{
			foreach ($element AS $child)
			{
				$this->_analyze($child, $handler);
			}
		}
	}

	/**
	 * Returns a list of phrases used by this template
	 *
	 * @return array
	 */
	public function analyzePhrases()
	{
		$phrases = [];

		$this->analyze(function (Syntax\AbstractSyntax $syntax) use (&$phrases)
		{
			if (
				$syntax instanceof Syntax\Func
				&& strtolower($syntax->name) == 'phrase'
				&& isset($syntax->arguments[0])
				&& $syntax->arguments[0] instanceof Syntax\Str
			)
			{
				$phraseName = $syntax->arguments[0]->content;
				$phraseName = preg_replace('/^[^a-z0-9_]+/i', '', $phraseName);
				$phraseName = preg_replace('/[^a-z0-9_]+$/i', '', $phraseName);

				if (strlen($phraseName) <= 100)
				{
					$phrases[] = $phraseName;
				}
			}
		});

		return array_unique($phrases);
	}
}