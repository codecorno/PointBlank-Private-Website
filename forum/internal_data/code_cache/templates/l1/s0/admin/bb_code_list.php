<?php
// FROM HASH: 04fe73e05551a3e20be70f401be296fe
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Custom BB codes');
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	if (!$__templater->test($__vars['bbCodes'], 'empty', array())) {
		$__compilerTemp1 .= '
			' . $__templater->button('', array(
			'href' => $__templater->func('link', array('bb-codes', null, array('export' => ($__vars['exportView'] ? '' : '1'), ), ), false),
			'icon' => 'export',
		), '', array(
		)) . '
		';
	}
	$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	<div class="buttonGroup">
		' . $__templater->button('Add BB code', array(
		'href' => $__templater->func('link', array('bb-codes/add', ), false),
		'icon' => 'add',
	), '', array(
	)) . '
		' . $__templater->button('', array(
		'href' => $__templater->func('link', array('bb-codes/import', ), false),
		'icon' => 'import',
		'overlay' => 'true',
	), '', array(
	)) . '
		' . $__compilerTemp1 . '
	</div>
');
	$__finalCompiled .= '

';
	if (!$__templater->test($__vars['bbCodes'], 'empty', array())) {
		$__finalCompiled .= '
	';
		$__compilerTemp2 = '';
		if ($__templater->isTraversable($__vars['bbCodes'])) {
			foreach ($__vars['bbCodes'] AS $__vars['bbCode']) {
				$__compilerTemp2 .= '
						';
				$__compilerTemp3 = array();
				if ($__vars['exportView']) {
					$__compilerTemp3[] = array(
						'name' => 'export[]',
						'value' => $__vars['bbCode']['bb_code_id'],
						'disabled' => ($__vars['bbCode']['addon_id'] ? true : false),
						'tooltip' => ($__vars['bbCode']['addon_id'] ? 'BB codes that belong to an add-on can not be exported independently.' : ''),
						'_type' => 'toggle',
						'html' => '',
					);
				}
				$__compilerTemp3[] = array(
					'href' => ($__vars['exportView'] ? null : $__templater->func('link', array('bb-codes/edit', $__vars['bbCode'], ), false)),
					'label' => $__templater->escape($__vars['bbCode']['title']),
					'hint' => '[' . $__templater->escape($__vars['bbCode']['bb_code_id']) . ']',
					'explain' => $__templater->escape($__vars['bbCode']['description']),
					'_type' => 'main',
					'html' => '',
				);
				if (!$__vars['exportView']) {
					$__compilerTemp3[] = array(
						'name' => 'active[' . $__vars['bbCode']['bb_code_id'] . ']',
						'selected' => $__vars['bbCode']['active'],
						'class' => 'dataList-cell--separated',
						'submit' => 'true',
						'tooltip' => 'Enable / disable \'' . (('[' . $__vars['bbCode']['bb_code_id']) . ']') . '\'',
						'_type' => 'toggle',
						'html' => '',
					);
					$__compilerTemp3[] = array(
						'href' => $__templater->func('link', array('bb-codes/delete', $__vars['bbCode'], ), false),
						'_type' => 'delete',
						'html' => '',
					);
				}
				$__compilerTemp2 .= $__templater->dataRow(array(
				), $__compilerTemp3) . '
					';
			}
		}
		$__compilerTemp4 = '';
		if ($__vars['exportView']) {
			$__compilerTemp4 .= '
					<span class="block-footer-select">' . $__templater->formCheckBox(array(
				'standalone' => 'true',
			), array(array(
				'check-all' => '< .block-container',
				'label' => 'Select all',
				'_type' => 'option',
			))) . '</span>
					<span class="block-footer-controls">' . $__templater->button('', array(
				'type' => 'submit',
				'icon' => 'export',
			), '', array(
			)) . '</span>
				';
		}
		$__finalCompiled .= $__templater->form('

		<div class="block-outer">
			' . $__templater->callMacro('filter_macros', 'quick_filter', array(
			'key' => 'bb-codes',
			'class' => 'block-outer-opposite',
		), $__vars) . '
		</div>
		<div class="block-container">
			<div class="block-body">
				' . $__templater->dataList('
					' . $__compilerTemp2 . '
				', array(
		)) . '
			</div>
			<div class="block-footer block-footer--split">
				<span class="block-footer-counter">' . $__templater->func('display_totals', array($__vars['bbCodes'], ), true) . '</span>
				' . $__compilerTemp4 . '
			</div>
		</div>
	', array(
			'action' => $__templater->func('link', array(($__vars['exportView'] ? 'bb-codes/export' : 'bb-codes/toggle'), ), false),
			'ajax' => ($__vars['exportView'] ? false : true),
			'class' => 'block',
		)) . '
';
	} else {
		$__finalCompiled .= '
	<div class="blockMessage">' . 'No items have been created yet.' . '</div>
';
	}
	return $__finalCompiled;
});