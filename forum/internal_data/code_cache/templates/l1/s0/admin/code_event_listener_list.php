<?php
// FROM HASH: 893d252be3f6fece8408740da73e6ddf
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Code event listeners');
	$__finalCompiled .= '

';
	$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('Add code event listener', array(
		'href' => $__templater->func('link', array('code-events/listeners/add', ), false),
		'icon' => 'add',
	), '', array(
	)) . '
');
	$__finalCompiled .= '

';
	if (!$__templater->test($__vars['listeners'], 'empty', array())) {
		$__finalCompiled .= '
	';
		$__compilerTemp1 = '';
		if ($__templater->isTraversable($__vars['listeners'])) {
			foreach ($__vars['listeners'] AS $__vars['addOnId'] => $__vars['listenerEntity']) {
				$__compilerTemp1 .= '
						<tbody class="dataList-rowGroup">
							';
				$__compilerTemp2 = '';
				if ($__vars['addOns'][$__vars['addOnId']]) {
					$__compilerTemp2 .= '
										' . $__templater->escape($__vars['addOns'][$__vars['addOnId']]['title']) . '
									';
				} else {
					$__compilerTemp2 .= '
										' . 'Custom event listeners' . '
									';
				}
				$__compilerTemp1 .= $__templater->dataRow(array(
					'rowtype' => 'subsection',
					'rowclass' => 'dataList-row--noHover',
				), array(array(
					'colspan' => '3',
					'_type' => 'cell',
					'html' => '
									' . $__compilerTemp2 . '
								',
				))) . '
							';
				if ($__templater->isTraversable($__vars['listenerEntity'])) {
					foreach ($__vars['listenerEntity'] AS $__vars['listener']) {
						$__compilerTemp1 .= '
								' . $__templater->dataRow(array(
						), array(array(
							'hash' => $__vars['listener']['event_listener_id'],
							'href' => $__templater->func('link', array('code-events/listeners/edit', $__vars['listener'], ), false),
							'label' => $__templater->escape($__vars['listener']['event_id']),
							'explain' => $__templater->escape($__vars['listener']['description']),
							'_type' => 'main',
							'html' => '',
						),
						array(
							'name' => 'active[' . $__vars['listener']['event_listener_id'] . ']',
							'selected' => $__vars['listener']['active'],
							'class' => 'dataList-cell--separated',
							'submit' => 'true',
							'tooltip' => 'Enable / disable \'' . $__vars['listener']['event_id'] . '\'',
							'_type' => 'toggle',
							'html' => '',
						),
						array(
							'href' => $__templater->func('link', array('code-events/listeners/delete', $__vars['listener'], ), false),
							'_type' => 'delete',
							'html' => '',
						))) . '
							';
					}
				}
				$__compilerTemp1 .= '
						</tbody>
					';
			}
		}
		$__finalCompiled .= $__templater->form('
		<div class="block-outer">
			' . $__templater->callMacro('filter_macros', 'quick_filter', array(
			'key' => 'listeners',
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
				<span class="block-footer-counter">' . $__templater->func('display_totals', array($__vars['totalListeners'], ), true) . '</span>
			</div>
		</div>
	', array(
			'action' => $__templater->func('link', array('code-events/listeners/toggle', ), false),
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