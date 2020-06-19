<?php

namespace XF\Template\Compiler\Tag;

use XF\Template\Compiler;
use XF\Template\Compiler\Syntax\Tag;

class TelBoxRow extends AbstractFormElement
{
	public function compile(Tag $tag, Compiler $compiler, array $context, $inlineExpected)
	{
		$tag->assertAttribute('dialcodename')
			->assertAttribute('intlnumbername');

		return $this->compileTextInput('TelBox', $tag->name == 'telboxrow', $tag, $compiler, $context);
	}
}