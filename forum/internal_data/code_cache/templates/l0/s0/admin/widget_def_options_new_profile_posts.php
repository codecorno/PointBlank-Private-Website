<?php
// FROM HASH: ba9fea873f65044e91cc22a6a06668a2
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<hr class="formRowSep" />

' . $__templater->formNumberBoxRow(array(
		'name' => 'options[limit]',
		'value' => $__vars['options']['limit'],
		'min' => '1',
	), array(
		'label' => 'Maximum entries',
	)) . '

' . $__templater->formRadioRow(array(
		'name' => 'options[style]',
		'value' => ($__vars['options']['style'] ?: 'simple'),
	), array(array(
		'value' => 'simple',
		'label' => 'Simple',
		'hint' => 'A simple view, designed for narrow spaces such as sidebars.',
		'_type' => 'option',
	),
	array(
		'value' => 'full',
		'label' => 'Full',
		'hint' => 'A full size view, displaying similar to on profiles.',
		'_type' => 'option',
	)), array(
		'label' => 'Display style',
	)) . '

' . $__templater->formRadioRow(array(
		'name' => 'options[filter]',
		'value' => ($__vars['options']['filter'] ?: 'latest'),
	), array(array(
		'value' => 'latest',
		'label' => 'Latest',
		'hint' => 'A list of any profile post which has been recently posted (default for guests).',
		'_type' => 'option',
	),
	array(
		'value' => 'followed',
		'label' => 'Followed',
		'hint' => 'A list of profile posts by the author or people they are following.',
		'_type' => 'option',
	)), array(
		'label' => 'Filter',
	));
	return $__finalCompiled;
});