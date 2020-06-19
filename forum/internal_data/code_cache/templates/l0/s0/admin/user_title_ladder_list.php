<?php
// FROM HASH: 8321eb7759c084b824eb7a037b8ab764
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('User title ladder');
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	if ($__templater->isTraversable($__vars['ladder'])) {
		foreach ($__vars['ladder'] AS $__vars['title']) {
			$__compilerTemp1 .= '
					';
			$__vars['maxLength'] = $__templater->func('max_length', array($__vars['title'], 'title', ), false);
			$__compilerTemp1 .= '
					' . $__templater->dataRow(array(
			), array(array(
				'name' => 'delete[]',
				'value' => $__vars['title']['minimum_level'],
				'_type' => 'toggle',
				'html' => '',
			),
			array(
				'_type' => 'cell',
				'html' => '
							' . $__templater->formTextBox(array(
				'name' => 'update[' . $__vars['title']['minimum_level'] . '][title]',
				'value' => $__vars['title']['title'],
				'maxlength' => $__templater->func('max_length', array($__vars['title'], 'title', ), false),
			)) . '
						',
			),
			array(
				'_type' => 'cell',
				'html' => '
							' . $__templater->formNumberBox(array(
				'name' => 'update[' . $__vars['title']['minimum_level'] . '][minimum_level]',
				'min' => '0',
				'value' => $__vars['title']['minimum_level'],
			)) . '
						',
			))) . '
				';
		}
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->dataList('
				<colgroup>
					<col style="width: 1%" />
					<col />
					<col style="width: 130px" />
				</colgroup>
				' . $__templater->dataRow(array(
		'rowtype' => 'header',
	), array(array(
		'_type' => 'cell',
		'html' => 'Delete',
	),
	array(
		'_type' => 'cell',
		'html' => 'Title',
	),
	array(
		'_type' => 'cell',
		'html' => 'Minimum value',
	))) . '
				' . $__compilerTemp1 . '
				' . $__templater->dataRow(array(
	), array(array(
		'data-hide-label' => 'true',
		'_type' => 'cell',
		'html' => 'New' . $__vars['xf']['language']['label_separator'],
	),
	array(
		'_type' => 'cell',
		'html' => '
						' . $__templater->formTextBox(array(
		'name' => 'title',
		'maxlength' => $__vars['maxLength'],
	)) . '
					',
	),
	array(
		'_type' => 'cell',
		'html' => '
						' . $__templater->formNumberBox(array(
		'name' => 'minimum_level',
		'min' => '0',
		'value' => '0',
	)) . '
					',
	))) . '
			', array(
		'data-xf-init' => 'responsive-data-list',
	)) . '
		</div>
		<div class="block-footer block-footer--split">
			<span class="block-footer-counter">' . $__templater->func('display_totals', array($__vars['ladder'], ), true) . '</span>
			<span class="block-footer-select">' . $__templater->formCheckBox(array(
		'standalone' => 'true',
	), array(array(
		'check-all' => '< .block-container',
		'label' => 'Select all',
		'_type' => 'option',
	))) . '</span>
			<span class="block-footer-controls">' . $__templater->button('Update user titles', array(
		'type' => 'submit',
	), '', array(
	)) . '</span>
		</div>
	</div>
', array(
		'action' => $__templater->func('link', array('user-title-ladder/update', ), false),
		'class' => 'block',
	)) . '

' . $__templater->callMacro('option_macros', 'option_form_block', array(
		'options' => $__vars['options'],
	), $__vars);
	return $__finalCompiled;
});