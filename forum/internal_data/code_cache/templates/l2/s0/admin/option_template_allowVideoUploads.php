<?php
// FROM HASH: d77a24e466d0daa8d7a9b7741f0faa6b
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => $__vars['inputName'] . '[enabled]',
		'selected' => $__vars['option']['option_value']['enabled'],
		'label' => $__templater->escape($__vars['option']['title']),
		'_dependent' => array('
			<div class="inputGroup">
				' . $__templater->formNumberBox(array(
		'name' => $__vars['inputName'] . '[size]',
		'min' => '1',
		'max' => $__vars['max'],
		'value' => ($__vars['option']['option_value']['enabled'] ? $__vars['option']['option_value']['size'] : $__vars['xf']['options']['attachmentMaxFileSize']),
	)) . '
				<span class="inputGroup--splitter"></span>
				<span class="inputGroup-text">' . 'KB' . '</span>
			</div>
		'),
		'_type' => 'option',
	)), array(
		'hint' => $__templater->escape($__vars['hintHtml']),
		'explain' => $__templater->escape($__vars['explainHtml']),
		'html' => $__templater->escape($__vars['listedHtml']),
	));
	return $__finalCompiled;
});