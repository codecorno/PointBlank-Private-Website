<?php
// FROM HASH: cbbd8901a8210273e7e2f0bb249ccb8e
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->includeTemplate('nl_base.less', $__vars) . '
' . $__templater->includeTemplate('nl_mod.less', $__vars) . '
' . $__templater->includeTemplate('nl_custom_bbcodes.less', $__vars) . '
' . $__templater->includeTemplate('nl_style.less', $__vars);
	return $__finalCompiled;
});