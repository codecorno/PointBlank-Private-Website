<?php
// FROM HASH: 1c4efa46d2e182c4d280eaa8d7086856
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Message users');
	$__finalCompiled .= '
';
	$__templater->pageParams['pageDescription'] = $__templater->preEscaped('You can use this form to start a conversation with the users which match the criteria specified below.');
	$__templater->pageParams['pageDescriptionMeta'] = true;
	$__finalCompiled .= '

';
	if ($__vars['sent']) {
		$__finalCompiled .= '
	<div class="blockMessage blockMessage--success blockMessage--iconic">
		' . 'Sua conversa foi iniciada com ' . $__templater->filter($__vars['sent'], array(array('number', array()),), true) . ' usuários.' . '
	</div>
';
	}
	$__finalCompiled .= '

' . $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formTextBoxRow(array(
		'name' => 'from_user',
		'value' => $__vars['xf']['visitor']['username'],
		'ac' => 'single',
	), array(
		'label' => 'From user',
		'explain' => '
					<p>' . 'Digite o nome de um usuário existente com o qual a conversa deve ser iniciada.' . '</p>
					<p><b>' . 'Note' . $__vars['xf']['language']['label_separator'] . '</b> ' . 'Você não pode iniciar uma conversa com você mesmo.' . '</p>
				',
	)) . '

			<hr class="formRowSep" />

			' . $__templater->formTextBoxRow(array(
		'name' => 'message_title',
		'maxlength' => '100',
		'required' => 'true',
	), array(
		'label' => 'Título da conversa',
	)) . '

			' . $__templater->formTextAreaRow(array(
		'name' => 'message_body',
		'rows' => '5',
		'autosize' => 'true',
		'required' => 'true',
	), array(
		'label' => 'Mensagem de conversa',
		'hint' => 'Você pode usar BBcode',
		'explain' => 'The following placeholders will be replaced in the message: {name}, {email}, {id}.' . ' ' . 'You may also use {phrase:phrase_title} which will be replaced with the phrase text in the recipient\'s language.',
	)) . '

			<hr class="formRowSep" />

			' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'open_invite',
		'value' => '1',
		'label' => 'Permitir que qualquer pessoa na conversa convide outras pessoas',
		'_type' => 'option',
	),
	array(
		'name' => 'conversation_locked',
		'value' => '1',
		'label' => 'Bloquear conversa (não serão permitidas respostas)',
		'_type' => 'option',
	)), array(
		'label' => 'Conversation options',
	)) . '

			' . $__templater->formRadioRow(array(
		'name' => 'delete_type',
	), array(array(
		'selected' => true,
		'label' => 'Do not leave conversation',
		'explain' => 'A conversa permanecerá na sua caixa de entrada e você será notificado das respostas.',
		'_type' => 'option',
	),
	array(
		'value' => 'deleted',
		'label' => 'Deixar conversa e aceitar mensagens futuras',
		'explain' => 'Should this conversation receive further responses in the future, this conversation will be restored to your inbox.',
		'_type' => 'option',
	),
	array(
		'value' => 'deleted_ignored',
		'label' => 'Deixar conversa e ignorar mensagens futuras',
		'explain' => 'You will not be notified of any future responses and the conversation will remain deleted.',
		'_type' => 'option',
	)), array(
		'label' => 'Future message handling',
	)) . '
		</div>

		<h2 class="block-formSectionHeader"><span class="block-formSectionHeader-aligner">' . 'User criteria' . '</span></h2>
		<div class="block-body">
			' . $__templater->includeTemplate('helper_user_search_criteria', $__vars) . '
		</div>

		' . $__templater->formSubmitRow(array(
		'submit' => 'Prosseguir' . $__vars['xf']['language']['ellipsis'],
		'sticky' => 'true',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('users/message/confirm', ), false),
		'class' => 'block',
	));
	return $__finalCompiled;
});