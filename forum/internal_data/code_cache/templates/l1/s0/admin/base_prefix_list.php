<?php
// FROM HASH: 25d166dbae8ae021759616657c8378e7
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	<div class="buttonGroup">
		' . $__templater->button('Add prefix', array(
		'href' => $__templater->func('link', array($__vars['linkPrefix'] . '/add', ), false),
		'icon' => 'add',
	), '', array(
	)) . '
		' . $__templater->button('Add prefix group', array(
		'href' => $__templater->func('link', array($__vars['groupLinkPrefix'] . '/add', ), false),
		'icon' => 'add',
		'overlay' => 'true',
	), '', array(
	)) . '
	</div>
');
	$__finalCompiled .= '

';
	if (!$__templater->test($__vars['prefixesGrouped'], 'empty', array()) OR ($__templater->func('count', array($__vars['prefixGroups'], ), false) > 1)) {
		$__finalCompiled .= '
	';
		$__compilerTemp1 = '';
		if ($__templater->isTraversable($__vars['prefixGroups'])) {
			foreach ($__vars['prefixGroups'] AS $__vars['prefixGroupId'] => $__vars['prefixGroup']) {
				$__compilerTemp1 .= '
						<tbody class="dataList-rowGroup" id="js-prefixGroup' . $__templater->escape($__vars['prefixGroupId']) . '">

						';
				if (($__templater->func('count', array($__vars['prefixGroups'], ), false) > 1)) {
					$__compilerTemp1 .= '
							';
					$__compilerTemp2 = array();
					if (($__vars['prefixGroupId'] > 0)) {
						$__compilerTemp3 = '';
						if ($__templater->func('count', array($__vars['prefixesGrouped'][$__vars['prefixGroupId']], ), false)) {
							$__compilerTemp3 .= '
											' . $__templater->formCheckBox(array(
								'standalone' => 'true',
							), array(array(
								'check-all' => '#js-prefixGroup' . $__vars['prefixGroupId'],
								'_type' => 'option',
							))) . '
										';
						}
						$__compilerTemp2[] = array(
							'class' => 'dataList-cell--min',
							'_type' => 'cell',
							'html' => '
										' . $__compilerTemp3 . '
									',
						);
						$__compilerTemp2[] = array(
							'href' => $__templater->func('link', array($__vars['groupLinkPrefix'] . '/edit', $__vars['prefixGroup'], ), false),
							'overlay' => 'true',
							'_type' => 'cell',
							'html' => '
										' . $__templater->escape($__vars['prefixGroup']['title']) . '
									',
						);
						$__compilerTemp2[] = array(
							'href' => $__templater->func('link', array($__vars['groupLinkPrefix'] . '/delete', $__vars['prefixGroup'], ), false),
							'_type' => 'delete',
							'html' => '',
						);
					} else if ($__templater->func('count', array($__vars['prefixesGrouped'][$__vars['prefixGroupId']], ), false)) {
						$__compilerTemp2[] = array(
							'class' => 'dataList-cell--min',
							'_type' => 'cell',
							'html' => '
										' . $__templater->formCheckBox(array(
							'standalone' => 'true',
						), array(array(
							'check-all' => '#js-prefixGroup' . $__vars['prefixGroupId'],
							'_type' => 'option',
						))) . '
									',
						);
						$__compilerTemp2[] = array(
							'_type' => 'cell',
							'html' => $__vars['xf']['language']['parenthesis_open'] . 'Ungrouped' . $__vars['xf']['language']['parenthesis_close'],
						);
						$__compilerTemp2[] = array(
							'_type' => 'cell',
							'html' => '&nbsp;',
						);
					}
					$__compilerTemp1 .= $__templater->dataRow(array(
						'rowtype' => 'subsection',
						'rowclass' => ((!$__vars['prefixGroupId']) ? 'dataList-row--noHover' : ''),
					), $__compilerTemp2) . '
						';
				}
				$__compilerTemp1 .= '

						';
				$__compilerTemp4 = true;
				if ($__templater->isTraversable($__vars['prefixesGrouped'][$__vars['prefixGroupId']])) {
					foreach ($__vars['prefixesGrouped'][$__vars['prefixGroupId']] AS $__vars['prefixId'] => $__vars['prefix']) {
						$__compilerTemp4 = false;
						$__compilerTemp1 .= '
							' . $__templater->dataRow(array(
							'rowclass' => 'prefixGroup' . $__vars['prefixGroupId'],
						), array(array(
							'name' => 'prefix_ids[]',
							'value' => $__vars['prefix']['prefix_id'],
							'_type' => 'toggle',
							'html' => '',
						),
						array(
							'href' => $__templater->func('link', array($__vars['linkPrefix'] . '/edit', $__vars['prefix'], ), false),
							'class' => 'dataList-cell',
							'_type' => 'cell',
							'html' => '
									' . $__templater->escape($__vars['prefix']['title']) . '
								',
						),
						array(
							'href' => $__templater->func('link', array($__vars['linkPrefix'] . '/delete', $__vars['prefix'], ), false),
							'_type' => 'delete',
							'html' => '',
						))) . '
						';
					}
				}
				if ($__compilerTemp4) {
					$__compilerTemp1 .= '
							';
					if (($__templater->func('count', array($__vars['prefixesGrouped'][$__vars['prefixGroupId']], ), false) OR ($__vars['prefixGroupId'] > 0))) {
						$__compilerTemp1 .= '
								';
						$__compilerTemp5 = '';
						if (($__templater->func('count', array($__vars['prefixGroups'], ), false) > 1)) {
							$__compilerTemp5 .= '
											' . 'No prefixes have been added to this group yet.' . '
										';
						} else {
							$__compilerTemp5 .= '
											' . 'No prefixes have been added yet.' . '
										';
						}
						$__compilerTemp1 .= $__templater->dataRow(array(
							'rowclass' => 'dataList-row--noHover',
						), array(array(
							'colspan' => '3',
							'class' => 'dataList-cell--noSearch',
							'_type' => 'cell',
							'html' => '
										' . $__compilerTemp5 . '
									',
						))) . '
							';
					}
					$__compilerTemp1 .= '
						';
				}
				$__compilerTemp1 .= '
						</tbody>
					';
			}
		}
		$__finalCompiled .= $__templater->form('

		<div class="block-outer">
			' . $__templater->callMacro('filter_macros', 'quick_filter', array(
			'key' => $__vars['linkPrefix'],
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
			<div class="block-footer block-footer--split">
				<span class="block-footer-counter">' . $__templater->func('display_totals', array($__vars['prefixTotal'], ), true) . '</span>
				<span class="block-footer-select">' . $__templater->formCheckBox(array(
			'standalone' => 'true',
		), array(array(
			'check-all' => '.dataList',
			'label' => 'Select all',
			'_type' => 'option',
		))) . '</span>
				<span class="block-footer-controls">' . $__templater->button('Quick set', array(
			'type' => 'submit',
			'name' => 'quickset',
			'value' => '1',
		), '', array(
		)) . '</span>
			</div>
		</div>
	', array(
			'action' => $__templater->func('link', array($__vars['linkPrefix'] . '/quick-set', ), false),
			'ajax' => 'true',
			'class' => 'block',
			'data-xf-init' => 'select-plus',
			'data-sp-checkbox' => '.dataList-cell--toggle input:checkbox',
			'data-sp-container' => '.dataList-row',
			'data-sp-control' => '.dataList-cell a',
		)) . '
';
	} else {
		$__finalCompiled .= '
	<div class="blockMessage">' . 'No items have been created yet.' . '</div>
';
	}
	return $__finalCompiled;
});