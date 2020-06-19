<?php
// FROM HASH: 5f291ba1c70b00403c031136023cef24
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->formRow('

	' . $__templater->formCheckBox(array(
	), array(array(
		'name' => $__vars['inputName'] . '[visible]',
		'selected' => $__vars['option']['option_value']['visible'],
		'label' => 'Mostrar status online',
		'_type' => 'option',
	),
	array(
		'name' => $__vars['inputName'] . '[activity_visible]',
		'selected' => $__vars['option']['option_value']['activity_visible'],
		'label' => 'Mostrar a atividade atual',
		'_type' => 'option',
	),
	array(
		'name' => $__vars['inputName'] . '[content_show_signature]',
		'selected' => $__vars['option']['option_value']['content_show_signature'],
		'label' => 'Mostrar assinaturas com mensagens',
		'_type' => 'option',
	),
	array(
		'name' => $__vars['inputName'] . '[show_dob_date]',
		'selected' => $__vars['option']['option_value']['show_dob_date'],
		'label' => 'Mostrar dia e mês de nascimento',
		'_type' => 'option',
	),
	array(
		'name' => $__vars['inputName'] . '[show_dob_year]',
		'selected' => $__vars['option']['option_value']['show_dob_year'],
		'label' => 'Mostrar ano de nascimento',
		'_type' => 'option',
	),
	array(
		'name' => $__vars['inputName'] . '[receive_admin_email]',
		'selected' => $__vars['option']['option_value']['receive_admin_email'],
		'label' => 'Receive news and update emails',
		'_type' => 'option',
	),
	array(
		'name' => $__vars['inputName'] . '[email_on_conversation]',
		'selected' => $__vars['option']['option_value']['email_on_conversation'],
		'label' => 'Receber e-mail quando uma nova mensagem de conversa é recebida',
		'_type' => 'option',
	))) . '
	<div class="u-inputSpacer">
		<dl class="inputLabelPair">
			<dt><label for="' . $__templater->escape($__vars['inputName']) . '_dws">' . 'Seguir ao conteúdo da criação' . '</label></dt>
			<dd>' . $__templater->formSelect(array(
		'name' => $__vars['inputName'] . '[creation_watch_state]',
		'value' => $__vars['option']['option_value']['creation_watch_state'],
		'id' => $__vars['inputName'] . '_dws',
	), array(array(
		'value' => 'watch_no_email',
		'label' => 'Sim',
		'_type' => 'option',
	),
	array(
		'value' => 'watch_email',
		'label' => 'Sim, com e-mail',
		'_type' => 'option',
	),
	array(
		'label' => 'Não',
		'_type' => 'option',
	))) . '</dd>
		</dl>
		<dl class="inputLabelPair">
			<dt><label for="' . $__templater->escape($__vars['inputName']) . '_dws">' . 'Seguir conteúdo na interação' . '</label></dt>
			<dd>' . $__templater->formSelect(array(
		'name' => $__vars['inputName'] . '[interaction_watch_state]',
		'value' => $__vars['option']['option_value']['interaction_watch_state'],
		'id' => $__vars['inputName'] . '_dws',
	), array(array(
		'value' => 'watch_no_email',
		'label' => 'Sim',
		'_type' => 'option',
	),
	array(
		'value' => 'watch_email',
		'label' => 'Sim, com e-mail',
		'_type' => 'option',
	),
	array(
		'label' => 'Não',
		'_type' => 'option',
	))) . '</dd>
		</dl>
		<dl class="inputLabelPair">
			<dt><label for="' . $__templater->escape($__vars['inputName']) . '_avp">' . 'Ver detalhes da página de perfil do usuário' . '</label></dt>
			<dd>' . $__templater->formSelect(array(
		'name' => $__vars['inputName'] . '[allow_view_profile]',
		'value' => $__vars['option']['option_value']['allow_view_profile'],
		'id' => $__vars['inputName'] . '_avp',
	), array(array(
		'value' => 'everyone',
		'label' => 'Todos os visitantes',
		'_type' => 'option',
	),
	array(
		'value' => 'members',
		'label' => 'Apenas membros',
		'_type' => 'option',
	),
	array(
		'value' => 'followed',
		'label' => 'Apenas membros seguidos',
		'_type' => 'option',
	),
	array(
		'value' => 'none',
		'label' => 'Ninguém',
		'_type' => 'option',
	))) . '</dd>
		</dl>
		<dl class="inputLabelPair">
			<dt><label for="' . $__templater->escape($__vars['inputName']) . '_app">' . 'Postar mensagens na página de perfil deste usuário' . '</label></dt>
			<dd>' . $__templater->formSelect(array(
		'name' => $__vars['inputName'] . '[allow_post_profile]',
		'value' => $__vars['option']['option_value']['allow_post_profile'],
		'id' => $__vars['inputName'] . '_app',
	), array(array(
		'value' => 'members',
		'label' => 'Apenas membros',
		'_type' => 'option',
	),
	array(
		'value' => 'followed',
		'label' => 'Apenas membros seguidos',
		'_type' => 'option',
	),
	array(
		'value' => 'none',
		'label' => 'Ninguém',
		'_type' => 'option',
	))) . '</dd>
		</dl>
		<dl class="inputLabelPair">
			<dt><label for="' . $__templater->escape($__vars['inputName']) . '_arnf">' . 'Receber feed de notícias deste usuário' . '</label></dt>
			<dd>' . $__templater->formSelect(array(
		'name' => $__vars['inputName'] . '[allow_receive_news_feed]',
		'value' => $__vars['option']['option_value']['allow_receive_news_feed'],
		'id' => $__vars['inputName'] . '_arnf',
	), array(array(
		'value' => 'everyone',
		'label' => 'Todos os visitantes',
		'_type' => 'option',
	),
	array(
		'value' => 'members',
		'label' => 'Apenas membros',
		'_type' => 'option',
	),
	array(
		'value' => 'followed',
		'label' => 'Apenas membros seguidos',
		'_type' => 'option',
	),
	array(
		'value' => 'none',
		'label' => 'Ninguém',
		'_type' => 'option',
	))) . '</dd>
		</dl>
		<dl class="inputLabelPair">
			<dt><label for="' . $__templater->escape($__vars['inputName']) . '_aspc">' . 'Iniciar conversa com este usuário' . '</label></dt>
			<dd>' . $__templater->formSelect(array(
		'name' => $__vars['inputName'] . '[allow_send_personal_conversation]',
		'value' => $__vars['option']['option_value']['allow_send_personal_conversation'],
		'id' => $__vars['inputName'] . '_aspc',
	), array(array(
		'value' => 'members',
		'label' => 'Apenas membros',
		'_type' => 'option',
	),
	array(
		'value' => 'followed',
		'label' => 'Apenas membros seguidos',
		'_type' => 'option',
	),
	array(
		'value' => 'none',
		'label' => 'Ninguém',
		'_type' => 'option',
	))) . '</dd>
		</dl>
		<dl class="inputLabelPair">
			<dt><label for="' . $__templater->escape($__vars['inputName']) . '_avi">' . 'Ver as identidades deste usuário' . '</label></dt>
			<dd>' . $__templater->formSelect(array(
		'name' => $__vars['inputName'] . '[allow_view_identities]',
		'id' => $__vars['inputName'] . '_avi',
		'value' => $__vars['option']['option_value']['allow_view_identities'],
	), array(array(
		'value' => 'everyone',
		'label' => 'Todos os visitantes',
		'_type' => 'option',
	),
	array(
		'value' => 'members',
		'label' => 'Apenas membros',
		'_type' => 'option',
	),
	array(
		'value' => 'followed',
		'label' => 'Apenas membros seguidos',
		'_type' => 'option',
	),
	array(
		'value' => 'none',
		'label' => 'Ninguém',
		'_type' => 'option',
	))) . '</dd>
		</dl>
	</div>
', array(
		'label' => $__templater->escape($__vars['option']['title']),
		'hint' => $__templater->escape($__vars['hintHtml']),
		'explain' => $__templater->escape($__vars['explainHtml']),
		'html' => $__templater->escape($__vars['listedHtml']),
	));
	return $__finalCompiled;
});