<?php
// FROM HASH: b8f8182a6b929b90dc2e1959e079ec43
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<div class="menu-row menu-row--alt menu-row--close">
	<div data-xf-init="bookmark-label-filter" data-target=".js-bookmarksMenuBody">
		' . $__templater->callMacro('bookmark_macros', 'filter', array(
		'label' => $__vars['label'],
		'allLabels' => $__vars['allLabels'],
	), $__vars) . '
	</div>
</div>
<div class="menu-scroller">
	<ol class="listPlain">
		';
	if (!$__templater->test($__vars['bookmarks'], 'empty', array())) {
		$__finalCompiled .= '
			';
		if ($__templater->isTraversable($__vars['bookmarks'])) {
			foreach ($__vars['bookmarks'] AS $__vars['bookmark']) {
				$__finalCompiled .= '
				<li class="menu-row menu-row--close menu-row--separated">
					' . $__templater->callMacro('bookmark_macros', 'row', array(
					'bookmark' => $__vars['bookmark'],
					'content' => $__vars['bookmark']['Content'],
				), $__vars) . '
				</li>
			';
			}
		}
		$__finalCompiled .= '
			';
	} else {
		$__finalCompiled .= '
			';
		if ($__vars['label']) {
			$__finalCompiled .= '
				<li class="menu-row">' . 'No items matched your filter.' . '</li>
			';
		} else {
			$__finalCompiled .= '
				<li class="menu-row">' . 'You have not added any bookmarks yet.' . '</li>
			';
		}
		$__finalCompiled .= '
		';
	}
	$__finalCompiled .= '
	</ol>
</div>';
	return $__finalCompiled;
});