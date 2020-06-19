<?php
// FROM HASH: 42be69e68cdda0f4ea482a90c5a26f65
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__compilerTemp1 = '';
	if ($__vars['formatParams']['units']) {
		$__compilerTemp1 .= '<span class="inputGroup-text">' . $__templater->escape($__vars['formatParams']['units']) . '</span>';
	}
	$__finalCompiled .= $__templater->formRow('

	<div class="inputGroup inputGroup--numbers">
		' . $__templater->formNumberBox(array(
		'name' => $__vars['inputName'] . '[min]',
		'value' => $__vars['option']['option_value']['min'],
		'placeholder' => 'Minimum',
		'min' => ($__vars['formatParams']['min'] ?: 0),
		'max' => ($__vars['formatParams']['max'] ?: null),
	)) . '
		<span class="inputGroup-text">' . ($__vars['formatParams']['between'] ? $__templater->escape($__vars['formatParams']['between']) : '-') . '</span>
		' . $__templater->formNumberBox(array(
		'name' => $__vars['inputName'] . '[max]',
		'value' => $__vars['option']['option_value']['max'],
		'placeholder' => 'Maximum',
		'min' => ($__vars['formatParams']['min'] ?: 0),
		'max' => ($__vars['formatParams']['max'] ?: null),
	)) . '
		' . $__compilerTemp1 . '
	</div>
', array(
		'rowtype' => 'input',
		'label' => $__templater->escape($__vars['option']['title']),
		'hint' => $__templater->escape($__vars['hintHtml']),
		'explain' => $__templater->escape($__vars['explainHtml']),
		'html' => $__templater->escape($__vars['listedHtml']),
	));
	return $__finalCompiled;
});