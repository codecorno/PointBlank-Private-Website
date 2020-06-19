<?php
// FROM HASH: 602eeed15360a4d54c4e4db89041eece
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->method($__vars['bbCode'], 'isInsert', array())) {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Add BB code');
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Edit BB code' . $__vars['xf']['language']['label_separator'] . ' [' . $__templater->escape($__vars['bbCode']['bb_code_id']) . ']');
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	if ($__templater->method($__vars['bbCode'], 'isUpdate', array()) AND $__templater->method($__vars['bbCode'], 'canEdit', array())) {
		$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('', array(
			'href' => $__templater->func('link', array('bb-codes/delete', $__vars['bbCode'], ), false),
			'icon' => 'delete',
			'overlay' => 'true',
		), '', array(
		)) . '
');
	}
	$__finalCompiled .= '

';
	if ((!$__templater->method($__vars['bbCode'], 'canEdit', array()))) {
		$__finalCompiled .= '
	<div class="blockMessage blockMessage--important blockMessage--iconic">
		' . 'Only a limited number of fields in this item may be edited.' . '
	</div>
';
	}
	$__finalCompiled .= '

' . $__templater->form('
	<div class="block-container">

		<div class="block-body">
			' . $__templater->formTextBoxRow(array(
		'name' => 'bb_code_id',
		'value' => $__vars['bbCode']['bb_code_id'],
		'maxlength' => $__templater->func('max_length', array($__vars['bbCode'], 'bb_code_id', ), false),
		'readonly' => (!$__templater->method($__vars['bbCode'], 'canEdit', array())),
		'dir' => 'ltr',
	), array(
		'label' => 'BB code tag',
	)) . '

			' . $__templater->formTextBoxRow(array(
		'name' => 'title',
		'value' => ($__templater->method($__vars['bbCode'], 'exists', array()) ? $__vars['bbCode']['MasterTitle']['phrase_text'] : ''),
		'readonly' => (!$__templater->method($__vars['bbCode'], 'canEdit', array())),
	), array(
		'label' => 'Title',
	)) . '
			' . $__templater->formTextAreaRow(array(
		'name' => 'desc',
		'value' => ($__templater->method($__vars['bbCode'], 'exists', array()) ? $__vars['bbCode']['MasterDesc']['phrase_text'] : ''),
		'autosize' => 'true',
		'readonly' => (!$__templater->method($__vars['bbCode'], 'canEdit', array())),
	), array(
		'label' => 'Description',
	)) . '

			' . $__templater->formRadioRow(array(
		'name' => 'bb_code_mode',
		'value' => $__vars['bbCode']['bb_code_mode'],
		'readonly' => (!$__templater->method($__vars['bbCode'], 'canEdit', array())),
	), array(array(
		'value' => 'replace',
		'label' => 'Simple replacement',
		'_type' => 'option',
	),
	array(
		'value' => 'callback',
		'label' => 'PHP callback',
		'_type' => 'option',
	)), array(
		'label' => 'Replacement mode',
	)) . '

			' . $__templater->formRadioRow(array(
		'name' => 'has_option',
		'value' => $__vars['bbCode']['has_option'],
		'readonly' => (!$__templater->method($__vars['bbCode'], 'canEdit', array())),
	), array(array(
		'value' => 'yes',
		'label' => 'Yes',
		'_type' => 'option',
	),
	array(
		'value' => 'no',
		'label' => 'No',
		'_type' => 'option',
	),
	array(
		'value' => 'optional',
		'explain' => 'This tag will work with and without the option provided. This is most commonly used with PHP callbacks.',
		'label' => '
					' . 'Optional' . '
				',
		'_type' => 'option',
	)), array(
		'label' => 'Supports option parameter',
	)) . '

			' . $__templater->formCodeEditorRow(array(
		'name' => 'replace_html',
		'value' => $__vars['bbCode']['replace_html'],
		'mode' => 'html',
		'data-line-wrapping' => 'true',
		'readonly' => (!$__templater->method($__vars['bbCode'], 'canEdit', array())),
		'class' => 'codeEditor--autoSize',
	), array(
		'label' => 'HTML replacement',
		'explain' => 'Use {option} to refer to content inside the tag\'s option (if provided) and {text} to refer to content within the tag.',
	)) . '

			' . $__templater->callMacro('helper_callback_fields', 'callback_row', array(
		'label' => 'PHP callback',
		'explain' => 'This callback will receive these parameters: $tagChildren, $tagOption, $tag,  array $options, \\XF\\BbCode\\Renderer\\AbstractRenderer $renderer.',
		'data' => $__vars['bbCode'],
		'readOnly' => (!$__templater->method($__vars['bbCode'], 'canEdit', array())),
	), $__vars) . '

			<hr class="formRowSep" />

			' . $__templater->formRadioRow(array(
		'name' => 'editor_icon_type',
		'value' => $__vars['bbCode']['editor_icon_type'],
		'readonly' => (!$__templater->method($__vars['bbCode'], 'canEdit', array())),
	), array(array(
		'value' => '',
		'label' => 'None',
		'_type' => 'option',
	),
	array(
		'value' => 'fa',
		'label' => 'Font Awesome icon',
		'_dependent' => array($__templater->formTextBox(array(
		'name' => 'editor_icon_fa',
		'readonly' => (!$__templater->method($__vars['bbCode'], 'canEdit', array())),
		'value' => (($__vars['bbCode']['editor_icon_type'] == 'fa') ? $__vars['bbCode']['editor_icon_value'] : ''),
		'maxlength' => $__templater->func('max_length', array($__vars['bbCode'], 'editor_icon_value', ), false),
		'dir' => 'ltr',
	))),
		'_type' => 'option',
	),
	array(
		'value' => 'image',
		'label' => 'Image',
		'_dependent' => array($__templater->formTextBox(array(
		'name' => 'editor_icon_image',
		'readonly' => (!$__templater->method($__vars['bbCode'], 'canEdit', array())),
		'value' => (($__vars['bbCode']['editor_icon_type'] == 'image') ? $__vars['bbCode']['editor_icon_value'] : ''),
		'maxlength' => $__templater->func('max_length', array($__vars['bbCode'], 'editor_icon_value', ), false),
		'dir' => 'ltr',
	))),
		'_type' => 'option',
	)), array(
		'label' => 'Editor icon',
	)) . '

			<hr class="formRowSep" />

			' . $__templater->formTextAreaRow(array(
		'name' => 'example',
		'value' => ($__templater->method($__vars['bbCode'], 'exists', array()) ? $__vars['bbCode']['MasterExample']['phrase_text'] : ''),
		'autosize' => 'true',
		'readonly' => (!$__templater->method($__vars['bbCode'], 'canEdit', array())),
	), array(
		'label' => 'Example usage',
		'explain' => 'If you provide an example, this BB code will appear on the BB code help page.',
	)) . '

			' . $__templater->formTextAreaRow(array(
		'name' => 'output',
		'value' => ($__templater->method($__vars['bbCode'], 'exists', array()) ? $__vars['bbCode']['MasterOutput']['phrase_text'] : ''),
		'autosize' => 'true',
		'readonly' => (!$__templater->method($__vars['bbCode'], 'canEdit', array())),
	), array(
		'label' => 'Example output',
		'explain' => 'Control how the example will appear on the BB code help page. If an output is not entered, the example will be rendered instead.',
	)) . '

			<hr class="formRowSep" />

			' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'allow_signature',
		'value' => '1',
		'selected' => $__vars['bbCode']['allow_signature'],
		'label' => '
					' . 'Allow this BB code in signatures' . '
				',
		'_type' => 'option',
	)), array(
	)) . '

			' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'active',
		'value' => '1',
		'selected' => $__vars['bbCode']['active'],
		'hint' => (($__vars['xf']['development'] AND $__vars['bbCode']['addon_id']) ? 'The value of this field will not be changed when this add-on is upgraded.' : ''),
		'label' => '
					' . 'Enabled' . '
				',
		'_type' => 'option',
	)), array(
	)) . '

			' . $__templater->callMacro('addon_macros', 'addon_edit', array(
		'addOnId' => $__vars['bbCode']['addon_id'],
	), $__vars) . '
		</div>

		<h3 class="block-formSectionHeader">
			<span class="collapseTrigger collapseTrigger--block" data-xf-click="toggle" data-target="< :up:next">
				<span class="block-formSectionHeader-aligner">' . 'Advanced options' . '</span>
			</span>
		</h3>
		<div class="block-body block-body--collapsible">
			' . $__templater->formTextAreaRow(array(
		'name' => 'option_regex',
		'value' => $__vars['bbCode']['option_regex'],
		'code' => 'true',
		'autosize' => 'true',
		'readonly' => (!$__templater->method($__vars['bbCode'], 'canEdit', array())),
	), array(
		'label' => 'Option match regular expression',
		'explain' => 'If provided, the tag will only be valid if the option matches this regular expression. This will be ignored if no option is provided. Please include the delimiters and pattern modifiers.',
	)) . '

			' . $__templater->formCheckBoxRow(array(
		'readonly' => (!$__templater->method($__vars['bbCode'], 'canEdit', array())),
	), array(array(
		'name' => 'disable_smilies',
		'value' => '1',
		'selected' => $__vars['bbCode']['disable_smilies'],
		'label' => '
					' . 'Disable smilies' . '
				',
		'_type' => 'option',
	),
	array(
		'name' => 'disable_nl2br',
		'value' => '1',
		'selected' => $__vars['bbCode']['disable_nl2br'],
		'label' => '
					' . 'Disable line break conversion' . '
				',
		'_type' => 'option',
	),
	array(
		'name' => 'disable_autolink',
		'value' => '1',
		'selected' => $__vars['bbCode']['disable_autolink'],
		'label' => '
					' . 'Disable auto-linking' . '
				',
		'_type' => 'option',
	),
	array(
		'name' => 'plain_children',
		'value' => '1',
		'selected' => $__vars['bbCode']['plain_children'],
		'label' => '
					' . 'Stop parsing BB code' . '
				',
		'_type' => 'option',
	)), array(
		'label' => 'Within this BB code',
	)) . '

			' . $__templater->formCheckBoxRow(array(
		'readonly' => (!$__templater->method($__vars['bbCode'], 'canEdit', array())),
	), array(array(
		'name' => 'allow_empty',
		'value' => '1',
		'selected' => $__vars['bbCode']['allow_empty'],
		'label' => 'Display HTML replacement when empty',
		'explain' => 'If selected, the replacement HTML will be displayed even if there is no text inside this BB code. Normally, empty BB code tags are silently ignored.',
		'_type' => 'option',
	)), array(
	)) . '

			' . $__templater->formNumberBoxRow(array(
		'name' => 'trim_lines_after',
		'value' => $__vars['bbCode']['trim_lines_after'],
		'min' => '0',
		'max' => '10',
		'readonly' => (!$__templater->method($__vars['bbCode'], 'canEdit', array())),
	), array(
		'label' => 'Trim line breaks after',
		'explain' => 'If this tag is a block-level tag, you may want to ignore 1 or 2 line breaks that come after this tag. This prevents the appearance of extra line breaks being inserted if users put this tag on its own line.',
	)) . '

			<hr class="formRowSep" />

			' . $__templater->formCodeEditorRow(array(
		'name' => 'replace_html_email',
		'value' => $__vars['bbCode']['replace_html_email'],
		'mode' => 'html',
		'data-line-wrapping' => 'true',
		'readonly' => (!$__templater->method($__vars['bbCode'], 'canEdit', array())),
		'class' => 'codeEditor--autoSize',
	), array(
		'label' => 'HTML email replacement',
		'explain' => 'If provided, this will override the HTML replacement when being rendered for an HTML email. If this is left empty, the default HTML replacement will be used.',
	)) . '

			' . $__templater->formCodeEditorRow(array(
		'name' => 'replace_text',
		'value' => $__vars['bbCode']['replace_text'],
		'mode' => 'text',
		'data-line-wrapping' => 'true',
		'readonly' => (!$__templater->method($__vars['bbCode'], 'canEdit', array())),
		'class' => 'codeEditor--autoSize',
	), array(
		'label' => 'Text replacement',
		'explain' => 'If provided, this replacement will be used when rendering this tag to text. If this is left empty, the tag will effectively be ignored, leaving only the text inside it.',
	)) . '
		</div>

		' . $__templater->formSubmitRow(array(
		'sticky' => 'true',
		'icon' => 'save',
	), array(
	)) . '
	</div>

', array(
		'action' => $__templater->func('link', array('bb-codes/save', $__vars['bbCode'], ), false),
		'ajax' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});