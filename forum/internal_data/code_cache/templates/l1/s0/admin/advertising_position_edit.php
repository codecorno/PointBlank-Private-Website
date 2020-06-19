<?php
// FROM HASH: bb09b2f4ff5d2c2fb11ebec786b1298b
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->method($__vars['advertisingPosition'], 'isInsert', array())) {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Add advertising position');
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Edit advertising position' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['advertisingPosition']['title']));
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	if ($__templater->method($__vars['advertisingPosition'], 'isUpdate', array())) {
		$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('', array(
			'href' => $__templater->func('link', array('advertising/positions/delete', $__vars['advertisingPosition'], ), false),
			'icon' => 'delete',
			'overlay' => 'true',
		), '', array(
		)) . '
');
	}
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	if ($__templater->isTraversable($__vars['advertisingPosition']['arguments'])) {
		foreach ($__vars['advertisingPosition']['arguments'] AS $__vars['counter'] => $__vars['argument']) {
			$__compilerTemp1 .= '
						<li class="inputGroup">
							' . $__templater->formTextBox(array(
				'name' => 'arguments[' . $__vars['counter'] . '][argument]',
				'value' => $__vars['argument']['argument'],
				'placeholder' => 'Name of argument',
				'size' => '20',
			)) . '
							<span class="inputGroup-splitter"></span>

							' . $__templater->formCheckBox(array(
				'value' => $__vars['argument']['required'],
				'standalone' => 'true',
			), array(array(
				'name' => 'arguments[' . $__vars['counter'] . '][required]',
				'value' => '1',
				'labelclass' => 'inputGroup-text',
				'label' => 'Required',
				'_type' => 'option',
			))) . '
						</li>
					';
		}
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<div class="block-body">

			' . $__templater->formTextBoxRow(array(
		'name' => 'position_id',
		'value' => $__vars['advertisingPosition']['position_id'],
		'maxlength' => $__templater->func('max_length', array($__vars['advertisingPosition'], 'position_id', ), false),
		'dir' => 'ltr',
	), array(
		'label' => 'Position ID',
	)) . '

			' . $__templater->formTextBoxRow(array(
		'name' => 'title',
		'value' => $__vars['advertisingPosition']['MasterTitle']['phrase_text'],
	), array(
		'label' => 'Title',
	)) . '

			' . $__templater->formCodeEditorRow(array(
		'name' => 'description',
		'value' => $__vars['advertisingPosition']['MasterDescription']['phrase_text'],
		'mode' => 'html',
		'data-line-wrapping' => 'true',
		'class' => 'codeEditor--autoSize codeEditor--proportional',
	), array(
		'label' => 'Description',
		'hint' => 'You may use HTML',
	)) . '

			' . $__templater->formRow('

				<ul class="listPlain inputGroup-container">
					' . $__compilerTemp1 . '

					<li class="inputGroup" data-xf-init="field-adder" data-increment-format="arguments[{counter}]">
						' . $__templater->formTextBox(array(
		'name' => 'arguments[' . $__vars['nextCounter'] . '][argument]',
		'placeholder' => 'Name of argument',
		'size' => '20',
	)) . '
						<span class="inputGroup-splitter"></span>

						' . $__templater->formCheckBox(array(
		'standalone' => 'true',
	), array(array(
		'name' => 'arguments[' . $__vars['nextCounter'] . '][required]',
		'value' => '1',
		'labelclass' => 'inputGroup-text',
		'label' => 'Required',
		'_type' => 'option',
	))) . '
					</li>
				</ul>
			', array(
		'rowtype' => 'input',
		'label' => 'Arguments',
		'explain' => 'List the arguments that this advertising position requires.',
	)) . '

			' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'active',
		'value' => '1',
		'selected' => $__vars['advertisingPosition']['active'],
		'label' => 'Advertising position is active',
		'_type' => 'option',
	)), array(
	)) . '

			' . $__templater->callMacro('addon_macros', 'addon_edit', array(
		'addOnId' => $__vars['advertisingPosition']['addon_id'],
	), $__vars) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'sticky' => 'true',
		'icon' => 'save',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('advertising/positions/save', $__vars['advertisingPosition'], ), false),
		'class' => 'block',
		'ajax' => 'true',
	));
	return $__finalCompiled;
});