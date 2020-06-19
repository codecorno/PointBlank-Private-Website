<?php
// FROM HASH: 5c95df04298cb7d3a741d3d871fbacc2
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Bookmarks');
	$__finalCompiled .= '

';
	$__templater->wrapTemplate('account_wrapper', $__vars);
	$__finalCompiled .= '

<div class="block">
	<div class="block-container">
		<div class="block-filterBar">
			<div class="filterBar">
				';
	$__compilerTemp1 = '';
	$__compilerTemp1 .= '
							';
	if ($__vars['label']) {
		$__compilerTemp1 .= '
								<li><a href="' . $__templater->func('link', array('account/bookmarks', ), true) . '"
									class="filterBar-filterToggle" data-xf-init="tooltip" title="' . $__templater->filter('Remove this filter', array(array('for_attr', array()),), true) . '">
									<span class="filterBar-filterToggle-label">' . 'Label' . $__vars['xf']['language']['label_separator'] . '</span>
									' . $__templater->escape($__vars['label']) . '</a></li>
							';
	}
	$__compilerTemp1 .= '
						';
	if (strlen(trim($__compilerTemp1)) > 0) {
		$__finalCompiled .= '
					<ul class="filterBar-filters">
						' . $__compilerTemp1 . '
					</ul>
				';
	}
	$__finalCompiled .= '

				<a class="filterBar-menuTrigger" data-xf-click="menu" role="button" tabindex="0" aria-expanded="false" aria-haspopup="true">' . 'Filters' . '</a>
				<div class="menu menu--wide" data-menu="menu" aria-hidden="true">
					<div class="menu-content">
						<h4 class="menu-header">' . 'Filter by label' . $__vars['xf']['language']['label_separator'] . '</h4>
						' . $__templater->form('
							<div class="menu-row menu-row--separated">
								' . $__templater->callMacro('bookmark_macros', 'filter', array(
		'name' => 'label',
		'label' => $__vars['label'],
		'allLabels' => $__vars['allLabels'],
		'placeholder' => '',
	), $__vars) . '
							</div>
							<div class="menu-footer">
								<span class="menu-footer-controls">
									' . $__templater->button('Filter', array(
		'type' => 'submit',
		'class' => 'button--primary',
	), '', array(
	)) . '
								</span>
							</div>
						', array(
		'action' => $__templater->func('link', array('account/bookmarks', ), false),
	)) . '
					</div>
				</div>
			</div>
		</div>
		<div class="block-body">
			';
	if (!$__templater->test($__vars['bookmarks'], 'empty', array())) {
		$__finalCompiled .= '
				<ol class="listPlain">
					';
		if ($__templater->isTraversable($__vars['bookmarks'])) {
			foreach ($__vars['bookmarks'] AS $__vars['bookmark']) {
				$__finalCompiled .= '
						<li class="block-row block-row--separated">
							' . $__templater->callMacro('bookmark_macros', 'row', array(
					'bookmark' => $__vars['bookmark'],
					'content' => $__vars['bookmark']['Content'],
				), $__vars) . '
						</li>
					';
			}
		}
		$__finalCompiled .= '
				</ol>
			';
	} else {
		$__finalCompiled .= '
				';
		if ($__vars['label']) {
			$__finalCompiled .= '
					<div class="block-row">' . 'No items matched your filter.' . '</div>
				';
		} else {
			$__finalCompiled .= '
					<div class="block-row">' . 'You have not added any bookmarks yet.' . '</div>
				';
		}
		$__finalCompiled .= '
			';
	}
	$__finalCompiled .= '
		</div>
	</div>

	' . $__templater->func('page_nav', array(array(
		'link' => 'account/bookmarks',
		'params' => array('label' => $__vars['label'], 'difference' => $__vars['paginationDifference'], ),
		'page' => $__vars['page'],
		'total' => $__vars['totalBookmarks'],
		'wrapperclass' => 'block-outer block-outer--after',
		'perPage' => $__vars['perPage'],
	))) . '
</div>';
	return $__finalCompiled;
});