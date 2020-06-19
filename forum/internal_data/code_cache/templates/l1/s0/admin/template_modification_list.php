<?php
// FROM HASH: 600baa5a1187bf9c2e6253860e0fbc82
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Template modifications');
	$__finalCompiled .= '

';
	$__templater->setPageParam('skipBreadcrumb', 'templateModifications');
	$__finalCompiled .= '

';
	if ($__vars['canCreateModification']) {
		$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('
		' . 'Add template modification' . '
	', array(
			'href' => $__templater->func('link', array('template-modifications/add', '', array('type' => $__vars['type'], ), ), false),
			'icon' => 'add',
		), '', array(
		)) . '
');
	}
	$__finalCompiled .= '

';
	$__templater->includeCss('template_modification.less');
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	if ($__templater->isTraversable($__vars['types'])) {
		foreach ($__vars['types'] AS $__vars['typeId'] => $__vars['typeName']) {
			$__compilerTemp1 .= '
				<a href="' . $__templater->func('link', array('template-modifications', '', array('type' => $__vars['typeId'], ), ), true) . '"
					class="tabs-tab ' . (($__vars['typeId'] == $__vars['type']) ? 'is-active' : '') . '">' . $__templater->escape($__vars['typeName']) . '</a>
			';
		}
	}
	$__compilerTemp2 = '';
	if ($__vars['groupedModifications']) {
		$__compilerTemp2 .= '
			<div class="block-body">
				';
		$__compilerTemp3 = '';
		if ($__templater->isTraversable($__vars['groupedModifications'])) {
			foreach ($__vars['groupedModifications'] AS $__vars['addOnId'] => $__vars['modifications']) {
				$__compilerTemp3 .= '
						<tbody class="dataList-rowGroup">
							';
				if ($__vars['addOnId']) {
					$__compilerTemp3 .= '
								' . $__templater->dataRow(array(
						'rowtype' => 'subsection',
						'rowclass' => 'dataList-row--noHover',
					), array(array(
						'colspan' => '4',
						'_type' => 'cell',
						'html' => $__templater->escape($__vars['addOns'][$__vars['addOnId']]['title']),
					))) . '
								';
				} else {
					$__compilerTemp3 .= '
								' . $__templater->dataRow(array(
						'rowtype' => 'subsection',
						'rowclass' => 'dataList-row--noHover',
					), array(array(
						'colspan' => '4',
						'_type' => 'cell',
						'html' => 'Custom modifications',
					))) . '
							';
				}
				$__compilerTemp3 .= '
							';
				if ($__templater->isTraversable($__vars['modifications'])) {
					foreach ($__vars['modifications'] AS $__vars['modification']) {
						$__compilerTemp3 .= '
								' . $__templater->dataRow(array(
							'label' => $__templater->escape($__vars['modification']['template']),
							'hint' => $__templater->escape($__vars['modification']['description']),
							'delete' => ($__templater->method($__vars['modification'], 'canEdit', array()) ? $__templater->func('link', array('template-modifications/delete', $__vars['modification'], ), false) : null),
							'href' => $__templater->func('link', array('template-modifications/edit', $__vars['modification'], ), false),
						), array(array(
							'name' => 'enabled[' . $__vars['modification']['modification_id'] . ']',
							'selected' => $__vars['modification']['enabled'],
							'class' => 'dataList-cell--separated',
							'submit' => 'true',
							'_type' => 'toggle',
							'html' => '',
						),
						array(
							'href' => $__templater->func('link', array('template-modifications/log', $__vars['modification'], ), false),
							'overlay' => 'true',
							'_type' => 'action',
							'html' => '
										<span class="templateModApply templateModApply--ok' . ($__vars['modification']['log_summary']['ok'] ? ' is-active' : '') . '">' . $__templater->escape($__vars['modification']['log_summary']['ok']) . '</span>
										/ <span class="templateModApply templateModApply--notFound' . ($__vars['modification']['log_summary']['not_found'] ? ' is-active' : '') . '">' . $__templater->escape($__vars['modification']['log_summary']['not_found']) . '</span>
										/ <span class="templateModApply templateModApply--error' . ($__vars['modification']['log_summary']['error'] ? ' is-active' : '') . '">' . $__templater->escape($__vars['modification']['log_summary']['error']) . '</span>
									',
						))) . '
							';
					}
				}
				$__compilerTemp3 .= '
						</tbody>
					';
			}
		}
		$__compilerTemp2 .= $__templater->dataList('
					' . $__compilerTemp3 . '
				', array(
		)) . '
			</div>
			<div class="block-footer">
				<span class="block-footer-counter">' . $__templater->func('display_totals', array($__vars['modificationCount'], ), true) . '</span>
			</div>
		';
	} else {
		$__compilerTemp2 .= '
			<div class="block-body block-row">' . 'No template modifications have been defined yet.' . '</div>
		';
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-outer">
		' . $__templater->callMacro('filter_macros', 'quick_filter', array(
		'key' => 'template-modifications',
		'class' => 'block-outer-opposite',
	), $__vars) . '
	</div>
	<div class="block-container">
		<h2 class="block-tabHeader tabs hScroller" data-xf-init="h-scroller">
			<span class="hScroller-scroll">
			' . $__compilerTemp1 . '
			</span>
		</h2>
		' . $__compilerTemp2 . '
	</div>
', array(
		'action' => $__templater->func('link', array('template-modifications/toggle', ), false),
		'class' => 'block',
		'ajax' => 'true',
	));
	return $__finalCompiled;
});