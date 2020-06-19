<?php
// FROM HASH: 60352d6d32e52def07699af5c7b39266
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Routes');
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	if ($__templater->isTraversable($__vars['routeTypes'])) {
		foreach ($__vars['routeTypes'] AS $__vars['routeTypeId'] => $__vars['routeType']) {
			$__compilerTemp1 .= '
					<a href="' . $__templater->func('link', array('routes/add', null, array('type' => $__vars['routeTypeId'], ), ), true) . '"  class="menu-linkRow">' . $__templater->escape($__vars['routeType']) . '</a>
				';
		}
	}
	$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('Add route' . $__vars['xf']['language']['ellipsis'], array(
		'class' => 'menuTrigger',
		'data-xf-click' => 'menu',
		'aria-expanded' => 'false',
		'aria-haspopup' => 'true',
	), '', array(
	)) . '
		<div class="menu" data-menu="menu" aria-hidden="true">
			<div class="menu-content">
				<h3 class="menu-header">' . 'Add route' . $__vars['xf']['language']['ellipsis'] . '</h3>

				' . $__compilerTemp1 . '
			</div>
		</div>
');
	$__finalCompiled .= '

<div class="block">
	<div class="block-container">
		<h2 class="block-tabHeader tabs hScroller" data-xf-init="tabs h-scroller" data-state="replace" role="tablist">
			<span class="hScroller-scroll">
			';
	if ($__templater->isTraversable($__vars['routeTypes'])) {
		foreach ($__vars['routeTypes'] AS $__vars['routeTypeId'] => $__vars['routeType']) {
			$__finalCompiled .= '
				<a class="tabs-tab ' . (($__vars['routeTypeId'] == $__vars['selectedTab']) ? 'is-active' : '') . '"
					role="tab"
					tabindex="0"
					aria-controls="route-' . $__templater->escape($__vars['routeTypeId']) . '"
					id="' . (($__vars['routeTypeId'] != 'public') ? $__templater->escape($__vars['routeTypeId']) : '') . '">' . $__templater->escape($__vars['routeType']) . '</a>
			';
		}
	}
	$__finalCompiled .= '
			</span>
		</h2>
		<ul class="tabPanes">
			';
	if ($__templater->isTraversable($__vars['routeTypes'])) {
		foreach ($__vars['routeTypes'] AS $__vars['routeTypeId'] => $__vars['routeType']) {
			$__finalCompiled .= '
				<li class="block-body ' . (($__vars['routeTypeId'] == $__vars['selectedTab']) ? 'is-active' : '') . '"
					role="tabpanel" id="route-' . $__templater->escape($__vars['routeTypeId']) . '">

					';
			if (!$__templater->test($__vars['routesGrouped'][$__vars['routeTypeId']], 'empty', array())) {
				$__finalCompiled .= '
						';
				$__compilerTemp2 = '';
				if ($__templater->isTraversable($__vars['routesGrouped'][$__vars['routeTypeId']])) {
					foreach ($__vars['routesGrouped'][$__vars['routeTypeId']] AS $__vars['route']) {
						$__compilerTemp2 .= '
								' . $__templater->dataRow(array(
							'hash' => $__vars['route']['route_id'],
							'href' => $__templater->func('link', array('routes/edit', $__vars['route'], ), false),
							'label' => $__templater->escape($__vars['route']['unique_name']),
							'hint' => $__templater->escape($__vars['route']['route_prefix']) . '/' . $__templater->escape($__vars['route']['format']),
							'delete' => $__templater->func('link', array('routes/delete', $__vars['route'], ), false),
							'dir' => 'auto',
						), array()) . '
							';
					}
				}
				$__finalCompiled .= $__templater->dataList('
							' . $__compilerTemp2 . '
						', array(
				)) . '
					';
			} else {
				$__finalCompiled .= '
						<div class="block-row">' . 'No items have been created yet.' . '</div>
					';
			}
			$__finalCompiled .= '
				</li>
			';
		}
	}
	$__finalCompiled .= '
		</ul>
		<div class="block-footer">
			<span class="block-footer-counter">' . $__templater->func('display_totals', array($__vars['totalRoutes'], ), true) . '</span>
		</div>
	</div>
</div>';
	return $__finalCompiled;
});