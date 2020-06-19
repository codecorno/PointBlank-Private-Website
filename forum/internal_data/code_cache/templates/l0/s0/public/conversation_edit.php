<?php
// FROM HASH: 7eb78ce27c48cebe18f0aaa1b6553549
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Edit conversation');
	$__finalCompiled .= '

';
	$__templater->breadcrumb($__templater->preEscaped('Conversations'), $__templater->func('link', array('conversations', ), false), array(
	));
	$__finalCompiled .= '
';
	$__templater->breadcrumb($__templater->preEscaped($__templater->escape($__vars['conversation']['title'])), $__templater->func('link', array('conversations', $__vars['conversation'], ), false), array(
	));
	$__finalCompiled .= '

' . $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formTextBoxRow(array(
		'name' => 'title',
		'value' => $__vars['conversation']['title'],
		'maxlength' => $__templater->func('max_length', array($__vars['conversation'], 'title', ), false),
	), array(
		'label' => 'Title',
	)) . '

			' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'open_invite',
		'checked' => ($__vars['conversation']['open_invite'] ? 'checked' : ''),
		'label' => 'Allow anyone in the conversation to invite others',
		'_type' => 'option',
	),
	array(
		'name' => 'conversation_locked',
		'checked' => ($__vars['conversation']['conversation_open'] ? '' : 'checked'),
		'label' => 'Lock conversation',
		'hint' => 'No responses will be allowed',
		'_type' => 'option',
	)), array(
	)) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'icon' => 'save',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('conversations/edit', $__vars['conversation'], ), false),
		'class' => 'block',
		'ajax' => 'true',
	));
	return $__finalCompiled;
});