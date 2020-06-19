<?php
// FROM HASH: a7eb6382da8a0bbe434cf6cf1f974350
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Groups &amp; permissions');
	$__finalCompiled .= '

' . $__templater->callMacro('section_nav_macros', 'section_nav', array(
		'section' => 'groupsAndPermissions',
	), $__vars);
	return $__finalCompiled;
});