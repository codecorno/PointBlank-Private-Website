<?php
// FROM HASH: 8dd55bd313886abc8f7a1a1c5056b37e
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->formRadioRow(array(
		'name' => $__vars['inputName'] . '[type]',
		'value' => $__vars['option']['option_value']['type'],
	), array(array(
		'value' => '',
		'label' => 'No contact URL',
		'_type' => 'option',
	),
	array(
		'value' => 'default',
		'label' => 'Default URL' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->func('link_type', array('public', 'canonical:misc/contact', ), true),
		'_type' => 'option',
	),
	array(
		'value' => 'custom',
		'label' => 'Custom URL',
		'_dependent' => array($__templater->formTextBox(array(
		'name' => $__vars['inputName'] . '[custom]',
		'value' => $__vars['option']['option_value']['custom'],
	)), '
			' . $__templater->formCheckBox(array(
		'standalone' => 'true',
	), array(array(
		'name' => $__vars['inputName'] . '[overlay]',
		'selected' => $__vars['option']['option_value']['overlay'],
		'label' => 'Open this link in an overlay',
		'_type' => 'option',
	))) . '
		'),
		'_type' => 'option',
	)), array(
		'label' => $__templater->escape($__vars['option']['title']),
		'hint' => $__templater->escape($__vars['hintHtml']),
		'explain' => $__templater->escape($__vars['explainHtml']),
		'html' => $__templater->escape($__vars['listedHtml']),
	)) . '



';
	return $__finalCompiled;
});