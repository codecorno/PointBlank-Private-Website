<?php

namespace XF\BbCode\Renderer;

use XF\BbCode\Traverser;

abstract class AbstractRenderer extends Traverser
{
	protected $tags = [];

	abstract public function getCustomTagConfig(array $tag);

	public function addTag($tag, array $config)
	{
		$this->tags[$tag] = $config;
	}

	public function modifyTag($tag, array $modification)
	{
		if (isset($this->tags[$tag]))
		{
			$this->tags[$tag] = array_merge($this->tags[$tag], $modification);
		}
	}

	public function removeTag($tag)
	{
		unset($this->tags[$tag]);
	}

	public static function factory(\XF\App $app)
	{
		throw new \LogicException("The factory method must be overridden in BB code renderers");
	}
}