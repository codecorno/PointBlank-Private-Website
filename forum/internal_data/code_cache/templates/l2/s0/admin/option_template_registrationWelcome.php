<?php
// FROM HASH: fb88f76590eb1ab9b54c956616b9f39c
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__compilerTemp1 = '';
	if ($__vars['option']['option_value']['messageParticipants'] AND $__templater->func('is_array', array($__vars['option']['option_value']['messageParticipants'], ), false)) {
		$__compilerTemp1 .= '
				';
		$__vars['users'] = $__templater->method($__templater->method($__vars['xf']['app']['em'], 'getRepository', array('XF:User', )), 'getUsersByIdsOrdered', array($__vars['option']['option_value']['messageParticipants'], ));
		$__compilerTemp1 .= '
				';
		$__vars['value'] = $__templater->filter($__templater->method($__vars['users'], 'pluckNamed', array('username', )), array(array('join', array(', ', )),), false);
		$__compilerTemp1 .= '
			';
	} else {
		$__compilerTemp1 .= '
				';
		$__vars['value'] = $__templater->preEscaped($__templater->escape($__vars['xf']['visitor']['username']));
		$__compilerTemp1 .= '
			';
	}
	$__finalCompiled .= $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => $__vars['inputName'] . '[messageEnabled]',
		'selected' => $__vars['option']['option_value']['messageEnabled'],
		'data-hide' => 'true',
		'label' => 'Iniciar conversa de boas-vindas no registro',
		'_dependent' => array('
			<div>' . 'Outros participantes' . $__vars['xf']['language']['label_separator'] . '</div>
			' . $__compilerTemp1 . '
			' . $__templater->formTokenInput(array(
		'name' => $__vars['inputName'] . '[messageParticipants]',
		'value' => $__vars['value'],
		'href' => $__templater->func('link', array('users/find', ), false),
	)) . '
		', '
			<div>' . 'Título da conversa' . $__vars['xf']['language']['label_separator'] . '</div>
			' . $__templater->formTextBox(array(
		'name' => $__vars['inputName'] . '[messageTitle]',
		'value' => $__vars['option']['option_value']['messageTitle'],
		'placeholder' => 'Título da conversa' . $__vars['xf']['language']['ellipsis'],
		'maxlength' => '100',
	)) . '
		', '
			<div>' . 'Mensagem de conversa' . $__vars['xf']['language']['label_separator'] . '</div>
			' . $__templater->formTextArea(array(
		'name' => $__vars['inputName'] . '[messageBody]',
		'value' => $__vars['option']['option_value']['messageBody'],
		'rows' => '5',
		'autosize' => 'true',
	)) . '
			<p class="formRow-explain">' . 'The following placeholders will be replaced in the message: {name}, {email}, {id}.' . ' ' . 'You may also use {phrase:phrase_title} which will be replaced with the phrase text in the recipient\'s language.' . '</p>
		', '
			<div>' . 'Conversation options' . $__vars['xf']['language']['label_separator'] . '</div>
			' . $__templater->formCheckBox(array(
	), array(array(
		'name' => $__vars['inputName'] . '[messageOpenInvite]',
		'value' => '1',
		'selected' => $__vars['option']['option_value']['messageOpenInvite'],
		'label' => '
					' . 'Permitir que qualquer pessoa na conversa convide outras pessoas' . '
				',
		'_type' => 'option',
	),
	array(
		'name' => $__vars['inputName'] . '[messageLocked]',
		'value' => '1',
		'selected' => $__vars['option']['option_value']['messageLocked'],
		'label' => '
					' . 'Bloquear conversa (não serão permitidas respostas)' . '
				',
		'_type' => 'option',
	))) . '
		', '
			<div>' . 'Future message handling' . $__vars['xf']['language']['label_separator'] . '</div>
			' . $__templater->formRadio(array(
		'name' => $__vars['inputName'] . '[messageDelete]',
	), array(array(
		'value' => 'delete',
		'selected' => (!$__vars['option']['option_value']['messageDelete']) OR ($__vars['option']['option_value']['messageDelete'] == 'delete'),
		'label' => 'Deixar conversa e aceitar mensagens futuras',
		'explain' => 'Should this conversation receive further responses in the future, this conversation will be restored to your inbox.',
		'_type' => 'option',
	),
	array(
		'value' => 'delete_ignore',
		'selected' => $__vars['option']['option_value']['messageDelete'] == 'delete_ignore',
		'label' => 'Deixar conversa e ignorar mensagens futuras',
		'explain' => 'You will not be notified of any future responses and the conversation will remain deleted.',
		'_type' => 'option',
	),
	array(
		'value' => 'no_delete',
		'selected' => $__vars['option']['option_value']['messageDelete'] == 'no_delete',
		'label' => 'Do not leave conversation',
		'explain' => 'A conversa permanecerá na sua caixa de entrada e você será notificado das respostas.',
		'_type' => 'option',
	))) . '
		'),
		'_type' => 'option',
	),
	array(
		'name' => $__vars['inputName'] . '[emailEnabled]',
		'selected' => $__vars['option']['option_value']['emailEnabled'],
		'data-hide' => 'true',
		'label' => 'Enviar e-mail de boas-vindas no registro',
		'_dependent' => array('
			<div>' . 'De nome' . $__vars['xf']['language']['label_separator'] . '</div>
			' . $__templater->formTextBox(array(
		'name' => $__vars['inputName'] . '[emailFromName]',
		'value' => ($__vars['option']['option_value']['emailFromName'] ? $__vars['option']['option_value']['emailFromName'] : $__vars['xf']['options']['boardTitle']),
	)) . '
		', '
			<div>' . 'From email' . $__vars['xf']['language']['label_separator'] . '</div>
			' . $__templater->formTextBox(array(
		'name' => $__vars['inputName'] . '[emailFromEmail]',
		'value' => ($__vars['option']['option_value']['emailFromEmail'] ? $__vars['option']['option_value']['emailFromEmail'] : $__vars['xf']['options']['defaultEmailAddress']),
		'type' => 'email',
	)) . '
		', '
			<div>' . 'Email title' . $__vars['xf']['language']['label_separator'] . '</div>
			' . $__templater->formTextBox(array(
		'name' => $__vars['inputName'] . '[emailTitle]',
		'value' => $__vars['option']['option_value']['emailTitle'],
	)) . '
		', '
			<div>' . 'Email format' . $__vars['xf']['language']['label_separator'] . '</div>
			' . $__templater->formRadio(array(
		'name' => $__vars['inputName'] . '[emailFormat]',
		'value' => $__vars['option']['option_value']['emailFormat'],
	), array(array(
		'value' => 'plain',
		'label' => 'Plain text',
		'selected' => (!$__vars['option']['option_value']['emailFormat']) OR ($__vars['option']['option_value']['emailFormat'] == 'plain'),
		'_type' => 'option',
	),
	array(
		'value' => 'html',
		'label' => 'HTML',
		'_type' => 'option',
	))) . '
			<p class="formRow-explain">' . 'Observe que os clientes de e-mail lidam com HTML de maneiras muito diferentes. Certifique-se de testar antes de enviar e-mails em HTML. Uma versão de texto do seu e-mail será gerada removendo todas as tags HTML.' . '</p>
		', '
			<div>' . 'Email body' . $__vars['xf']['language']['label_separator'] . '</div>
			' . $__templater->formTextArea(array(
		'name' => $__vars['inputName'] . '[emailBody]',
		'value' => $__vars['option']['option_value']['emailBody'],
		'rows' => '5',
		'autosize' => 'true',
	)) . '
			<p class="formRow-explain">' . 'The following placeholders will be replaced in the message: {name}, {email}, {id}.' . ' ' . 'You may also use {phrase:phrase_title} which will be replaced with the phrase text in the recipient\'s language.' . '</p>
		'),
		'_type' => 'option',
	)), array(
		'label' => $__templater->escape($__vars['option']['title']),
		'hint' => $__templater->escape($__vars['hintHtml']),
		'explain' => $__templater->escape($__vars['explainHtml']),
		'html' => $__templater->escape($__vars['listedHtml']),
	));
	return $__finalCompiled;
});