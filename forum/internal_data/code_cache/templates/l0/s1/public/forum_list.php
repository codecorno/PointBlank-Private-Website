<?php
// FROM HASH: 2247b92fafc8207f121b3be26cdce591
return array('macros' => array('node_list_entry' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'node' => '!',
		'extras' => '!',
		'children' => '!',
		'childExtras' => '!',
		'depth' => '1',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	$__templater->includeCss('node_list.less');
	$__finalCompiled .= '
	';
	$__vars['nodeTemplate'] = $__templater->method($__vars['node'], 'getNodeTemplateRenderer', array($__vars['depth'], ));
	$__finalCompiled .= '
	';
	if ($__vars['nodeTemplate']['macro']) {
		$__finalCompiled .= '
		' . $__templater->callMacro($__vars['nodeTemplate']['template'], $__vars['nodeTemplate']['macro'], array(
			'node' => $__vars['node'],
			'extras' => $__vars['extras'],
			'children' => $__vars['children'],
			'childExtras' => $__vars['childExtras'],
			'depth' => $__vars['depth'],
		), $__vars) . '
	';
	} else if ($__vars['nodeTemplate']['template']) {
		$__finalCompiled .= '
		' . $__templater->includeTemplate($__vars['nodeTemplate']['template'], $__vars) . '
	';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},
'node_list' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'children' => '!',
		'extras' => '!',
		'depth' => '1',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	$__templater->includeCss('node_list.less');
	$__finalCompiled .= '
	';
	if ($__templater->isTraversable($__vars['children'])) {
		foreach ($__vars['children'] AS $__vars['id'] => $__vars['child']) {
			$__finalCompiled .= '
		' . $__templater->callMacro(null, 'node_list_entry', array(
				'node' => $__vars['child']['record'],
				'extras' => $__vars['extras'][$__vars['id']],
				'children' => $__vars['child']['children'],
				'childExtras' => $__vars['extras'],
				'depth' => $__vars['depth'],
			), $__vars) . '
	';
		}
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},
'sub_node_list' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'children' => '!',
		'childExtras' => '!',
		'depth' => '3',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	$__compilerTemp1 = '';
	$__compilerTemp1 .= '
			' . $__templater->callMacro('forum_list', 'node_list', array(
		'children' => $__vars['children'],
		'extras' => $__vars['childExtras'],
		'depth' => ($__vars['depth'] + 1),
	), $__vars) . '
		';
	if (strlen(trim($__compilerTemp1)) > 0) {
		$__finalCompiled .= '
		<ol>
		' . $__compilerTemp1 . '
		</ol>
	';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},
'sub_nodes_flat' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'children' => '!',
		'childExtras' => '!',
		'depth' => '3',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	$__templater->includeCss('node_list.less');
	$__finalCompiled .= '
	';
	$__compilerTemp1 = '';
	$__compilerTemp1 .= '
				' . $__templater->callMacro('forum_list', 'node_list', array(
		'children' => $__vars['children'],
		'extras' => $__vars['childExtras'],
		'depth' => ($__vars['depth'] + 1),
	), $__vars) . '
			';
	if (strlen(trim($__compilerTemp1)) > 0) {
		$__finalCompiled .= '
		<div class="node-subNodesFlat">
			<span class="node-subNodesLabel">' . 'Sub-forums' . $__vars['xf']['language']['label_separator'] . '</span>
			<ol class="node-subNodeFlatList">
			' . $__compilerTemp1 . '
			</ol>
		</div>
	';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},
'sub_nodes_menu' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'children' => '!',
		'childExtras' => '!',
		'depth' => '3',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	$__templater->includeCss('node_list.less');
	$__finalCompiled .= '
	';
	$__compilerTemp1 = '';
	$__compilerTemp1 .= '
						' . $__templater->callMacro('forum_list', 'node_list', array(
		'children' => $__vars['children'],
		'extras' => $__vars['childExtras'],
		'depth' => ($__vars['depth'] + 1),
	), $__vars) . '
					';
	if (strlen(trim($__compilerTemp1)) > 0) {
		$__finalCompiled .= '
		<div class="node-subNodeMenu">
			<a class="menuTrigger" data-xf-click="menu" role="button" tabindex="0" aria-expanded="false" aria-haspopup="true">' . 'Sub-forums' . '</a>
			<div class="menu" data-menu="menu" aria-hidden="true">
				<div class="menu-content">
					<h4 class="menu-header">' . 'Sub-forums' . '</h4>
					<ol class="subNodeMenu">
					' . $__compilerTemp1 . '
					</ol>
				</div>
			</div>
		</div>
	';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageH1'] = $__templater->preEscaped($__templater->escape($__vars['xf']['options']['boardTitle']));
	$__finalCompiled .= '
';
	if ($__vars['xf']['options']['forumsDefaultPage'] != 'forums') {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Forum list');
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

' . $__templater->callMacro('metadata_macros', 'metadata', array(
		'description' => $__vars['xf']['options']['boardDescription'],
		'canonicalUrl' => $__templater->func('link', array('canonical:' . $__vars['selfRoute'], ), false),
	), $__vars) . '


';
	$__compilerTemp1 = $__vars;
	$__compilerTemp1['pageSelected'] = $__templater->preEscaped('forums');
	$__templater->wrapTemplate('forum_overview_wrapper', $__compilerTemp1);
	$__finalCompiled .= '

';
	$__templater->modifySidebarHtml('_xfWidgetPositionSidebarForumListSidebar', $__templater->widgetPosition('forum_list_sidebar', array()), 'replace');
	$__finalCompiled .= '

' . $__templater->widgetPosition('forum_list_above_nodes', array()) . '
' . $__templater->callMacro(null, 'node_list', array(
		'children' => $__vars['nodeTree'],
		'extras' => $__vars['nodeExtras'],
	), $__vars) . '
' . $__templater->widgetPosition('forum_list_below_nodes', array()) . '

';
	$__templater->setPageParam('head.' . 'rss_forum', $__templater->preEscaped('<link rel="alternate" type="application/rss+xml" title="' . $__templater->filter('RSS feed for ' . $__vars['xf']['options']['boardTitle'] . '', array(array('for_attr', array()),), true) . '" href="' . $__templater->func('link', array('forums/index.rss', '-', ), true) . '" />'));
	$__finalCompiled .= '

' . '

' . '

' . '

' . '

';
	return $__finalCompiled;
});