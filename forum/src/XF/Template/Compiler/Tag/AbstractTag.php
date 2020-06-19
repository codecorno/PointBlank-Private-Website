<?php

namespace XF\Template\Compiler\Tag;

use XF\Template\Compiler\Syntax\Tag;
use XF\Template\Compiler;

abstract class AbstractTag
{
	public $name;

	public function __construct($name)
	{
		$this->name = $name;
	}

	abstract public function compile(Tag $tag, Compiler $compiler, array $context, $inlineExpected);

	public function reset()
	{

	}

	/**
	 * @param Compiler\Syntax\AbstractSyntax[] $attributes
	 * @param Compiler $compiler
	 * @param array $context
	 * @param array $htmlAttributes Listed attributes will have variables escaped; used when HTML is able to be used (eg, labels)
	 * @param array $forcedExpressions Listed attributes will always be treated as expressions
	 *
	 * @return array
	 */
	public function compileAttributesAsArray(array $attributes, Compiler $compiler, array $context,
		array $htmlAttributes = [], array $forcedExpressions = [])
	{
		$output = [];

		$context['escape'] = true;
		$rawContext = $context;
		$rawContext['escape'] = false;

		foreach ($attributes AS $name => $value)
		{
			if (in_array($name, $forcedExpressions))
			{
				$value = $compiler->forceToExpression($value);
			}

			if ($htmlAttributes && in_array($name, $htmlAttributes))
			{
				$output[$name] = $compiler->compileToArraySyntax($value, $name, $context);
			}
			else
			{
				$output[$name] = $compiler->compileToArraySyntax($value, $name, $rawContext);
			}
		}

		return $output;
	}

	public function splitArrayByKeys(array $data, array $specialKeys)
	{
		$special = [];
		$fallback = [];

		foreach ($data AS $key => $value)
		{
			if (in_array($key, $specialKeys))
			{
				$special[$key] = $value;
			}
			else
			{
				$fallback[$key] = $value;
			}
		}

		return [$special, $fallback];
	}

	public function isNamedTag($tag, $name)
	{
		if (!($tag instanceof Tag))
		{
			return false;
		}

		if (is_array($name))
		{
			foreach ($name AS $n)
			{
				if ($tag->name == $n)
				{
					return $n;
				}
			}

			return false;
		}
		else
		{
			return ($tag->name == $name);
		}
	}

	public function isEmptyString($el)
	{
		return ($el instanceof Compiler\Syntax\Str && $el->isEmpty());
	}
}