<?php
// FROM HASH: d0e6d8ceee553737ed1f09a6053b0966
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Widget positions');
	$__finalCompiled .= '

';
	$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('Add widget position', array(
		'href' => $__templater->func('link', array('widgets/positions/add', ), false),
		'icon' => 'add',
	), '', array(
	)) . '
');
	$__finalCompiled .= '

';
	if (!$__templater->test($__vars['widgetPositions'], 'empty', array())) {
		$__finalCompiled .= '
	';
		$__compilerTemp1 = '';
		if ($__templater->isTraversable($__vars['widgetPositions'])) {
			foreach ($__vars['widgetPositions'] AS $__vars['widgetPosition']) {
				$__compilerTemp1 .= '
						' . $__templater->dataRow(array(
					'label' => $__templater->escape($__vars['widgetPosition']['title']),
					'explain' => $__templater->filter($__vars['widgetPosition']['description'], array(array('raw', array()),), true),
					'href' => $__templater->func('link', array('widgets/positions/edit', $__vars['widgetPosition'], ), false),
					'delete' => $__templater->func('link', array('widgets/positions/delete', $__vars['widgetPosition'], ), false),
				), array(array(
					'name' => 'active[' . $__vars['widgetPosition']['position_id'] . ']',
					'selected' => $__vars['widgetPosition']['active'],
					'class' => 'dataList-cell--separated',
					'submit' => 'true',
					'tooltip' => 'Enable / disable \'' . $__vars['widgetPosition']['position_id'] . '\'',
					'_type' => 'toggle',
					'html' => '',
				))) . '
					';
			}
		}
		$__finalCompiled .= $__templater->form('
		<div class="block-outer">
			' . $__templater->callMacro('filter_macros', 'quick_filter', array(
			'key' => 'widget-positions',
			'class' => 'block-outer-opposite',
		), $__vars) . '
		</div>
		<div class="block-container">
			<div class="block-body">
				' . $__templater->dataList('
					' . $__compilerTemp1 . '
				', array(
		)) . '
			</div>
			<div class="block-footer">
				<span class="block-footer-counter">' . $__templater->func('display_totals', array($__vars['widgetPositions'], ), true) . '</span>
			</div>
		</div>
	', array(
			'action' => $__templater->func('link', array('widgets/positions/toggle', ), false),
			'class' => 'block',
			'ajax' => 'true',
		)) . '
';
	} else {
		$__finalCompiled .= '
	<div class="blockMessage">' . 'No items have been created yet.' . '</div>
';
	}
	return $__finalCompiled;
});