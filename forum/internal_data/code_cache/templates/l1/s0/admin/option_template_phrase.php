<?php
// FROM HASH: eee360b51c509ff8a9e0a7a9f1c123c2
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->formTextBoxRow(array(
		'name' => $__vars['inputName'],
		'value' => $__vars['phrase']['phrase_text'],
		'type' => $__vars['formatParams']['type'],
		'class' => $__vars['formatParams']['class'],
	), array(
		'label' => $__templater->escape($__vars['option']['title']),
		'hint' => $__templater->escape($__vars['hintHtml']),
		'explain' => $__templater->escape($__vars['explainHtml']),
		'finalhtml' => $__templater->escape($__vars['listedHtml']),
	));
	return $__finalCompiled;
});