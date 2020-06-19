<?php
// FROM HASH: 658a8ec9c6c38dee600f551184688efd
return array('macros' => array('navigation_list' => function($__templater, array $__arguments, array $__vars)
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
		'defaultNavigationId' => '',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	if ($__vars['depth'] == 1) {
		$__finalCompiled .= '
		';
		$__compilerTemp1 = array();
		if ($__vars['navigation']['navigation_id'] == $__vars['xf']['app']['defaultNavigationId']) {
			$__compilerTemp1[] = array(
				'_type' => 'cell',
				'html' => $__templater->escape($__vars['navigation']['title']),
			);
			$__compilerTemp1[] = array(
				'_type' => 'cell',
				'html' => '',
			);
			$__compilerTemp1[] = array(
				'_type' => 'cell',
				'html' => '',
			);
			$__compilerTemp1[] = array(
				'_type' => 'cell',
				'html' => '',
			);
		} else {
			$__compilerTemp1[] = array(
				'href' => $__templater->func('link', array('navigation/edit', $__vars['navigation'], ), false),
				'hash' => $__vars['navigation']['navigation_id'],
				'_type' => 'cell',
				'html' => $__templater->escape($__vars['navigation']['title']),
			);
			$__compilerTemp1[] = array(
				'class' => 'dataList-cell--min dataList-cell--hint',
				'_type' => 'cell',
				'html' => $__templater->escape($__vars['navigation']['display_order']),
			);
			$__compilerTemp1[] = array(
				'name' => 'enabled[' . $__vars['navigation']['navigation_id'] . ']',
				'selected' => $__vars['navigation']['enabled'],
				'submit' => 'true',
				'tooltip' => 'Enable / disable \'' . $__vars['navigation']['title'] . '\'',
				'_type' => 'toggle',
				'html' => '',
			);
			$__compilerTemp1[] = array(
				'href' => ($__templater->method($__vars['navigation'], 'canDelete', array()) ? $__templater->func('link', array('navigation/delete', $__vars['navigation'], ), false) : ''),
				'_type' => 'delete',
				'html' => '',
			);
		}
		$__finalCompiled .= $__templater->dataRow(array(
			'rowtype' => 'subsection',
		), $__compilerTemp1) . '
	';
	} else {
		$__finalCompiled .= '
		' . $__templater->dataRow(array(
		), array(array(
			'href' => $__templater->func('link', array('navigation/edit', $__vars['navigation'], ), false),
			'hash' => $__vars['navigation']['navigation_id'],
			'class' => 'dataList-cell--d' . ($__vars['depth'] - 1),
			'_type' => 'cell',
			'html' => '
				' . $__templater->escape($__vars['navigation']['title']) . '
			',
		),
		array(
			'class' => 'dataList-cell--min dataList-cell--hint',
			'_type' => 'cell',
			'html' => $__templater->escape($__vars['navigation']['display_order']),
		),
		array(
			'name' => 'enabled[' . $__vars['navigation']['navigation_id'] . ']',
			'selected' => $__vars['navigation']['enabled'],
			'class' => 'dataList-cell--separated',
			'submit' => 'true',
			'tooltip' => 'Enable / disable \'' . $__vars['navigation']['title'] . '\'',
			'_type' => 'toggle',
			'html' => '',
		),
		array(
			'href' => ($__templater->method($__vars['navigation'], 'canDelete', array()) ? $__templater->func('link', array('navigation/delete', $__vars['navigation'], ), false) : ''),
			'_type' => 'delete',
			'html' => '',
		))) . '
	';
	}
	$__finalCompiled .= '
	';
	if (($__vars['depth'] == 1) AND $__templater->test($__vars['children'], 'empty', array())) {
		$__finalCompiled .= '
		' . $__templater->dataRow(array(
			'rowclass' => 'dataList-row--note dataList-row--noHover',
		), array(array(
			'colspan' => '3',
			'_type' => 'cell',
			'html' => 'This section is currently empty.',
		))) . '
	';
	} else {
		$__finalCompiled .= '
		' . $__templater->callMacro(null, 'navigation_list', array(
			'children' => $__vars['children'],
			'depth' => ($__vars['depth'] + 1),
		), $__vars) . '
	';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Public navigation');
	$__finalCompiled .= '

';
	$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	<div class="buttonGroup">
		' . $__templater->button('Add navigation', array(
		'href' => $__templater->func('link', array('navigation/add', ), false),
		'icon' => 'add',
	), '', array(
	)) . '
		' . $__templater->button('', array(
		'href' => $__templater->func('link', array('navigation/sort', ), false),
		'icon' => 'sort',
		'overlay' => 'true',
	), '', array(
	)) . '
	</div>
');
	$__finalCompiled .= '

' . $__templater->form('
	<div class="block-outer">
		' . $__templater->callMacro('filter_macros', 'quick_filter', array(
		'key' => 'navigation',
		'class' => 'block-outer-opposite',
	), $__vars) . '
	</div>
	<div class="block-container">
		<div class="block-body">
			' . $__templater->dataList('
				' . $__templater->callMacro(null, 'navigation_list', array(
		'children' => $__vars['tree'],
	), $__vars) . '
			', array(
	)) . '
		</div>
		<div class="block-footer">
			<span class="block-footer-counter">' . $__templater->func('display_totals', array($__templater->method($__vars['tree'], 'getFlattened', array(0, )), ), true) . '</span>
		</div>
	</div>
', array(
		'action' => $__templater->func('link', array('navigation/toggle', ), false),
		'class' => 'block',
		'ajax' => 'true',
	)) . '

' . '

';
	return $__finalCompiled;
});