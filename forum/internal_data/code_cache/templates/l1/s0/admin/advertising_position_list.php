<?php
// FROM HASH: cce69a5ecf079c0ba8c8599deac24f1b
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Advertising positions');
	$__finalCompiled .= '

';
	$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('Add advertising position', array(
		'href' => $__templater->func('link', array('advertising/positions/add', ), false),
		'icon' => 'add',
	), '', array(
	)) . '
');
	$__finalCompiled .= '

';
	if (!$__templater->test($__vars['advertisingPositions'], 'empty', array())) {
		$__finalCompiled .= '
	';
		$__compilerTemp1 = '';
		if ($__templater->isTraversable($__vars['advertisingPositions'])) {
			foreach ($__vars['advertisingPositions'] AS $__vars['advertisingPosition']) {
				$__compilerTemp1 .= '
						' . $__templater->dataRow(array(
					'label' => $__templater->escape($__vars['advertisingPosition']['title']),
					'explain' => $__templater->filter($__vars['advertisingPosition']['description'], array(array('raw', array()),), true),
					'href' => $__templater->func('link', array('advertising/positions/edit', $__vars['advertisingPosition'], ), false),
					'delete' => $__templater->func('link', array('advertising/positions/delete', $__vars['advertisingPosition'], ), false),
				), array(array(
					'name' => 'active[' . $__vars['advertisingPosition']['position_id'] . ']',
					'selected' => $__vars['advertisingPosition']['active'],
					'class' => 'dataList-cell--separated',
					'submit' => 'true',
					'tooltip' => 'Enable / disable \'' . $__vars['advertisingPosition']['position_id'] . '\'',
					'_type' => 'toggle',
					'html' => '',
				))) . '
					';
			}
		}
		$__finalCompiled .= $__templater->form('
		<div class="block-outer">
			' . $__templater->callMacro('filter_macros', 'quick_filter', array(
			'key' => 'advertising-positions',
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
				<span class="block-footer-counter">' . $__templater->func('display_totals', array($__vars['advertisingPositions'], ), true) . '</span>
			</div>
		</div>
	', array(
			'action' => $__templater->func('link', array('advertising/positions/toggle', ), false),
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