<?php
// FROM HASH: 2af9b1e8f4c66ba0ab9f4ae52f3e438b
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__compilerTemp1 = '';
	if (!$__vars['xf']['visitor']['user_id']) {
		$__compilerTemp1 .= '
				' . $__templater->formTextBoxRow(array(
			'name' => 'username',
			'autofocus' => 'autofocus',
			'maxlength' => $__templater->func('max_length', array($__vars['xf']['visitor'], 'username', ), false),
			'required' => 'required',
		), array(
			'label' => 'Seu nome',
			'hint' => 'Obrigatório',
		)) . '

				' . $__templater->formTextBoxRow(array(
			'name' => 'email',
			'maxlength' => $__templater->func('max_length', array($__vars['xf']['visitor'], 'email', ), false),
			'type' => 'email',
			'required' => 'required',
		), array(
			'label' => 'Seu endereço de e-mail',
			'hint' => 'Obrigatório',
		)) . '
			';
	} else {
		$__compilerTemp1 .= '
				<div class="label-group--inline">
					<label>' . 'Seu nome' . '</label>
					<span>' . $__templater->escape($__vars['xf']['visitor']['username']) . '</span>
				</div>
				';
		if ($__vars['xf']['visitor']['email']) {
			$__compilerTemp1 .= '
				<div class="label-group--inline">
					<label>' . 'Seu endereço de e-mail' . '</label>
					<span>' . $__templater->escape($__vars['xf']['visitor']['email']) . '</span>
				</div>
				';
		} else {
			$__compilerTemp1 .= '

					' . $__templater->formTextBox(array(
				'name' => 'email',
				'type' => 'email',
				'required' => 'required',
			)) . '

				';
		}
		$__compilerTemp1 .= '
			';
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<div class="block-row block-body">
			' . $__compilerTemp1 . '

			' . $__templater->formRowIfContent($__templater->func('captcha', array(false)), array(
		'label' => 'Verificação',
		'hint' => 'Obrigatório',
	)) . '
			<label>' . 'Assunto' . '</label>
				<dfn class="formRow-hint">Required</dfn>
			' . $__templater->formTextBox(array(
		'name' => 'subject',
		'required' => 'required',
	)) . '
			<label>' . 'Mensagem' . '</label>
				<dfn class="formRow-hint">Required</dfn>
			' . $__templater->formTextArea(array(
		'name' => 'message',
		'rows' => '5',
		'autosize' => 'true',
		'required' => 'required',
	)) . '
			' . $__templater->formSubmitRow(array(
		'submit' => 'Enviar',
	), array(
	)) . '
		</div>
		' . $__templater->func('redirect_input', array(null, null, true)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('misc/contact', ), false),
		'class' => 'block form-inline',
		'ajax' => 'true',
		'data-force-flash-message' => 'true',
	));
	return $__finalCompiled;
});