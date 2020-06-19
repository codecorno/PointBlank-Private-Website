<?php
// FROM HASH: ca2613382fe9f073da32d17180dc32b0
return array('macros' => array('button' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'buttonClass' => 'button--link',
		'variant' => '',
		'label' => null,
		'tooltip' => null,
	), $__arguments, $__vars);
	$__finalCompiled .= '

	' . $__templater->button('

		<span class="inlineModButton ' . $__templater->escape($__vars['variant']) . '">
			<i aria-hidden="true"></i>
			<span class="inlineModButton-label">' . ($__templater->escape($__vars['label']) ?: 'Moderation') . '</span>
			<span class="inlineModButton-count js-inlineModCounter">0</span>
		</span>
	', array(
		'class' => $__vars['buttonClass'] . ' js-inlineModTrigger',
		'data-xf-init' => (($__vars['tooltip'] === false) ? '' : 'tooltip'),
		'title' => (($__vars['tooltip'] === false) ? '' : ($__vars['tooltip'] ?: 'Number of items selected for moderation')),
	), '', array(
	)) . '
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';

	return $__finalCompiled;
});