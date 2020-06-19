<?php
// FROM HASH: 86f75dc7b98d64a55e72dd2792678d22
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Detalhes da conta');
	$__finalCompiled .= '

';
	$__templater->wrapTemplate('account_wrapper', $__vars);
	$__finalCompiled .= '

';
	if (!$__templater->method($__vars['xf']['visitor'], 'canEditProfile', array())) {
		$__finalCompiled .= '
	<div class="blockMessage blockMessage--important">' . 'Os detalhes completos da sua conta não são editáveis.' . '</div>
';
	}
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	if ($__vars['xf']['visitor']['email']) {
		$__compilerTemp1 .= '
					' . $__templater->escape($__vars['xf']['visitor']['email']) . '
				';
	} else {
		$__compilerTemp1 .= '
					<i>' . 'Nenhuma' . '</i>
				';
	}
	$__compilerTemp2 = '';
	if ($__templater->method($__vars['xf']['visitor'], 'canUploadAvatar', array())) {
		$__compilerTemp2 .= '
				' . $__templater->formRow('

					' . $__templater->func('avatar', array($__vars['xf']['visitor'], 'm', false, array(
			'href' => $__templater->func('link', array('account/avatar', ), false),
			'data-xf-click' => 'overlay',
		))) . '
				', array(
			'label' => 'Avatar',
			'explain' => 'Clique na imagem para alterar o seu avatar.',
		)) . '
			';
	}
	$__compilerTemp3 = '';
	if ($__templater->method($__vars['xf']['visitor'], 'canEditProfile', array())) {
		$__compilerTemp3 .= '
				';
		if ($__templater->method($__vars['xf']['visitor'], 'hasPermission', array('general', 'editCustomTitle', ))) {
			$__compilerTemp3 .= '
					' . $__templater->formTextBoxRow(array(
				'name' => 'user[custom_title]',
				'value' => $__vars['xf']['visitor']['custom_title_'],
				'maxlength' => $__templater->func('max_length', array($__vars['xf']['visitor'], 'custom_title', ), false),
			), array(
				'label' => 'Título personalizado',
				'explain' => 'Se especificado, isso substituirá o título exibido em seu nome em suas postagens.',
			)) . '
				';
		}
		$__compilerTemp3 .= '

				<hr class="formRowSep" />

				';
		if (($__vars['xf']['visitor']['Profile']['dob_day'] AND ($__vars['xf']['visitor']['Profile']['dob_month'] AND $__vars['xf']['visitor']['Profile']['dob_year']))) {
			$__compilerTemp3 .= '
					';
			$__vars['birthday'] = $__templater->method($__vars['xf']['visitor']['Profile'], 'getBirthday', array(true, ));
			$__compilerTemp3 .= $__templater->formRow('

						' . '' . '
						' . $__templater->func('date', array($__vars['birthday']['timeStamp'], $__vars['birthday']['format'], ), true) . '
					', array(
				'label' => 'Data de nascimento',
				'explain' => 'Uma vez que o seu aniversário foi introduzido, não pode ser alterado. Entre em contato com um administrador se ele estiver incorreto.',
			)) . '
				';
		} else {
			$__compilerTemp3 .= '
					' . $__templater->callMacro('helper_user_dob_edit', 'dob_edit', array(
				'dobData' => $__vars['xf']['visitor']['Profile'],
			), $__vars) . '
				';
		}
		$__compilerTemp3 .= '

				' . $__templater->callMacro('helper_account', 'dob_privacy_row', array(), $__vars) . '

				<hr class="formRowSep" />

				' . $__templater->formTextBoxRow(array(
			'name' => 'profile[location]',
			'value' => $__vars['xf']['visitor']['Profile']['location_'],
			'maxlength' => $__templater->func('max_length', array($__vars['xf']['visitor']['Profile'], 'location', ), false),
		), array(
			'label' => 'Localização',
		)) . '
				' . $__templater->formTextBoxRow(array(
			'name' => 'profile[website]',
			'value' => $__vars['xf']['visitor']['Profile']['website_'],
			'type' => 'url',
			'maxlength' => $__templater->func('max_length', array($__vars['xf']['visitor']['Profile'], 'website', ), false),
		), array(
			'label' => 'Website',
		)) . '

				' . $__templater->callMacro('custom_fields_macros', 'custom_fields_edit', array(
			'type' => 'users',
			'group' => 'personal',
			'set' => $__vars['xf']['visitor']['Profile']['custom_fields'],
		), $__vars) . '

				<hr class="formRowSep" />

				' . $__templater->formEditorRow(array(
			'name' => 'about',
			'value' => $__vars['xf']['visitor']['Profile']['about_'],
			'previewable' => '0',
		), array(
			'label' => 'Sobre você',
		)) . '
			';
	}
	$__compilerTemp4 = '';
	if ($__templater->method($__vars['xf']['visitor'], 'canEditProfile', array())) {
		$__compilerTemp4 .= '
			';
		$__compilerTemp5 = '';
		$__compilerTemp5 .= '
						' . $__templater->callMacro('custom_fields_macros', 'custom_fields_edit', array(
			'type' => 'users',
			'group' => 'contact',
			'set' => $__vars['xf']['visitor']['Profile']['custom_fields'],
		), $__vars) . '
					';
		if (strlen(trim($__compilerTemp5)) > 0) {
			$__compilerTemp4 .= '
				<h2 class="block-formSectionHeader"><span class="block-formSectionHeader-aligner">' . 'Identidades' . '</span></h2>
				<div class="block-body">
					' . $__compilerTemp5 . '
				</div>
			';
		}
		$__compilerTemp4 .= '
			' . $__templater->formSubmitRow(array(
			'icon' => 'save',
			'sticky' => 'true',
		), array(
		)) . '
		';
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formRow('

				' . $__compilerTemp1 . '
				' . $__templater->button('Alterar', array(
		'href' => $__templater->func('link', array('account/email', ), false),
		'class' => 'button--link',
		'overlay' => 'true',
	), '', array(
	)) . '
			', array(
		'rowtype' => 'button',
		'label' => 'E-mail',
	)) . '

			' . $__templater->callMacro('helper_account', 'email_options_row', array(
		'showExplain' => true,
	), $__vars) . '

			' . $__compilerTemp2 . '

			' . $__compilerTemp3 . '
		</div>
		' . $__compilerTemp4 . '
	</div>
', array(
		'action' => $__templater->func('link', array('account/account-details', ), false),
		'ajax' => 'true',
		'class' => 'block',
		'data-force-flash-message' => 'true',
	));
	return $__finalCompiled;
});