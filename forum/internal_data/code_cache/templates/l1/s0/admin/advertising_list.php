<?php
// FROM HASH: 4803d11afcfa98ee7cb83d62aeeaf56b
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Advertising');
	$__finalCompiled .= '

';
	$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('Add advertisement', array(
		'href' => $__templater->func('link', array('advertising/add', ), false),
		'icon' => 'add',
	), '', array(
	)) . '
');
	$__finalCompiled .= '

';
	if (!$__templater->test($__vars['ads'], 'empty', array())) {
		$__finalCompiled .= '
	';
		$__compilerTemp1 = '';
		if ($__templater->isTraversable($__vars['positions'])) {
			foreach ($__vars['positions'] AS $__vars['positionId'] => $__vars['position']) {
				$__compilerTemp1 .= '
						';
				if (!$__templater->test($__vars['ads'][$__vars['positionId']], 'empty', array())) {
					$__compilerTemp1 .= '
							<tbody class="dataList-rowGroup">
								' . $__templater->dataRow(array(
						'colspan' => '3',
						'label' => $__templater->escape($__vars['position']['title']),
						'explain' => $__templater->filter($__vars['position']['description'], array(array('raw', array()),), true),
						'rowtype' => 'subsection',
						'rowclass' => 'dataList-row--noHover',
					), array()) . '
								';
					if ($__templater->isTraversable($__vars['ads'][$__vars['positionId']])) {
						foreach ($__vars['ads'][$__vars['positionId']] AS $__vars['ad']) {
							$__compilerTemp1 .= '
									' . $__templater->dataRow(array(
								'label' => $__templater->escape($__vars['ad']['title']),
								'href' => $__templater->func('link', array('advertising/edit', $__vars['ad'], ), false),
								'delete' => $__templater->func('link', array('advertising/delete', $__vars['ad'], ), false),
							), array(array(
								'name' => 'active[' . $__vars['ad']['ad_id'] . ']',
								'selected' => $__vars['ad']['active'],
								'class' => 'dataList-cell--separated',
								'submit' => 'true',
								'tooltip' => 'Enable / disable \'' . $__vars['ad']['title'] . '\'',
								'_type' => 'toggle',
								'html' => '',
							))) . '
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
			'key' => 'advertising',
			'class' => 'block-outer-opposite',
		), $__vars) . '
		</div>
		<div class="block-container">
			<div class="block-body">
				' . $__templater->dataList('
					' . $__compilerTemp1 . '
				', array(
		)) . '
				<div class="block-footer">
					<span class="block-footer-counter">' . $__templater->func('display_totals', array($__vars['totalAds'], ), true) . '</span>
				</div>
			</div>
		</div>
	', array(
			'action' => $__templater->func('link', array('advertising/toggle', ), false),
			'class' => 'block',
			'ajax' => 'true',
		)) . '
';
	} else {
		$__finalCompiled .= '
	<div class="blockMessage">' . 'No items have been created yet.' . '</div>
';
	}
	$__finalCompiled .= '

' . $__templater->callMacro('option_macros', 'option_form_block', array(
		'options' => $__vars['options'],
	), $__vars);
	return $__finalCompiled;
});