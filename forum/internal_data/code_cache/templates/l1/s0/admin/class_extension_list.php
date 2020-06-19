<?php
// FROM HASH: 6c1848ca85a6819323ca6eba37878d52
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Class extensions');
	$__finalCompiled .= '

';
	$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('Add class extension', array(
		'href' => $__templater->func('link', array('class-extensions/add', ), false),
		'icon' => 'add',
	), '', array(
	)) . '
');
	$__finalCompiled .= '

';
	if (!$__templater->test($__vars['extensions'], 'empty', array())) {
		$__finalCompiled .= '
	';
		$__compilerTemp1 = '';
		if ($__templater->isTraversable($__vars['extensions'])) {
			foreach ($__vars['extensions'] AS $__vars['addOnId'] => $__vars['addOnExtensions']) {
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
										' . 'Custom class extensions' . '
									';
				}
				$__compilerTemp1 .= $__templater->dataRow(array(
					'rowtype' => 'subsection',
					'rowclass' => 'dataList-row--noHover',
				), array(array(
					'colspan' => '4',
					'_type' => 'cell',
					'html' => '
									' . $__compilerTemp2 . '
								',
				))) . '
							';
				if ($__templater->isTraversable($__vars['addOnExtensions'])) {
					foreach ($__vars['addOnExtensions'] AS $__vars['extension']) {
						$__compilerTemp1 .= '
								' . $__templater->dataRow(array(
						), array(array(
							'hash' => $__vars['extension']['extension_id'],
							'href' => $__templater->func('link', array('class-extensions/edit', $__vars['extension'], ), false),
							'dir' => 'auto',
							'_type' => 'cell',
							'html' => '
										' . $__templater->filter($__vars['extension']['from_class'], array(array('split_long', array(25, )),), true) . '
									',
						),
						array(
							'href' => $__templater->func('link', array('class-extensions/edit', $__vars['extension'], ), false),
							'dir' => 'auto',
							'_type' => 'cell',
							'html' => '
										' . $__templater->filter($__vars['extension']['to_class'], array(array('split_long', array(25, )),), true) . '
									',
						),
						array(
							'name' => 'active[' . $__vars['extension']['extension_id'] . ']',
							'selected' => $__vars['extension']['active'],
							'class' => 'dataList-cell--separated',
							'submit' => 'true',
							'tooltip' => 'Enable / disable \'' . $__vars['extension']['from_class'] . '\'',
							'_type' => 'toggle',
							'html' => '',
						),
						array(
							'href' => $__templater->func('link', array('class-extensions/delete', $__vars['extension'], ), false),
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
			'key' => 'extensions',
			'class' => 'block-outer-opposite',
		), $__vars) . '
		</div>
		<div class="block-container">
			<div class="block-body">
				' . $__templater->dataList('
					<thead>
						' . $__templater->dataRow(array(
			'rowtype' => 'header',
		), array(array(
			'_type' => 'cell',
			'html' => 'Base class name',
		),
		array(
			'_type' => 'cell',
			'html' => 'Extension class name',
		),
		array(
			'_type' => 'cell',
			'html' => '&nbsp;',
		),
		array(
			'_type' => 'cell',
			'html' => '&nbsp;',
		))) . '
					</thead>
					' . $__compilerTemp1 . '
				', array(
			'data-xf-init' => 'responsive-data-list',
			'data-trigger-width' => 'medium',
		)) . '
			</div>
			<div class="block-footer">
				<span class="block-footer-counter">' . $__templater->func('display_totals', array($__vars['totalExtensions'], $__vars['total'], ), true) . '</span>
			</div>
		</div>
	', array(
			'action' => $__templater->func('link', array('class-extensions/toggle', ), false),
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