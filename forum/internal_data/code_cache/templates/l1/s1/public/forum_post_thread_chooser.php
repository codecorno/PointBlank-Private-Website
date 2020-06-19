<?php
// FROM HASH: f70b6a1973d915ad0eb2030967ed5c6b
return array('macros' => array('node_list' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'children' => '!',
		'extras' => '!',
		'depth' => '0',
	), $__arguments, $__vars);
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
'node_list_entry' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'node' => '!',
		'extras' => '!',
		'children' => '!',
		'childExtras' => '!',
		'depth' => '0',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	if ($__vars['depth'] == 0) {
		$__finalCompiled .= '
		';
		if ($__vars['node']['node_type_id'] == 'Category') {
			$__finalCompiled .= '
			' . $__templater->callMacro(null, 'node_list_entry_category_level_0', array(
				'node' => $__vars['node'],
				'extras' => $__vars['extras'],
				'children' => $__vars['children'],
				'childExtras' => $__vars['childExtras'],
				'depth' => $__vars['depth'],
			), $__vars) . '
		';
		} else if (($__vars['node']['node_type_id'] == 'Forum') AND $__templater->method($__vars['node']['Data'], 'canCreateThread', array())) {
			$__finalCompiled .= '
			' . $__templater->callMacro(null, 'node_list_entry_forum_level_0', array(
				'node' => $__vars['node'],
				'extras' => $__vars['extras'],
				'children' => $__vars['children'],
				'childExtras' => $__vars['childExtras'],
				'depth' => $__vars['depth'],
			), $__vars) . '
		';
		} else {
			$__finalCompiled .= '
			' . $__templater->callMacro(null, 'node_list_entry_no_posting_level_0', array(
				'node' => $__vars['node'],
				'extras' => $__vars['extras'],
				'children' => $__vars['children'],
				'childExtras' => $__vars['childExtras'],
				'depth' => $__vars['depth'],
			), $__vars) . '
		';
		}
		$__finalCompiled .= '
	';
	} else {
		$__finalCompiled .= '
		';
		if (($__vars['node']['node_type_id'] == 'Forum') AND $__templater->method($__vars['node']['Data'], 'canCreateThread', array())) {
			$__finalCompiled .= '
			' . $__templater->callMacro(null, 'node_list_entry_forum', array(
				'node' => $__vars['node'],
				'extras' => $__vars['extras'],
				'children' => $__vars['children'],
				'childExtras' => $__vars['childExtras'],
				'depth' => $__vars['depth'],
			), $__vars) . '
		';
		} else {
			$__finalCompiled .= '
			' . $__templater->callMacro(null, 'node_list_entry_no_posting', array(
				'node' => $__vars['node'],
				'extras' => $__vars['extras'],
				'children' => $__vars['children'],
				'childExtras' => $__vars['childExtras'],
				'depth' => $__vars['depth'],
			), $__vars) . '
		';
		}
		$__finalCompiled .= '
	';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},
'node_list_entry_category_level_0' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'node' => '!',
		'extras' => '!',
		'children' => '!',
		'childExtras' => '!',
		'depth' => '0',
	), $__arguments, $__vars);
	$__finalCompiled .= '

	<div class="block block--treeEntryChooser">
		<div class="block-container">
			<h2 class="block-header">
				' . $__templater->escape($__vars['node']['title']) . '
				';
	if ($__vars['node']['description']) {
		$__finalCompiled .= '
					<span class="block-desc">
						' . $__templater->filter($__vars['node']['description'], array(array('strip_tags', array()),), true) . '
					</span>
				';
	}
	$__finalCompiled .= '
			</h2>
			<div class="block-body">
				' . $__templater->callMacro(null, 'node_list', array(
		'children' => $__vars['children'],
		'extras' => $__vars['childExtras'],
		'depth' => ($__vars['depth'] + 1),
	), $__vars) . '
			</div>
		</div>
	</div>
';
	return $__finalCompiled;
},
'node_list_entry_forum_level_0' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'node' => '!',
		'extras' => '!',
		'children' => '!',
		'childExtras' => '!',
		'depth' => '0',
	), $__arguments, $__vars);
	$__finalCompiled .= '

	<div class="block block--treeEntryChooser">
		<div class="block-container">
			' . $__templater->callMacro(null, 'node_list_entry_forum', array(
		'node' => $__vars['node'],
		'extras' => $__vars['extras'],
		'children' => $__vars['children'],
		'childExtras' => $__vars['childExtras'],
		'depth' => $__vars['depth'],
	), $__vars) . '
		</div>
	</div>
';
	return $__finalCompiled;
},
'node_list_entry_no_posting_level_0' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'node' => '!',
		'extras' => '!',
		'children' => '!',
		'childExtras' => '!',
		'depth' => '0',
	), $__arguments, $__vars);
	$__finalCompiled .= '

	<div class="block block--treeEntryChooser">
		<div class="block-container">
			' . $__templater->callMacro(null, 'node_list_entry_no_posting', array(
		'node' => $__vars['node'],
		'extras' => $__vars['extras'],
		'children' => $__vars['children'],
		'childExtras' => $__vars['childExtras'],
		'depth' => $__vars['depth'],
	), $__vars) . '
		</div>
	</div>
';
	return $__finalCompiled;
},
'node_list_entry_forum' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'node' => '!',
		'extras' => '!',
		'children' => '!',
		'childExtras' => '!',
		'depth' => '0',
	), $__arguments, $__vars);
	$__finalCompiled .= '

	<div class="block-row block-row--clickable block-row--separated fauxBlockLink">
		<div class="contentRow contentRow--alignMiddle' . (($__vars['depth'] > 1) ? (' u-depth' . ($__vars['depth'] - 1)) : '') . '">
			<div class="contentRow-main">
				<h2 class="contentRow-title">
					<a href="' . $__templater->func('link', array('forums/post-thread', $__vars['node'], ), true) . '" class="fauxBlockLink-blockLink">
						' . $__templater->escape($__vars['node']['title']) . '
					</a>
				</h2>
				';
	if ($__vars['node']['description']) {
		$__finalCompiled .= '
					<div class="contentRow-minor contentRow-minor--singleLine">
						' . $__templater->filter($__vars['node']['description'], array(array('strip_tags', array()),), true) . '
					</div>
				';
	}
	$__finalCompiled .= '
			</div>
			<div class="contentRow-suffix">
				<dl class="pairs pairs--rows pairs--rows--centered">
					<dt>' . 'Threads' . '</dt>
					<dd>' . $__templater->filter($__vars['extras']['discussion_count'], array(array('number_short', array()),), true) . '</dd>
				</dl>
			</div>
		</div>
	</div>
	' . $__templater->callMacro(null, 'node_list', array(
		'children' => $__vars['children'],
		'extras' => $__vars['childExtras'],
		'depth' => ($__vars['depth'] + 1),
	), $__vars) . '
';
	return $__finalCompiled;
},
'node_list_entry_no_posting' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'node' => '!',
		'extras' => '!',
		'children' => '!',
		'childExtras' => '!',
		'depth' => '0',
	), $__arguments, $__vars);
	$__finalCompiled .= '

	<div class="block-row block-row--separated">
		<div class="contentRow contentRow--alignMiddle' . (($__vars['depth'] > 1) ? (' u-depth' . ($__vars['depth'] - 1)) : '') . ' is-disabled">
			<div class="contentRow-main">
				<h2 class="contentRow-title">
					' . $__templater->escape($__vars['node']['title']) . '
				</h2>
				';
	if ($__vars['node']['node_type_id'] == 'Forum') {
		$__finalCompiled .= '
					<div class="contentRow-minor contentRow-minor--singleLine">
						' . 'You have insufficient privileges to post threads here.' . '
					</div>
				';
	}
	$__finalCompiled .= '
			</div>
			<div class="contentRow-suffix">
				<dl class="pairs pairs--rows pairs--rows--centered">
					<dt>' . 'Threads' . '</dt>
					<dd>' . (($__vars['node']['node_type_id'] == 'Forum') ? $__templater->filter($__vars['extras']['discussion_count'], array(array('number_short', array()),), true) : 'N/A') . '</dd>
				</dl>
			</div>
		</div>
	</div>
	' . $__templater->callMacro(null, 'node_list', array(
		'children' => $__vars['children'],
		'extras' => $__vars['childExtras'],
		'depth' => ($__vars['depth'] + 1),
	), $__vars) . '
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Post thread in...');
	$__finalCompiled .= '

';
	$__templater->setPageParam('head.' . 'metaNoindex', $__templater->preEscaped('<meta name="robots" content="noindex" />'));
	$__finalCompiled .= '

' . $__templater->callMacro(null, 'node_list', array(
		'children' => $__vars['nodeTree'],
		'extras' => $__vars['nodeExtras'],
	), $__vars) . '

' . '

' . '

' . '

' . '

' . '

' . '

';
	return $__finalCompiled;
});