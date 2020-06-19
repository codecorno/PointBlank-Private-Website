<?php
// FROM HASH: 6b3ee596da4e43e8f02b8cff7accc20c
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->callMacro('phrase_translate_macros', 'collapsed', array(
		'phrase' => $__vars['phrase'],
		'language' => $__vars['language'],
	), $__vars);
	return $__finalCompiled;
});