<?php
// FROM HASH: 5f13f3ab0d5f8ebc64055e67a7f39a0b
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->formRow('

	<dl class="inputLabelPair">
		<dt><label for="spamuc_mc">' . 'Maximum message count' . '</label></dt>
		<dd>' . $__templater->formNumberBox(array(
		'name' => $__vars['inputName'] . '[message_count]',
		'value' => $__vars['option']['option_value']['message_count'],
		'min' => '0',
		'id' => 'spamuc_mc',
	)) . '</dd>
	</dl>
	<dl class="inputLabelPair">
		<dt><label for="spamuc_rd">' . 'Maximum days since registration' . '</label></dt>
		<dd>' . $__templater->formNumberBox(array(
		'name' => $__vars['inputName'] . '[register_date]',
		'value' => $__vars['option']['option_value']['register_date'],
		'min' => '0',
		'id' => 'spamuc_rd',
	)) . '</dd>
	</dl>
	<dl class="inputLabelPair">
		<dt><label for="spamuc_tl">' . 'Maximum reaction score received' . '</label></dt>
		<dd>' . $__templater->formNumberBox(array(
		'name' => $__vars['inputName'] . '[reaction_score]',
		'value' => $__vars['option']['option_value']['reaction_score'],
		'min' => '0',
		'id' => 'spamuc_tl',
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