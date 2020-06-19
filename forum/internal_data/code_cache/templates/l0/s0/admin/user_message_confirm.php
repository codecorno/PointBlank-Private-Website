<?php
// FROM HASH: 1008944b94f92a8b4ce0cf8657ed4c42
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Confirm sending message');
	$__finalCompiled .= '

' . $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formRow('
				' . $__templater->filter($__vars['total'], array(array('number', array()),), true) . '
				<span role="presentation" aria-hidden="true">&middot;</span>
				<a href="' . $__templater->func('link', array('users/list', null, array('criteria' => $__vars['criteria'], ), ), true) . '">' . 'View full list' . '</a>
			', array(
		'label' => 'Number of users matching criteria',
	)) . '
			' . $__templater->formRow('
				' . $__templater->button('', array(
		'type' => 'submit',
		'name' => 'preview',
		'value' => '1',
		'class' => 'js-previewButton',
		'icon' => 'preview',
	), '', array(
	)) . '
			', array(
		'label' => 'Test',
	)) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'submit' => 'Send message',
	), array(
	)) . '
	</div>

	' . $__templater->formHiddenVal('json_criteria', $__templater->filter($__vars['criteria'], array(array('json', array()),), false), array(
	)) . '

	' . $__templater->formHiddenVal('total', $__vars['total'], array(
	)) . '

	' . $__templater->formHiddenVal('from_user', $__vars['user']['username'], array(
	)) . '

	' . $__templater->formHiddenVal('message_title', $__vars['message']['message_title'], array(
	)) . '
	' . $__templater->formHiddenVal('message_body', $__vars['message']['message_body'], array(
	)) . '

	' . $__templater->formHiddenVal('open_invite', $__vars['message']['open_invite'], array(
	)) . '
	' . $__templater->formHiddenVal('conversation_locked', $__vars['message']['conversation_locked'], array(
	)) . '

	' . $__templater->formHiddenVal('delete_type', $__vars['message']['delete_type'], array(
	)) . '
', array(
		'action' => $__templater->func('link', array('users/message/send', ), false),
		'class' => 'block',
		'preview' => $__templater->func('link', array('users/message/preview', ), false),
	));
	return $__finalCompiled;
});