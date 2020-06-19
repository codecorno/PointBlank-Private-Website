<?php
// FROM HASH: 95f71046d6135fda593cce39801edf64
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Customized style components' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['style']['title']));
	$__finalCompiled .= '

';
	if (!$__templater->test($__vars['templates'], 'empty', array()) OR !$__templater->test($__vars['properties'], 'empty', array())) {
		$__finalCompiled .= '
	';
		$__compilerTemp1 = '';
		if (!$__templater->test($__vars['templates'], 'empty', array())) {
			$__compilerTemp1 .= '
						' . $__templater->dataRow(array(
				'rowtype' => 'subsection',
				'rowclass' => 'dataList-row--noHover',
			), array(array(
				'_type' => 'cell',
				'html' => 'Templates',
			),
			array(
				'class' => 'dataList-cell--min',
				'_type' => 'cell',
				'html' => $__templater->formCheckBox(array(
				'standalone' => 'true',
			), array(array(
				'check-all' => '.dataList .js-checkAllTemplates >',
				'_type' => 'option',
			))),
			))) . '
						';
			if ($__templater->isTraversable($__vars['templates'])) {
				foreach ($__vars['templates'] AS $__vars['template']) {
					$__compilerTemp1 .= '
							' . $__templater->dataRow(array(
						'rowclass' => 'js-checkAllTemplates',
					), array(array(
						'href' => $__templater->func('link', array('templates/edit', $__vars['template'], ), false),
						'label' => $__templater->escape($__vars['template']['title']),
						'hint' => ($__vars['template']['version_string'] ? 'Customized in ' . $__templater->escape($__vars['template']['version_string']) . '.' : ''),
						'_type' => 'main',
						'html' => '',
					),
					array(
						'name' => 'template_ids[]',
						'value' => $__vars['template']['template_id'],
						'_type' => 'toggle',
						'html' => '',
					))) . '
						';
				}
			}
			$__compilerTemp1 .= '
					';
		}
		$__compilerTemp2 = '';
		if (!$__templater->test($__vars['properties'], 'empty', array())) {
			$__compilerTemp2 .= '
						' . $__templater->dataRow(array(
				'rowtype' => 'subsection',
				'rowclass' => 'dataList-row--noHover',
			), array(array(
				'_type' => 'cell',
				'html' => 'Style properties',
			),
			array(
				'class' => 'dataList-cell--min',
				'_type' => 'cell',
				'html' => $__templater->formCheckBox(array(
				'standalone' => 'true',
			), array(array(
				'check-all' => '.dataList .js-checkAllProperties >',
				'_type' => 'option',
			))),
			))) . '
						';
			if ($__templater->isTraversable($__vars['properties'])) {
				foreach ($__vars['properties'] AS $__vars['property']) {
					$__compilerTemp2 .= '
							' . $__templater->dataRow(array(
						'rowclass' => 'js-checkAllProperties',
					), array(array(
						'href' => $__templater->func('link', array('styles/style-properties/group', $__vars['style'], array('group' => $__vars['property']['group_name'], ), ), false),
						'label' => $__templater->escape($__vars['property']['title']),
						'hint' => $__templater->escape($__vars['property']['property_name']),
						'_type' => 'main',
						'html' => '',
					),
					array(
						'name' => 'property_ids[]',
						'value' => $__vars['property']['property_id'],
						'_type' => 'toggle',
						'html' => '',
					))) . '
						';
				}
			}
			$__compilerTemp2 .= '
					';
		}
		$__finalCompiled .= $__templater->form('
		<div class="block-outer">
			<div class="block-outer-main">
				' . $__templater->callMacro('style_macros', 'style_change_menu', array(
			'styleTree' => $__vars['styleTree'],
			'currentStyle' => $__vars['style'],
			'route' => 'styles/customized-components',
		), $__vars) . '
			</div>
			<div class="block-outer-opposite">
				' . $__templater->callMacro('filter_macros', 'quick_filter', array(
			'key' => 'customized-components',
			'class' => 'block-outer-opposite',
		), $__vars) . '
			</div>
		</div>
		<div class="block-container">
			<div class="block-body">
				' . $__templater->dataList('
					' . $__compilerTemp1 . '
					' . $__compilerTemp2 . '
				', array(
		)) . '
			</div>
			<div class="block-footer block-footer--split">
				<span class="block-footer-counter">' . $__templater->func('display_totals', array($__vars['itemCount'], ), true) . '</span>
				<span class="block-footer-select">' . $__templater->formCheckBox(array(
			'standalone' => 'true',
		), array(array(
			'check-all' => '.dataList >',
			'label' => 'Select all',
			'_type' => 'option',
		))) . '</span>
				<span class="block-footer-controls">
					' . $__templater->button('Revert', array(
			'type' => 'submit',
		), '', array(
		)) . '
				</span>
			</div>
		</div>
	', array(
			'action' => $__templater->func('link', array('styles/mass-revert', $__vars['style'], ), false),
			'ajax' => 'true',
			'class' => 'block',
		)) . '
';
	} else {
		$__finalCompiled .= '
	<div class="block">
		<div class="block-outer">
			' . $__templater->callMacro('style_macros', 'style_change_menu', array(
			'styleTree' => $__vars['styleTree'],
			'currentStyle' => $__vars['style'],
			'route' => 'styles/customized-components',
		), $__vars) . '
		</div>
		<div class="block-container">
			<div class="block-body block-row">
				' . 'This style contains no customized templates or style properties.' . '
			</div>
		</div>
	</div>
';
	}
	return $__finalCompiled;
});