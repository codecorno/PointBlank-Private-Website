<?php
// FROM HASH: bd6a05bc886b98924c1fc1d8fe00e101
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->formRow('

	' . $__templater->formTextArea(array(
		'name' => $__vars['inputName'] . '[phrases]',
		'value' => $__vars['option']['option_value']['phrases'],
		'rows' => '5',
		'autosize' => 'true',
	)) . '
	<p class="formRow-explain">' . $__templater->escape($__vars['explainHtml']) . '</p>

	<div class="u-inputSpacer">' . 'Action' . $__vars['xf']['language']['label_separator'] . '</div>
	' . $__templater->formRadio(array(
		'name' => $__vars['inputName'] . '[action]',
		'value' => $__vars['option']['option_value']['action'],
	), array(array(
		'value' => 'moderate',
		'label' => 'Manually approve',
		'_type' => 'option',
	),
	array(
		'value' => 'reject',
		'label' => 'Reject',
		'_type' => 'option',
	))) . '
', array(
		'rowtype' => 'input',
		'label' => $__templater->escape($__vars['option']['title']),
		'hint' => $__templater->escape($__vars['hintHtml']),
		'html' => $__templater->escape($__vars['listedHtml']),
	));
	return $__finalCompiled;
});