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
			'label' => 'Your name',
			'hint' => 'Required',
		)) . '

				' . $__templater->formTextBoxRow(array(
			'name' => 'email',
			'maxlength' => $__templater->func('max_length', array($__vars['xf']['visitor'], 'email', ), false),
			'type' => 'email',
			'required' => 'required',
		), array(
			'label' => 'Your email address',
			'hint' => 'Required',
		)) . '
			';
	} else {
		$__compilerTemp1 .= '
				<div class="label-group--inline">
					<label>' . 'Your name' . '</label>
					<span>' . $__templater->escape($__vars['xf']['visitor']['username']) . '</span>
				</div>
				';
		if ($__vars['xf']['visitor']['email']) {
			$__compilerTemp1 .= '
				<div class="label-group--inline">
					<label>' . 'Your email address' . '</label>
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
		'label' => 'Verification',
		'hint' => 'Required',
	)) . '
			<label>' . 'Subject' . '</label>
				<dfn class="formRow-hint">Required</dfn>
			' . $__templater->formTextBox(array(
		'name' => 'subject',
		'required' => 'required',
	)) . '
			<label>' . 'Message' . '</label>
				<dfn class="formRow-hint">Required</dfn>
			' . $__templater->formTextArea(array(
		'name' => 'message',
		'rows' => '5',
		'autosize' => 'true',
		'required' => 'required',
	)) . '
			' . $__templater->formSubmitRow(array(
		'submit' => 'Send',
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