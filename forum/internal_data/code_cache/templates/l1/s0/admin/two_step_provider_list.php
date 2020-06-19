<?php
// FROM HASH: c922d3411cff6594f21c68fe43b49ac8
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Two-step verification providers');
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	if (!$__templater->test($__vars['activeProviders'], 'empty', array())) {
		$__compilerTemp1 .= '
			<div class="block-body">
				';
		$__compilerTemp2 = '';
		if ($__templater->isTraversable($__vars['activeProviders'])) {
			foreach ($__vars['activeProviders'] AS $__vars['provider']) {
				$__compilerTemp2 .= '
						' . $__templater->dataRow(array(
					'label' => $__templater->escape($__vars['provider']['title']),
					'href' => $__templater->func('link', array('two-step/edit', $__vars['provider'], ), false),
				), array()) . '
					';
			}
		}
		$__compilerTemp1 .= $__templater->dataList('
					' . $__templater->dataRow(array(
			'rowtype' => 'subsection',
			'rowclass' => 'dataList-row--noHover',
		), array(array(
			'_type' => 'cell',
			'html' => 'Active providers',
		))) . '
					' . $__compilerTemp2 . '
				', array(
		)) . '
			</div>
		';
	}
	$__compilerTemp3 = '';
	if (!$__templater->test($__vars['inactiveProviders'], 'empty', array())) {
		$__compilerTemp3 .= '
			<div class="block-body">
				';
		$__compilerTemp4 = '';
		if ($__templater->isTraversable($__vars['inactiveProviders'])) {
			foreach ($__vars['inactiveProviders'] AS $__vars['provider']) {
				$__compilerTemp4 .= '
						' . $__templater->dataRow(array(
					'label' => $__templater->escape($__vars['provider']['title']),
					'href' => $__templater->func('link', array('two-step/edit', $__vars['provider'], ), false),
				), array()) . '
					';
			}
		}
		$__compilerTemp3 .= $__templater->dataList('
					' . $__templater->dataRow(array(
			'rowtype' => 'subsection',
			'rowclass' => 'dataList-row--noHover',
		), array(array(
			'_type' => 'cell',
			'html' => 'Inactive providers',
		))) . '
					' . $__compilerTemp4 . '
				', array(
		)) . '
			</div>
		';
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-outer">
		' . $__templater->callMacro('filter_macros', 'quick_filter', array(
		'key' => 'two-step',
		'class' => 'block-outer-opposite',
	), $__vars) . '
	</div>
	<div class="block-container">
		' . $__compilerTemp1 . '

		' . $__compilerTemp3 . '
		<div class="block-footer">
			<span class="block-footer-counter">' . $__templater->func('display_totals', array($__templater->func('count', array($__vars['activeProviders'], ), false) + $__templater->func('count', array($__vars['inactiveProviders'], ), false), ), true) . '</span>
		</div>
	</div>
', array(
		'action' => $__templater->func('link', array('two-step/toggle', ), false),
		'class' => 'block',
		'ajax' => 'true',
	));
	return $__finalCompiled;
});