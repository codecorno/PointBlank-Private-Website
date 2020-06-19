<?php
// FROM HASH: 9817bf8aecdb5e18113b5a5ff1ea1704
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => $__vars['inputName'] . '[enabled]',
		'selected' => $__vars['option']['option_value']['enabled'],
		'label' => $__templater->escape($__vars['option']['title']),
		'_type' => 'option',
	)), array(
		'hint' => $__templater->escape($__vars['hintHtml']),
		'explain' => $__templater->escape($__vars['explainHtml']),
		'html' => $__templater->escape($__vars['listedHtml']),
	)) . '

' . $__templater->formHiddenVal($__vars['inputName'] . '[configured]', '1', array(
	)) . '
' . $__templater->formHiddenVal($__vars['inputName'] . '[installation_id]', $__vars['option']['option_value']['installation_id'], array(
	)) . '
' . $__templater->formHiddenVal($__vars['inputName'] . '[last_sent]', $__vars['option']['option_value']['last_sent'], array(
	));
	return $__finalCompiled;
});