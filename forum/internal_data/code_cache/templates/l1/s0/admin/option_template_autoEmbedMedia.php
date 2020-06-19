<?php
// FROM HASH: 5d4383c1b8bdbe9d33821e6a2edf8d50
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->formRadioRow(array(
		'name' => $__vars['inputName'] . '[embedType]',
		'value' => $__vars['option']['option_value']['embedType'],
	), array(array(
		'value' => '0',
		'label' => 'Do not auto-embed media links',
		'_type' => 'option',
	),
	array(
		'value' => '1',
		'label' => 'Auto-embed media links',
		'_type' => 'option',
	),
	array(
		'value' => '2',
		'label' => 'Auto-embed media, and add a link to the content within this BB code',
		'_dependent' => array('
			' . $__templater->formTextBox(array(
		'name' => $__vars['inputName'] . '[linkBbCode]',
		'value' => $__vars['option']['option_value']['linkBbCode'],
		'size' => '30',
		'code' => 'true',
	)) . '
			<dfn class="inputChoices-explain">' . 'Include <strong>{$url}</strong> as a placeholder for the link.' . '</dfn>
		'),
		'_type' => 'option',
	)), array(
		'label' => $__templater->escape($__vars['option']['title']),
		'hint' => $__templater->escape($__vars['hintHtml']),
		'explain' => $__templater->escape($__vars['explainHtml']),
		'html' => $__templater->escape($__vars['listedHtml']),
	));
	return $__finalCompiled;
});