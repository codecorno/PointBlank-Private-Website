<?php

namespace XF\Template\Compiler\Tag;

use XF\Template\Compiler\Syntax\Tag;
use XF\Template\Compiler;

class UserActivity extends AbstractTag
{
	public function compile(Tag $tag, Compiler $compiler, array $context, $inlineExpected)
	{
		$tag->assertEmpty()->assertAttribute('user');

		$context['escape'] = false;

		$user = $compiler->compileForcedExpression($tag->attributes['user'], $context);

		return "{$compiler->templaterVariable}->func('user_activity', array($user))";
	}
}