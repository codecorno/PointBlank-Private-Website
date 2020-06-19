<?php
// FROM HASH: 08c237828d20ac3cbd5e2be2e0cc0d29
return array('macros' => array('section_nav' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'section' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	$__vars['sectionNav'] = $__templater->method($__templater->method($__vars['xf']['app'], 'get', array('navigation.admin', )), 'getTree', array($__vars['section'], ));
	$__finalCompiled .= '
	' . $__templater->callMacro(null, 'section_nav_block', array(
		'sectionNav' => $__vars['sectionNav'],
	), $__vars) . '
';
	return $__finalCompiled;
},
'section_nav_block' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'sectionNav' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	<div class="block">
		<div class="block-container">
			<h2 class="block-header">' . 'Sub-sections' . '</h2>
			<div class="block-body">
				' . $__templater->callMacro(null, 'navigation_list', array(
		'children' => $__vars['sectionNav'],
	), $__vars) . '
			</div>
		</div>
	</div>
';
	return $__finalCompiled;
},
'navigation_list' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'children' => '!',
		'depth' => '1',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	if ($__templater->isTraversable($__vars['children'])) {
		foreach ($__vars['children'] AS $__vars['child']) {
			$__finalCompiled .= '
		' . $__templater->callMacro(null, 'navigation_list_entry', array(
				'navigation' => $__vars['child']['record'],
				'children' => $__vars['child']['children'],
				'depth' => $__vars['depth'],
			), $__vars) . '
	';
		}
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},
'navigation_list_entry' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'navigation' => '!',
		'children' => '!',
		'depth' => '1',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	if (($__vars['depth'] == 1) AND (!$__vars['navigation']['link'])) {
		$__finalCompiled .= '
		<div class="block-minorHeader">
			<div class="u-depth' . ($__vars['depth'] - 1) . '">
				';
		if ($__vars['navigation']['icon']) {
			$__finalCompiled .= $__templater->fontAwesome('fa-fw ' . $__templater->escape($__vars['navigation']['icon']), array(
			));
		}
		$__finalCompiled .= '
				' . $__templater->escape($__vars['navigation']['title']) . '
			</div>
		</div>
	';
	} else {
		$__finalCompiled .= '
		<a href="' . $__templater->func('link', array($__vars['navigation']['link'], ), true) . '" class="blockLink">
			<div class="u-depth' . ($__vars['depth'] - 1) . '">' . $__templater->escape($__vars['navigation']['title']) . '</div>
		</a>
	';
	}
	$__finalCompiled .= '
	' . $__templater->callMacro(null, 'navigation_list', array(
		'children' => $__vars['children'],
		'depth' => ($__vars['depth'] + 1),
	), $__vars) . '
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '

' . '

' . '

';
	return $__finalCompiled;
});