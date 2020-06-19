<?php
// FROM HASH: 4f5db66d9f37e7dce6734dfc9dc3a606
return array('macros' => array('debug' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'controller' => '!',
		'action' => '!',
		'template' => '!',
		'link' => true,
		'classInfo' => true,
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	if ($__vars['xf']['debug']) {
		$__finalCompiled .= '
		<ul class="listInline listInline--bullet listInline--selfInline">
			<li><dl class="pairs pairs--inline debugResolution" title="' . $__templater->filter('Responsive width', array(array('for_attr', array()),), true) . '">
				<dt class="debugResolution-label">' . 'Width' . '</dt>
				<dd class="debugResolution-output"></dd>
			</dl></li>
			<li><dl class="pairs pairs--inline">
				<dt>' . 'Queries' . '</dt>
				<dd><!--XF:QUERIES--></dd>
			</dl></li>
			<li><dl class="pairs pairs--inline">
				';
		if ($__vars['link']) {
			$__finalCompiled .= '
					<dt>' . 'Time' . '</dt>
					<dd><a href="' . $__templater->func('debug_url', array(), true) . '" rel="nofollow"><!--XF:PAGE_TIME-->s</a></dd>
				';
		} else {
			$__finalCompiled .= '
					<dt>' . 'Time' . '</dt>
					<dd><!--XF:PAGE_TIME-->s</dd>
				';
		}
		$__finalCompiled .= '
			</dl></li>
			<li><dl class="pairs pairs--inline">
				<dt>' . 'Memory' . '</dt>
				<dd><!--XF:MEMORY_PEAK--></dd>
			</dl></li>
			';
		if ($__vars['classInfo']) {
			$__finalCompiled .= '
				<li class="cog-info">
					' . $__templater->callMacro(null, 'class_info', array(
				'controller' => $__vars['controller'],
				'action' => $__vars['action'],
				'template' => $__vars['template'],
			), $__vars) . '
				</li>
			';
		}
		$__finalCompiled .= '
		</ul>
	';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},
'class_info' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'controller' => '!',
		'action' => '!',
		'template' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	<a data-xf-init="tooltip" data-click-hide="false"
		data-trigger="hover focus click"
		title="' . $__templater->escape($__vars['controller']) . ' :: ' . $__templater->escape($__vars['action']) . ' &middot; ' . $__templater->escape($__vars['template']) . '"
		role="button" tabindex="0">' . $__templater->fontAwesome('fa-cog', array(
	)) . '</a>
';
	return $__finalCompiled;
},
'debug_resolution' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	if ($__vars['xf']['debug']) {
		$__finalCompiled .= '
		<span class="debugResolution" title="' . $__templater->filter('Responsive width', array(array('for_attr', array()),), true) . '">
			<span class="debugResolution-label">' . 'Responsive width' . $__vars['xf']['language']['label_separator'] . '</span>
			<span class="debugResolution-output"></span>
		</span>
	';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '

' . '

';
	return $__finalCompiled;
});