<?php
// FROM HASH: fe5216105dcf2e33e783c80427989b6a
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->formRow('

	' . $__templater->escape($__vars['explainHtml']) . '
', array(
		'label' => $__templater->escape($__vars['option']['title']),
		'hint' => $__templater->escape($__vars['hintHtml']),
	));
	return $__finalCompiled;
});