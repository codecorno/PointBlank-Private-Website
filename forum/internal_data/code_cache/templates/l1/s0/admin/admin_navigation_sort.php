<?php
// FROM HASH: 3fdd56e003d36b550bc62fdb14800edd
return array('macros' => array('navigation_list' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'children' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	<ol class="nestable-list">
		';
	if ($__templater->isTraversable($__vars['children'])) {
		foreach ($__vars['children'] AS $__vars['id'] => $__vars['child']) {
			$__finalCompiled .= '
			' . $__templater->callMacro(null, 'navigation_list_entry', array(
				'nav' => $__vars['child']['record'],
				'children' => $__vars['child']['children'],
			), $__vars) . '
		';
		}
	}
	$__finalCompiled .= '
	</ol>
';
	return $__finalCompiled;
},
'navigation_list_entry' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'nav' => '!',
		'children' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	<li class="nestable-item" data-id="' . $__templater->escape($__vars['nav']['navigation_id']) . '">
		<div class="nestable-handle" aria-label="' . $__templater->filter('Drag handle', array(array('for_attr', array()),), true) . '">' . $__templater->fontAwesome('fa-bars', array(
	)) . '</div>
		<div class="nestable-content">' . $__templater->escape($__vars['nav']['title']) . '</div>
		';
	if (!$__templater->test($__vars['children'], 'empty', array())) {
		$__finalCompiled .= '
			' . $__templater->callMacro(null, 'navigation_list', array(
			'children' => $__vars['children'],
		), $__vars) . '
		';
	}
	$__finalCompiled .= '
	</li>
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Sort admin navigation');
	$__finalCompiled .= '

' . $__templater->callMacro('public:nestable_macros', 'setup', array(), $__vars) . '

' . $__templater->form('
	<div class="block-container">
		<div class="block-body">
			<div class="nestable-container" data-xf-init="nestable" data-parent-id="">
				' . $__templater->callMacro(null, 'navigation_list', array(
		'children' => $__vars['navTree'],
	), $__vars) . '
				' . $__templater->formHiddenVal('navigation', '', array(
	)) . '
			</div>
		</div>
		' . $__templater->formSubmitRow(array(
		'icon' => 'save',
	), array(
		'rowtype' => 'simple',
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('admin-navigation/sort', ), false),
		'class' => 'block',
		'ajax' => 'true',
	)) . '

' . '

';
	return $__finalCompiled;
});