<?php
// FROM HASH: 0a83c299d6bd16c4cafb5776c1c5910f
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->method($__vars['warning'], 'isInsert', array())) {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Add warning');
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Edit warning' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['warning']['title']));
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	if ($__templater->method($__vars['warning'], 'isUpdate', array())) {
		$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('', array(
			'href' => $__templater->func('link', array('warnings/delete', $__vars['warning'], ), false),
			'icon' => 'delete',
			'overlay' => 'true',
		), '', array(
		)) . '
');
	}
	$__finalCompiled .= '

';
	$__compilerTemp1 = $__templater->mergeChoiceOptions(array(), $__vars['userGroups']);
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formTextBoxRow(array(
		'name' => 'title',
		'value' => $__vars['warning']['MasterTitle']['phrase_text'],
		'maxlength' => $__templater->func('max_length', array($__vars['warning'], 'title', ), false),
	), array(
		'label' => 'Title',
	)) . '

			' . $__templater->formNumberBoxRow(array(
		'name' => 'points_default',
		'value' => $__vars['warning']['points_default'],
		'min' => '0',
		'max' => '65535',
	), array(
		'label' => 'Warning points',
	)) . '

			' . $__templater->formRadioRow(array(
		'name' => 'expiry_type_base',
	), array(array(
		'value' => 'never',
		'selected' => $__vars['warning']['expiry_type'] == 'never',
		'label' => 'Never',
		'_type' => 'option',
	),
	array(
		'value' => 'other',
		'selected' => $__vars['warning']['expiry_type'] != 'never',
		'label' => 'After time' . $__vars['xf']['language']['label_separator'],
		'_dependent' => array('
						<div class="inputGroup">
							' . $__templater->formNumberBox(array(
		'name' => 'expiry_default',
		'value' => ($__vars['warning']['expiry_default'] ? $__vars['warning']['expiry_default'] : 1),
		'min' => '0',
	)) . '
							<span class="inputGroup-splitter"></span>
							' . $__templater->formSelect(array(
		'name' => 'expiry_type',
		'value' => (($__vars['warning']['expiry_type'] == 'never') ? 'months' : $__vars['warning']['expiry_type']),
		'class' => 'input--inline',
	), array(array(
		'value' => 'days',
		'label' => 'Days',
		'_type' => 'option',
	),
	array(
		'value' => 'weeks',
		'label' => 'Weeks',
		'_type' => 'option',
	),
	array(
		'value' => 'months',
		'label' => 'Months',
		'_type' => 'option',
	),
	array(
		'value' => 'years',
		'label' => 'Years',
		'_type' => 'option',
	))) . '
						</div>

					'),
		'_type' => 'option',
	)), array(
		'label' => 'Points expiry',
	)) . '

			' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'is_editable',
		'selected' => $__vars['warning']['is_editable'],
		'label' => 'Points and expiry time are editable when giving warning',
		'_type' => 'option',
	)), array(
	)) . '

			' . $__templater->formCheckBoxRow(array(
		'name' => 'extra_user_group_ids',
		'value' => $__vars['warning']['extra_user_group_ids'],
	), $__compilerTemp1, array(
		'label' => 'Extra user groups',
		'explain' => 'A user receiving this warning can be added to specific user groups until the warning has expired.',
	)) . '
		</div>

		<h3 class="block-formSectionHeader"><span class="block-formSectionHeader-aligner">' . 'Conversation settings' . '</span></h3>
		<div class="block-body">
			' . $__templater->formTextBoxRow(array(
		'name' => 'conversation_title',
		'value' => $__vars['warning']['MasterConversationTitle']['phrase_text'],
	), array(
		'label' => 'Default title',
	)) . '

			' . $__templater->formTextAreaRow(array(
		'name' => 'conversation_text',
		'value' => $__vars['warning']['MasterConversationText']['phrase_text'],
		'autosize' => 'true',
	), array(
		'label' => 'Default text',
		'explain' => 'You may use the following placeholders: {title}, {content} and {url} to dynamically insert the content\'s information; {name} to insert the name of the user receiving the warning; and {staff} to insert the name of the staff member giving the warning. You may use BB code.',
	)) . '
		</div>

		' . $__templater->formSubmitRow(array(
		'icon' => 'save',
		'sticky' => 'true',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('warnings/save', $__vars['warning'], ), false),
		'ajax' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});