<?php
// FROM HASH: 85ad946562bead88b3bf06412fda11cf
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => $__vars['inputName'] . '[check]',
		'selected' => $__vars['option']['option_value']['check'],
		'label' => 'Check DNSBL on registration',
		'hint' => $__templater->escape($__vars['option']['explain']),
		'data-hide' => 'true',
		'_dependent' => array('
			<div>' . 'Project Honey Pot key:' . '</div>
			' . $__templater->formTextBox(array(
		'name' => $__vars['inputName'] . '[projectHoneyPotKey]',
		'value' => $__vars['option']['option_value']['projectHoneyPotKey'],
		'class' => 'indented',
	)) . '
			<dfn class="formRow-explain">' . 'Se inserido, a lista negra HTTP <a href="https://www.projecthoneypot.org/index.php" target="_blank">Projeto Honey Pot</a> também será verificada.' . '</dfn>
		', '
			<div>' . 'Ação' . $__vars['xf']['language']['label_separator'] . '</div>
			' . $__templater->formRadio(array(
		'name' => $__vars['inputName'] . '[action]',
		'value' => ($__vars['option']['option_value']['action'] ? $__vars['option']['option_value']['action'] : 'moderate'),
		'listclass' => 'choiceList indented',
	), array(array(
		'value' => 'moderate',
		'label' => 'Require the registration to be manually approved by an admin',
		'_type' => 'option',
	),
	array(
		'value' => 'block',
		'label' => 'Bloquear este registro',
		'_type' => 'option',
	))) . '
		'),
		'_type' => 'option',
	)), array(
		'label' => $__templater->escape($__vars['option']['title']),
		'hint' => $__templater->escape($__vars['hintHtml']),
		'html' => $__templater->escape($__vars['listedHtml']),
	));
	return $__finalCompiled;
});