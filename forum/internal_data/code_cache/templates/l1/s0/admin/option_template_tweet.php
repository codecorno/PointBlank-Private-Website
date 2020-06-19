<?php
// FROM HASH: d3822d72337455288e2c7dbae5787ba9
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => $__vars['inputName'] . '[enabled]',
		'selected' => $__vars['option']['option_value']['enabled'],
		'label' => $__templater->escape($__vars['option']['title']),
		'_dependent' => array($__templater->formTextBox(array(
		'name' => $__vars['inputName'] . '[via]',
		'value' => $__vars['option']['option_value']['via'],
		'placeholder' => 'Via account: @...',
	)), $__templater->formTextBox(array(
		'name' => $__vars['inputName'] . '[related]',
		'value' => $__vars['option']['option_value']['related'],
		'placeholder' => 'Related account: @...',
	))),
		'_type' => 'option',
	)), array(
		'hint' => $__templater->escape($__vars['hintHtml']),
		'explain' => $__templater->escape($__vars['explainHtml']),
		'html' => $__templater->escape($__vars['listedHtml']),
	));
	return $__finalCompiled;
});