<?php
// FROM HASH: c3fd1027b9a7d6ff1ecd403d6aeaf832
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->formRow('

	<dl class="inputLabelPair">
		<dt><label for="' . $__templater->escape($__vars['inputName']) . '_0">' . 'Minimum number of bounces' . '</label></dt>
		<dd>' . $__templater->formNumberBox(array(
		'name' => $__vars['inputName'] . '[bounce_total]',
		'value' => $__vars['option']['option_value']['bounce_total'],
		'id' => $__vars['inputName'] . '_0',
		'min' => '1',
	)) . '</dd>
	</dl>
	<dl class="inputLabelPair">
		<dt><label for="' . $__templater->escape($__vars['inputName']) . '_1">' . 'Minimum number of days with bounces' . '</label></dt>
		<dd>' . $__templater->formNumberBox(array(
		'name' => $__vars['inputName'] . '[unique_days]',
		'value' => $__vars['option']['option_value']['unique_days'],
		'id' => $__vars['inputName'] . '_1',
		'min' => '1',
		'max' => '30',
	)) . '</dd>
	</dl>
	<dl class="inputLabelPair">
		<dt><label for="' . $__templater->escape($__vars['inputName']) . '_2">' . 'Min. days between oldest and newest bounce' . '</label></dt>
		<dd>' . $__templater->formNumberBox(array(
		'name' => $__vars['inputName'] . '[days_between]',
		'value' => $__vars['option']['option_value']['days_between'],
		'id' => $__vars['inputName'] . '_2',
		'min' => '0',
		'max' => '30',
	)) . '</dd>
	</dl>
', array(
		'rowtype' => 'inputLabelPair',
		'label' => $__templater->escape($__vars['option']['title']),
		'hint' => $__templater->escape($__vars['hintHtml']),
		'explain' => $__templater->escape($__vars['explainHtml']),
		'html' => $__templater->escape($__vars['listedHtml']),
	));
	return $__finalCompiled;
});