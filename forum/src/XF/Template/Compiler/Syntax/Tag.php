<?php

namespace XF\Template\Compiler\Syntax;

use XF\Template\Compiler;

class Tag extends AbstractSyntax
{
	/**
	 * @var string
	 */
	public $name = '';

	/**
	 * @var AbstractSyntax[]
	 */
	public $attributes = [];

	/**
	 * @var AbstractSyntax[]
	 */
	public $children = [];

	/**
	 * @var bool
	 */
	public $isSelfClose;

	public function __construct($name, array $attributes, array $children, $line, $isSelfClose = false)
	{
		$this->name = $name;
		$this->attributes = $attributes;
		$this->children = $children;
		$this->line = $line;
		$this->isSelfClose = (bool)$isSelfClose;
	}

	public function compile(Compiler $compiler, array $context, $inlineExpected)
	{
		return $this->getTag($compiler)->compile($this, $compiler, $context, $inlineExpected);
	}

	public function getTag(Compiler $compiler)
	{
		$tag = $compiler->getTag($this->name);
		if (!$tag)
		{
			throw $this->exception(\XF::phrase('unknown_tag_x_encountered', ['name' => $this->name]));
		}

		return $tag;
	}

	public function assertAttribute($attribute)
	{
		if (!isset($this->attributes[$attribute]))
		{
			throw $this->exception(\XF::phrase('tag_x_must_have_attribute_y', ['name' => $this->name, 'attribute' => $attribute]));
		}

		return $this;
	}

	public function assertEmpty()
	{
		if ($this->children)
		{
			throw $this->exception(\XF::phrase('tag_x_must_be_empty', ['name' => $this->name]));
		}

		return $this;
	}

	public function isSimpleValue()
	{
		return false;
	}
}