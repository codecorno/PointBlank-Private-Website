<?php
// FROM HASH: 41e604ea33dc9199c62f91c7d77536b8
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->method($__vars['smilie'], 'isInsert', array())) {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Add smilie');
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Edit smilie' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['smilie']['title']));
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	if ($__templater->method($__vars['smilie'], 'isUpdate', array())) {
		$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('', array(
			'href' => $__templater->func('link', array('smilies/delete', $__vars['smilie'], ), false),
			'icon' => 'delete',
			'overlay' => 'true',
		), '', array(
		)) . '
');
	}
	$__finalCompiled .= '

';
	$__compilerTemp1 = array(array(
		'value' => '0',
		'label' => $__vars['xf']['language']['parenthesis_open'] . 'None' . $__vars['xf']['language']['parenthesis_close'],
		'_type' => 'option',
	));
	$__compilerTemp1 = $__templater->mergeChoiceOptions($__compilerTemp1, $__vars['smilieCategories']);
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<div class="block-body">

			' . $__templater->formTextBoxRow(array(
		'name' => 'title',
		'value' => $__vars['smilie']['title'],
		'maxlength' => $__templater->func('max_length', array($__vars['smilie'], 'title', ), false),
	), array(
		'label' => 'Title',
	)) . '
			' . $__templater->formTextAreaRow(array(
		'name' => 'smilie_text',
		'value' => $__vars['smilie']['smilie_text'],
		'autosize' => 'true',
	), array(
		'label' => 'Text to replace',
		'explain' => 'You may enter multiple text values to replace by putting them on separate lines.',
	)) . '

			' . $__templater->formTextBoxRow(array(
		'name' => 'image_url',
		'value' => $__vars['smilie']['image_url'],
		'maxlength' => $__templater->func('max_length', array($__vars['smilie'], 'image_url', ), false),
		'dir' => 'ltr',
	), array(
		'label' => 'Image replacement URL',
	)) . '

			' . $__templater->formTextBoxRow(array(
		'name' => 'image_url_2x',
		'value' => $__vars['smilie']['image_url_2x'],
		'maxlength' => $__templater->func('max_length', array($__vars['smilie'], 'image_url_2x', ), false),
		'dir' => 'ltr',
	), array(
		'label' => '2x image replacement URL',
		'hint' => 'Optional',
		'explain' => 'If provided, the 2x image will be automatically displayed instead of the image URL above on devices capable of displaying a higher pixel resolution.<br />
<br />
<strong>Note: This option has no effect with sprite mode enabled.</strong>',
	)) . '

			<hr class="formRowSep" />

			' . $__templater->formSelectRow(array(
		'name' => 'smilie_category_id',
		'value' => $__vars['smilie']['smilie_category_id'],
	), $__compilerTemp1, array(
		'label' => 'Smilie category',
	)) . '

			' . $__templater->callMacro('display_order_macros', 'row', array(
		'value' => $__vars['smilie']['display_order'],
	), $__vars) . '

			' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'display_in_editor',
		'selected' => $__vars['smilie']['display_in_editor'],
		'label' => 'Show this smilie in the text editor',
		'explain' => 'Hidden smilies are not shown as clickable items in the text editor, but are displayed on the smilie help page, and will still convert smilie text to a smilie image if typed manually into the editor.',
		'_type' => 'option',
	)), array(
	)) . '

			<hr class="formRowSep" />

			' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'sprite_mode',
		'selected' => $__vars['smilie']['sprite_mode'],
		'label' => 'Enable CSS sprite mode with the following parameters:',
		'_type' => 'option',
	)), array(
		'label' => 'Sprite mode',
	)) . '

			' . $__templater->formRow('

				<div class="inputGroup">
					' . $__templater->formNumberBox(array(
		'name' => 'sprite_params[w]',
		'value' => $__vars['smilie']['sprite_params']['w'],
		'min' => '1',
		'title' => 'Width',
		'data-xf-init' => 'tooltip',
	)) . '
					<span class="inputGroup-text">x</span>
					' . $__templater->formNumberBox(array(
		'name' => 'sprite_params[h]',
		'value' => $__vars['smilie']['sprite_params']['h'],
		'min' => '1',
		'title' => 'Height',
		'data-xf-init' => 'tooltip',
	)) . '
					<span class="inputGroup-text">' . 'Pixels' . '</span>
				</div>
			', array(
		'rowtype' => 'input',
		'label' => 'Sprite dimensions',
	)) . '

			' . $__templater->formRow('

				<div class="inputGroup">
					' . $__templater->formNumberBox(array(
		'name' => 'sprite_params[x]',
		'value' => $__vars['smilie']['sprite_params']['x'],
		'title' => 'Background position x',
		'data-xf-init' => 'tooltip',
	)) . '
					<span class="inputGroup-text">x</span>
					' . $__templater->formNumberBox(array(
		'name' => 'sprite_params[y]',
		'value' => $__vars['smilie']['sprite_params']['y'],
		'title' => 'Background position y',
		'data-xf-init' => 'tooltip',
	)) . '
					<span class="inputGroup-text">' . 'Pixels' . '</span>
				</div>
			', array(
		'rowtype' => 'input',
		'label' => 'Sprite position',
	)) . '

			' . $__templater->formTextBoxRow(array(
		'name' => 'sprite_params[bs]',
		'value' => $__vars['smilie']['sprite_params']['bs'],
		'dir' => 'ltr',
	), array(
		'label' => 'Background size',
		'explain' => 'If required, enter a value for the <code>background-size</code> CSS property for this sprite.',
	)) . '
		</div>

		' . $__templater->formSubmitRow(array(
		'sticky' => 'true',
		'icon' => 'save',
	), array(
	)) . '

	</div>
', array(
		'action' => $__templater->func('link', array('smilies/save', $__vars['smilie'], ), false),
		'ajax' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});