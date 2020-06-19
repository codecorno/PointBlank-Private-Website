<?php
// FROM HASH: 3a038a578b10d69d196b8d2b681d09d5
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Iniciar conversa');
	$__finalCompiled .= '

';
	$__templater->breadcrumb($__templater->preEscaped('Conversas'), $__templater->func('link', array('conversations', ), false), array(
	));
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	$__compilerTemp1 .= '
			';
	if (!$__templater->test($__vars['errorUsernames'], 'empty', array())) {
		$__compilerTemp1 .= '
				<div>' . 'Você não pode iniciar uma conversa com os seguintes usuários por causa de suas configurações de privacidade: ' . $__templater->filter($__vars['errorUsernames'], array(array('join', array(', ', )),), true) . '.' . '</div>
			';
	}
	$__compilerTemp1 .= '

			';
	if (!$__templater->test($__vars['notFoundUsernames'], 'empty', array())) {
		$__compilerTemp1 .= '
				<div>' . 'Não foi possível encontrar os seguintes usuários: ' . $__templater->filter($__vars['notFoundUsernames'], array(array('join', array(', ', )),), true) . '.' . '</div>
			';
	}
	$__compilerTemp1 .= '

			';
	if ($__vars['recipientLimit']) {
		$__compilerTemp1 .= '
				<div>' . 'Você excedeu o número permitido de destinatários (' . $__templater->escape($__vars['recipientLimit']) . ') para esta mensagem.' . '</div>
			';
	}
	$__compilerTemp1 .= '
		';
	if (strlen(trim($__compilerTemp1)) > 0) {
		$__finalCompiled .= '
	<div class="blockMessage blockMessage--error blockMessage--iconic">
		' . $__compilerTemp1 . '
	</div>
';
	}
	$__finalCompiled .= '

';
	$__compilerTemp2 = '';
	if ($__vars['attachmentData']) {
		$__compilerTemp2 .= '
					' . $__templater->callMacro('helper_attach_upload', 'upload_block', array(
			'attachmentData' => $__vars['attachmentData'],
			'forceHash' => $__vars['draft']['attachment_hash'],
		), $__vars) . '
				';
	}
	$__compilerTemp3 = '';
	if ($__vars['xf']['options']['multiQuote']) {
		$__compilerTemp3 .= '
					' . $__templater->callMacro('multi_quote_macros', 'button', array(
			'href' => $__templater->func('link', array('conversations/multi-quote', $__vars['conversation'], ), false),
			'messageSelector' => '.js-message',
			'storageKey' => 'multiQuoteConversation',
		), $__vars) . '
				';
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formTokenInputRow(array(
		'name' => 'recipients',
		'value' => $__vars['to'],
		'href' => $__templater->func('link', array('members/find', ), false),
		'max-tokens' => (($__vars['maxRecipients'] > -1) ? $__vars['maxRecipients'] : null),
	), array(
		'rowtype' => 'fullWidth',
		'label' => ((($__vars['maxRecipients'] == -1) OR ($__vars['maxRecipients'] > 1)) ? 'Destinatários' : 'Recipient'),
		'explain' => ((($__vars['maxRecipients'] == -1) OR ($__vars['maxRecipients'] > 1)) ? 'Separe os nomes com uma vírgula.' : null),
	)) . '

			' . $__templater->formTextBoxRow(array(
		'name' => 'title',
		'value' => $__vars['title'],
		'class' => 'input--title',
		'maxlength' => $__templater->func('max_length', array('XF:ConversationMaster', 'title', ), false),
		'placeholder' => 'Título' . $__vars['xf']['language']['ellipsis'],
	), array(
		'rowtype' => 'fullWidth noLabel',
		'label' => 'Título',
	)) . '

			' . $__templater->formEditorRow(array(
		'name' => 'message',
		'value' => $__vars['message'],
		'attachments' => ($__vars['attachmentData'] ? $__vars['attachmentData']['attachments'] : array()),
	), array(
		'rowtype' => 'fullWidth noLabel mergePrev',
	)) . '

			' . $__templater->formRow('
				' . $__compilerTemp2 . '

				' . $__compilerTemp3 . '

				' . $__templater->button('', array(
		'class' => 'button--link u-jsOnly',
		'data-xf-click' => 'preview-click',
		'icon' => 'preview',
	), '', array(
	)) . '
			', array(
	)) . '

			' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'open_invite',
		'checked' => (($__vars['draft']['open_invite'] OR $__vars['conversation']['open_invite']) ? 'checked' : ''),
		'label' => '
					' . 'Permitir que qualquer pessoa na conversa convide outras pessoas' . '
				',
		'_type' => 'option',
	),
	array(
		'name' => 'conversation_locked',
		'checked' => (($__vars['draft']['conversation_open'] OR $__vars['conversation']['conversation_open']) ? '' : 'checked'),
		'label' => '
					' . 'Bloquear conversa (não serão permitidas respostas)' . '
				',
		'_type' => 'option',
	)), array(
	)) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'submit' => 'Iniciar conversa',
		'sticky' => 'true',
		'icon' => 'conversation',
	), array(
		'html' => '
				' . $__templater->button('', array(
		'class' => 'u-jsOnly',
		'data-xf-click' => 'preview-click',
		'icon' => 'preview',
	), '', array(
	)) . '
			',
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('conversations/add', ), false),
		'ajax' => 'true',
		'draft' => $__templater->func('link', array('conversations/draft', ), false),
		'data-preview-url' => $__templater->func('link', array('conversations/add-preview', ), false),
		'class' => 'block',
		'data-xf-init' => 'attachment-manager',
	));
	return $__finalCompiled;
});