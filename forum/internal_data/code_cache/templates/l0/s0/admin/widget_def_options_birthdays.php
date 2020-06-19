<?php
// FROM HASH: 68b928903aa32a1e4d430f409342f834
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<hr class="formRowSep" />

' . $__templater->formNumberBoxRow(array(
		'name' => 'options[limit]',
		'value' => $__vars['options']['limit'],
		'min' => '0',
	), array(
		'label' => 'Maximum entries',
		'explain' => 'Controls the number of avatars that can be shown in this widget. A value of 0 disables the limit.',
	));
	return $__finalCompiled;
});