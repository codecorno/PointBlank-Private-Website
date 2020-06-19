<?php
// FROM HASH: 90ae7494de6237e4754eeb6c18cd7b70
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Closed reports: ' . $__templater->func('date', array($__vars['timeFrame']['0'], ), true) . ' - ' . $__templater->func('date', array($__vars['timeFrame']['1'], ), true) . '');
	$__finalCompiled .= '

';
	$__templater->breadcrumb($__templater->preEscaped('Reports'), $__templater->func('link', array('reports', ), false), array(
	));
	$__finalCompiled .= '

<div class="block">
	<div class="block-outer">
		<div class="block-outer-opposite">
			' . $__templater->button('Find reports', array(
		'class' => 'button--link menuTrigger',
		'data-xf-click' => 'menu',
		'aria-expanded' => 'false',
		'aria-haspopup' => 'true',
	), '', array(
	)) . '
			' . $__templater->callMacro('report_list_macros', 'search_menu', array(), $__vars) . '
		</div>
	</div>
	<div class="block-container">
		<h2 class="block-tabHeader tabs">
			<a href="' . $__templater->func('link', array('reports', ), true) . '" class="tabs-tab">' . 'Active reports' . '</a>
			<a href="' . $__templater->func('link', array('reports/closed', ), true) . '" class="tabs-tab is-active">' . 'Closed reports' . '</a>
		</h2>
		<div class="block-body">
			';
	if (!$__templater->test($__vars['reports'], 'empty', array())) {
		$__finalCompiled .= '
				<div class="structItemContainer">
					';
		if ($__templater->isTraversable($__vars['reports'])) {
			foreach ($__vars['reports'] AS $__vars['report']) {
				$__finalCompiled .= '
						' . $__templater->callMacro('report_list_macros', 'item', array(
					'report' => $__vars['report'],
				), $__vars) . '
					';
			}
		}
		$__finalCompiled .= '
				</div>
			';
	} else {
		$__finalCompiled .= '
				<div class="block-row">' . 'No reports have been closed during this period.' . '</div>
			';
	}
	$__finalCompiled .= '
		</div>
		<div class="block-footer">
			<span class="block-footer-controls">
				' . $__templater->button('&larr; ' . 'Older', array(
		'href' => $__templater->func('link', array('reports/closed', null, array('page' => $__vars['page'] + 1, ), ), false),
	), '', array(
	)) . '
				';
	if ($__vars['page'] > 1) {
		$__finalCompiled .= '
					' . $__templater->button('Newer' . ' &rarr;', array(
			'href' => $__templater->func('link', array('reports/closed', null, array('page' => $__vars['page'] - 1, ), ), false),
		), '', array(
		)) . '
				';
	}
	$__finalCompiled .= '
			</span>
		</div>
	</div>
</div>';
	return $__finalCompiled;
});