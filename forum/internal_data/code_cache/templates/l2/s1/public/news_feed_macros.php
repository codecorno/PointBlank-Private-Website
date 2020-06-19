<?php
// FROM HASH: 1a5329b0a4ed53e6b4fe790a08f70aa2
return array('macros' => array('feed_row' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'item' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	<li class="block-row block-row--separated ' . ($__templater->method($__vars['xf']['visitor'], 'isIgnoring', array($__vars['item']['user_id'], )) ? ' is-ignored' : '') . '" data-author="' . ($__vars['item']['User'] ? $__templater->escape($__vars['item']['User']['username']) : $__templater->escape($__vars['item']['username'])) . '">
		' . $__templater->callMacro(null, 'feed_item', array(
		'item' => $__vars['item'],
	), $__vars) . '
	</li>
';
	return $__finalCompiled;
},
'feed_item' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'item' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	<div class="contentRow' . ($__templater->method($__vars['item'], 'isVisible', array()) ? '' : ' is-deleted') . '">
		<span class="contentRow-figure">
			' . $__templater->func('avatar', array($__vars['item']['User'], 's', false, array(
		'defaultname' => $__vars['item']['username'],
	))) . '
		</span>
		<div class="contentRow-main">
			' . $__templater->filter($__templater->method($__vars['item'], 'render', array()), array(array('raw', array()),), true) . '
		</div>
	</div>
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '

';
	return $__finalCompiled;
});