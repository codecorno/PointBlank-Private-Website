<?php
// FROM HASH: 1d243f7140bd00efe73ad9e045d744c4
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('Add field', array(
		'href' => $__templater->func('link', array($__vars['prefix'] . '/add', ), false),
		'icon' => 'add',
	), '', array(
	)) . '
');
	$__finalCompiled .= '

';
	if (!$__templater->test($__vars['fieldsGrouped'], 'empty', array())) {
		$__finalCompiled .= '
	';
		$__compilerTemp1 = '';
		if ($__templater->isTraversable($__vars['displayGroups'])) {
			foreach ($__vars['displayGroups'] AS $__vars['displayGroupId'] => $__vars['displayGroup']) {
				$__compilerTemp1 .= '
						';
				if (!$__templater->test($__vars['fieldsGrouped'][$__vars['displayGroupId']], 'empty', array())) {
					$__compilerTemp1 .= '
							<tbody class="dataList-rowGroup">
								' . $__templater->dataRow(array(
						'rowtype' => 'subsection',
						'rowclass' => 'dataList-row--noHover',
					), array(array(
						'colspan' => '2',
						'_type' => 'cell',
						'html' => $__templater->escape($__vars['displayGroup']),
					))) . '
								';
					if ($__templater->isTraversable($__vars['fieldsGrouped'][$__vars['displayGroupId']])) {
						foreach ($__vars['fieldsGrouped'][$__vars['displayGroupId']] AS $__vars['field']) {
							$__compilerTemp1 .= '
									' . $__templater->dataRow(array(
								'label' => $__templater->filter($__vars['field']['title'], array(array('htmlspecialchars', array()),), true),
								'href' => $__templater->func('link', array($__vars['prefix'] . '/edit', $__vars['field'], ), false),
								'delete' => $__templater->func('link', array($__vars['prefix'] . '/delete', $__vars['field'], ), false),
								'hash' => $__vars['field']['field_id'],
								'hint' => '
											' . $__templater->escape($__vars['fieldTypes'][$__vars['field']['field_type']]['label']) . '
										',
							), array()) . '
								';
						}
					}
					$__compilerTemp1 .= '
							</tbody>
						';
				}
				$__compilerTemp1 .= '
					';
			}
		}
		$__finalCompiled .= $__templater->form('
		<div class="block-outer">
			' . $__templater->callMacro('filter_macros', 'quick_filter', array(
			'key' => 'user-fields',
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
				<span class="block-footer-counter">' . $__templater->func('display_totals', array($__vars['displayGroups'], ), true) . '</span>
			</div>
		</div>
	', array(
			'action' => $__templater->func('link', array($__vars['prefix'] . '/toggle', ), false),
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