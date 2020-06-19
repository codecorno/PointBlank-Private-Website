<?php
// FROM HASH: 3f31443eda0c629ff6fcf8d43756cd02
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Password confirmation');
	$__finalCompiled .= '

' . $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formInfoRow('
				' . 'To access this page, you must first confirm your password.' . '
			', array(
		'rowtype' => 'confirm',
	)) . '

			' . $__templater->formRow($__templater->escape($__vars['xf']['visitor']['username']), array(
		'label' => 'User name',
	)) . '

			' . $__templater->formPasswordBoxRow(array(
		'name' => 'password',
	), array(
		'label' => 'Password',
	)) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'submit' => 'Confirm',
	), array(
	)) . '
	</div>
	' . $__templater->func('redirect_input', array(($__vars['redirect'] ?: $__vars['xf']['uri']), null, true)) . '
', array(
		'action' => $__templater->func('link', array('login/password-confirm', ), false),
		'class' => 'block',
		'ajax' => 'true',
	));
	return $__finalCompiled;
});