<?php
// FROM HASH: e86989a77b5f2b51275fc65e129322df
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	<div class="buttonGroup">
		' . $__templater->button('Add prompt', array(
		'href' => $__templater->func('link', array($__vars['linkPrefix'] . '/add', ), false),
		'icon' => 'add',
	), '', array(
	)) . '
		' . $__templater->button('Add prompt group', array(
		'href' => $__templater->func('link', array($__vars['groupLinkPrefix'] . '/add', ), false),
		'icon' => 'add',
		'overlay' => 'true',
	), '', array(
	)) . '
	</div>
');
	$__finalCompiled .= '

';
	if (!$__templater->test($__vars['promptsGrouped'], 'empty', array()) OR ($__templater->func('count', array($__vars['promptGroups'], ), false) > 1)) {
		$__finalCompiled .= '
	';
		$__compilerTemp1 = '';
		if ($__templater->isTraversable($__vars['promptGroups'])) {
			foreach ($__vars['promptGroups'] AS $__vars['promptGroupId'] => $__vars['promptGroup']) {
				$__compilerTemp1 .= '
						<tbody class="dataList-rowGroup" id="js-promptGroup' . $__templater->escape($__vars['promptGroupId']) . '">

						';
				if (($__templater->func('count', array($__vars['promptGroups'], ), false) > 1)) {
					$__compilerTemp1 .= '
							';
					$__compilerTemp2 = array();
					if (($__vars['promptGroupId'] > 0)) {
						$__compilerTemp3 = '';
						if ($__templater->func('count', array($__vars['promptsGrouped'][$__vars['promptGroupId']], ), false)) {
							$__compilerTemp3 .= '
											' . $__templater->formCheckBox(array(
								'standalone' => 'true',
							), array(array(
								'check-all' => '#js-promptGroup' . $__vars['promptGroupId'],
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
							'href' => $__templater->func('link', array($__vars['groupLinkPrefix'] . '/edit', $__vars['promptGroup'], ), false),
							'overlay' => 'true',
							'_type' => 'cell',
							'html' => '
										' . $__templater->escape($__vars['promptGroup']['title']) . '
									',
						);
						$__compilerTemp2[] = array(
							'href' => $__templater->func('link', array($__vars['groupLinkPrefix'] . '/delete', $__vars['promptGroup'], ), false),
							'_type' => 'delete',
							'html' => '',
						);
					} else if ($__templater->func('count', array($__vars['promptsGrouped'][$__vars['promptGroupId']], ), false)) {
						$__compilerTemp2[] = array(
							'class' => 'dataList-cell--min',
							'_type' => 'cell',
							'html' => '
										' . $__templater->formCheckBox(array(
							'standalone' => 'true',
						), array(array(
							'check-all' => '#js-promptGroup' . $__vars['promptGroupId'],
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
						'rowclass' => ((!$__vars['promptGroupId']) ? 'dataList-row--noHover' : ''),
					), $__compilerTemp2) . '
						';
				}
				$__compilerTemp1 .= '

						';
				$__compilerTemp4 = true;
				if ($__templater->isTraversable($__vars['promptsGrouped'][$__vars['promptGroupId']])) {
					foreach ($__vars['promptsGrouped'][$__vars['promptGroupId']] AS $__vars['promptId'] => $__vars['prompt']) {
						$__compilerTemp4 = false;
						$__compilerTemp1 .= '
							' . $__templater->dataRow(array(
							'rowclass' => 'promptGroup' . $__vars['promptGroupId'],
						), array(array(
							'name' => 'prompt_ids[]',
							'value' => $__vars['prompt']['prompt_id'],
							'_type' => 'toggle',
							'html' => '',
						),
						array(
							'href' => $__templater->func('link', array($__vars['linkPrefix'] . '/edit', $__vars['prompt'], ), false),
							'class' => 'dataList-cell',
							'_type' => 'cell',
							'html' => '
									' . $__templater->escape($__vars['prompt']['title']) . '
								',
						),
						array(
							'href' => $__templater->func('link', array($__vars['linkPrefix'] . '/delete', $__vars['prompt'], ), false),
							'_type' => 'delete',
							'html' => '',
						))) . '
							';
					}
				}
				if ($__compilerTemp4) {
					$__compilerTemp1 .= '
							';
					if (($__templater->func('count', array($__vars['promptsGrouped'][$__vars['promptGroupId']], ), false) OR ($__vars['promptGroupId'] > 0))) {
						$__compilerTemp1 .= '
								';
						$__compilerTemp5 = '';
						if (($__templater->func('count', array($__vars['promptGroups'], ), false) > 1)) {
							$__compilerTemp5 .= '
											' . 'No prompts have been added to this group yet' . '
											';
						} else {
							$__compilerTemp5 .= '
											' . 'no_prompts_have_been_added_yet' . '
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
				<span class="block-footer-counter">' . $__templater->func('display_totals', array($__vars['promptTotal'], ), true) . '</span>
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
			'data-sp-checkbox' => '.dataList-row:not(.dataList-row--subSection) input:checkbox',
			'data-sp-container' => '.dataList-row:not(.dataList-row--subSection, .dataList-row--noHover)',
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