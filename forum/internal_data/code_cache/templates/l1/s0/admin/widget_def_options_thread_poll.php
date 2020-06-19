<?php
// FROM HASH: 8e26cc4ba548e205f9addb32587c0788
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<hr class="formRowSep" />

';
	$__compilerTemp1 = $__vars;
	$__compilerTemp1['contentLink'] = $__templater->preEscaped($__templater->func('link_type', array('public', 'threads', $__vars['content'], ), true));
	$__compilerTemp1['contentTitle'] = $__templater->preEscaped($__templater->escape($__vars['content']['title']));
	$__finalCompiled .= $__templater->includeTemplate('widget_def_options_poll_base', $__compilerTemp1);
	return $__finalCompiled;
});