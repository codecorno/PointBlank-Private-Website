<?php
// FROM HASH: cd85730783240b26e1c05be178e4df10
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->callMacro('phrase_translate_macros', 'expanded', array(
		'phrase' => $__vars['phrase'],
		'language' => $__vars['language'],
	), $__vars);
	return $__finalCompiled;
});