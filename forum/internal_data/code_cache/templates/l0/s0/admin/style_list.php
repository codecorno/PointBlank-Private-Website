<?php
// FROM HASH: 63750fa7925cc77080e5880bd2f91e57
return array('macros' => array('style_list' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'children' => '!',
		'defaultStyleId' => '!',
		'depth' => '1',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	if (!$__templater->test($__vars['children'], 'empty', array())) {
		$__finalCompiled .= '
		';
		if ($__templater->isTraversable($__vars['children'])) {
			foreach ($__vars['children'] AS $__vars['child']) {
				$__finalCompiled .= '
			' . $__templater->callMacro(null, 'style_list_entry', array(
					'style' => $__vars['child']['record'],
					'children' => $__vars['child']['children'],
					'defaultStyleId' => $__vars['defaultStyleId'],
					'depth' => $__vars['depth'],
				), $__vars) . '
		';
			}
		}
		$__finalCompiled .= '
	';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},
'style_list_entry' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'style' => '!',
		'children' => '!',
		'defaultStyleId' => '!',
		'depth' => '1',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	$__compilerTemp1 = '';
	if ($__vars['xf']['designer'] AND $__vars['style']['designer_mode']) {
		$__compilerTemp1 .= '
				' . 'Designer mode enabled' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['style']['designer_mode']) . '
			';
	}
	$__compilerTemp2 = array(array(
		'hash' => $__vars['style']['style_id'],
		'href' => ($__vars['style']['style_id'] ? $__templater->func('link', array('styles/edit', $__vars['style'], ), false) : ''),
		'label' => $__templater->escape($__vars['style']['title']),
		'explain' => $__templater->escape($__vars['style']['description']),
		'class' => 'dataList-cell--d' . $__vars['depth'],
		'hint' => $__compilerTemp1,
		'_type' => 'main',
		'html' => '',
	));
	if ($__vars['style']['style_id']) {
		$__compilerTemp2[] = array(
			'name' => 'user_selectable[' . $__vars['style']['style_id'] . ']',
			'selected' => $__vars['style']['user_selectable'],
			'class' => 'dataList-cell--separated u-hideMedium',
			'submit' => 'true',
			'tooltip' => 'Enable / disable \'' . $__vars['style']['title'] . '\'',
			'_type' => 'toggle',
			'html' => '',
		);
		$__compilerTemp2[] = array(
			'name' => 'default_style_id',
			'type' => 'radio',
			'value' => $__vars['style']['style_id'],
			'selected' => (($__vars['defaultStyleId'] == $__vars['style']['style_id']) ? 1 : 0),
			'class' => 'dataList-cell--separated',
			'submit' => 'true',
			'_type' => 'toggle',
			'html' => '',
		);
	} else {
		$__compilerTemp2[] = array(
			'class' => 'u-hideMedium',
			'_type' => 'cell',
			'html' => '&nbsp;',
		);
		$__compilerTemp2[] = array(
			'_type' => 'cell',
			'html' => '&nbsp;',
		);
	}
	if ($__templater->method($__vars['style'], 'canEdit', array())) {
		$__compilerTemp2[] = array(
			'href' => $__templater->func('link', array('styles/templates', $__vars['style'], ), false),
			'class' => 'dataList-cell--responsiveMenuItem',
			'_type' => 'action',
			'html' => 'Templates',
		);
		$__compilerTemp2[] = array(
			'href' => $__templater->func('link', array('styles/style-properties', $__vars['style'], ), false),
			'class' => 'dataList-cell--responsiveMenuItem',
			'_type' => 'action',
			'html' => 'Style properties',
		);
	} else {
		$__compilerTemp2[] = array(
			'class' => 'dataList-cell--alt dataList-cell--fauxResponsiveMenuItem',
			'_type' => 'cell',
			'html' => ' ',
		);
		$__compilerTemp2[] = array(
			'class' => 'dataList-cell--alt dataList-cell--fauxResponsiveMenuItem',
			'_type' => 'cell',
			'html' => '&nbsp;',
		);
	}
	if ($__vars['style']['style_id']) {
		$__compilerTemp2[] = array(
			'label' => '&#8226;&#8226;&#8226;',
			'_type' => 'popup',
			'html' => '
				<div class="menu" data-menu="menu" aria-hidden="true" data-menu-builder="dataList">
					<div class="menu-content">
						<h3 class="menu-header">' . 'More options' . '</h3>
						<a href="' . $__templater->func('link', array('styles/export', $__vars['style'], ), true) . '" class="menu-linkRow" data-xf-click="overlay">' . 'Export' . '</a>
						<a href="' . $__templater->func('link', array('styles/customized-components', $__vars['style'], ), true) . '" class="menu-linkRow">' . 'Customized components' . '</a>
						<div class="js-menuBuilderTarget u-showMediumBlock"></div>
					</div>
				</div>
			',
		);
	} else {
		$__compilerTemp2[] = array(
			'class' => 'dataList-cell--alt',
			'_type' => 'cell',
			'html' => '&nbsp;',
		);
	}
	if ($__vars['style']['style_id']) {
		$__compilerTemp2[] = array(
			'href' => $__templater->func('link', array('styles/delete', $__vars['style'], ), false),
			'_type' => 'delete',
			'html' => '',
		);
	} else {
		$__compilerTemp2[] = array(
			'class' => 'dataList-cell--alt',
			'_type' => 'cell',
			'html' => '&nbsp;',
		);
	}
	$__finalCompiled .= $__templater->dataRow(array(
	), $__compilerTemp2) . '
	' . $__templater->callMacro(null, 'style_list', array(
		'children' => $__vars['children'],
		'depth' => ($__vars['depth'] + 1),
		'defaultStyleId' => $__vars['defaultStyleId'],
	), $__vars) . '
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Styles');
	$__finalCompiled .= '

';
	$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('Add style', array(
		'href' => $__templater->func('link', array('styles/add', ), false),
		'icon' => 'add',
	), '', array(
	)) . '
	' . $__templater->button('', array(
		'href' => $__templater->func('link', array('styles/import', ), false),
		'icon' => 'import',
	), '', array(
	)) . '
');
	$__finalCompiled .= '

' . '

' . '

';
	if (!$__templater->test($__vars['styleTree'], 'empty', array())) {
		$__finalCompiled .= '
	' . $__templater->form('
		<div class="block-outer">
			' . $__templater->callMacro('filter_macros', 'quick_filter', array(
			'key' => 'styles',
			'class' => 'block-outer-opposite',
		), $__vars) . '
		</div>
		<div class="block-container">
			<div class="block-body">
				' . $__templater->dataList('
					' . $__templater->callMacro(null, 'style_list', array(
			'children' => $__vars['styleTree'],
			'defaultStyleId' => $__vars['xf']['options']['defaultStyleId'],
		), $__vars) . '
				', array(
		)) . '
			</div>
			<div class="block-footer">
				<span class="block-footer-counter">' . $__templater->func('display_totals', array($__templater->method($__vars['styleTree'], 'getFlattened', array(0, )), ), true) . '</span>
			</div>
		</div>
	', array(
			'action' => $__templater->func('link', array('styles/toggle', ), false),
			'class' => 'block',
			'ajax' => 'true',
		)) . '
';
	}
	return $__finalCompiled;
});