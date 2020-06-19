<?php
// FROM HASH: da56752f05552d384c54fb6454d75936
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped($__templater->func('prefix', array('thread', $__vars['thread'], 'escaped', ), true) . $__templater->escape($__vars['thread']['title']));
	$__finalCompiled .= '
';
	$__templater->pageParams['pageH1'] = $__templater->preEscaped($__templater->func('prefix', array('thread', $__vars['thread'], ), true) . $__templater->escape($__vars['thread']['title']));
	$__finalCompiled .= '

<div class="block-outer js-threadStatusField">';
	$__compilerTemp1 = '';
	$__compilerTemp2 = '';
	$__compilerTemp2 .= '
				' . $__templater->callMacro('custom_fields_macros', 'custom_fields_view', array(
		'type' => 'threads',
		'group' => 'thread_status',
		'onlyInclude' => $__vars['thread']['Forum']['field_cache'],
		'set' => $__vars['thread']['custom_fields'],
		'wrapperClass' => 'blockStatus-message',
	), $__vars) . '
			';
	if (strlen(trim($__compilerTemp2)) > 0) {
		$__compilerTemp1 .= '
		<div class="blockStatus blockStatus--info">
			' . $__compilerTemp2 . '
		</div>
	';
	}
	$__finalCompiled .= trim('
	' . $__compilerTemp1 . '
') . '</div>

' . $__templater->callMacro('post_macros', 'post', array(
		'post' => $__vars['post'],
		'thread' => $__vars['thread'],
	), $__vars);
	return $__finalCompiled;
});