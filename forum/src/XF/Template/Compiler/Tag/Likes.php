<?php

namespace XF\Template\Compiler\Tag;

use XF\Template\Compiler\Syntax\Tag;
use XF\Template\Compiler;

class Likes extends AbstractTag
{
	public function compile(Tag $tag, Compiler $compiler, array $context, $inlineExpected)
	{
		$tag->assertEmpty()->assertAttribute('url');

		$context['escape'] = false;

		$otherAttributes = $tag->attributes;
		unset(
			$otherAttributes['count'], $otherAttributes['likes'],
			$otherAttributes['liked'], $otherAttributes['content']
		);

		$config = $this->compileAttributesAsArray($otherAttributes, $compiler, $context);
		$indent = $compiler->indent();
		$attributesCode = "array(" . implode('', $config) . "\n$indent)";

		$url = $tag->attributes['url']->compile($compiler, $context, true);

		if (isset($tag->attributes['content']))
		{
			$content = $tag->attributes['content']->compile($compiler, $context, true);

			return "{$compiler->templaterVariable}->func('likes_content', array($content, $url, $attributesCode))";
		}
		else
		{
			$tag->assertAttribute('count')->assertAttribute('likes');

			$count = $tag->attributes['count']->compile($compiler, $context, true);
			$users = $tag->attributes['users']->compile($compiler, $context, true);

			if (isset($tag->attributes['liked']))
			{
				$liked = $compiler->compileForcedExpression($tag->attributes['liked'], $context);
			}
			else
			{
				$liked = 'false';
			}

			return "{$compiler->templaterVariable}->func('likes', array($count, $users, $liked, $url, $attributesCode))";
		}
	}
}