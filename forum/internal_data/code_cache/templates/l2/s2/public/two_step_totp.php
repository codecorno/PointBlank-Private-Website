<?php
// FROM HASH: d52ec7d001f642bdcb337054a638d4fa
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__vars['context'] == 'setup') {
		$__finalCompiled .= '
	';
		$__templater->includeJs(array(
			'src' => 'vendor/qrcode/jquery-qrcode.min.js',
		));
		$__finalCompiled .= '
	' . $__templater->formRow('
		' . 'To receive verification codes via a phone app, you must first install a code-generating app such as <a href="https://www.authy.com" target="_blank">Authy</a> or <a href="https://support.google.com/accounts/answer/1066447?hl=en" target="_blank">Google Authenticator</a> on your phone.<br />
			<br />
			Once you have done this, you will need to scan the QR code below into the app and enter the generated code below to confirm.' . '
		<div style="text-align: center"><span id="js-totpQrCode" style="display: inline-block; background: white; padding: 12px"></span></div>
		' . 'Alternatively, you may enter the secret into the app directly: ' . $__templater->escape($__vars['secret']) . '' . '
	', array(
			'label' => 'Configuração',
		)) . '
	';
		$__templater->inlineJs('
	jQuery(function($)
	{
		var $el = $(\'#js-totpQrCode\');
		$el.qrcode({
			text: \'' . $__templater->filter($__vars['otpUrl'], array(array('escape', array('js', )),), false) . '\'
		});
	});
	');
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	' . $__templater->formInfoRow('Introduza o código de verificação gerado pela aplicação no seu celular.', array(
		)) . '
';
	}
	$__finalCompiled .= '

' . $__templater->formTextBoxRow(array(
		'name' => 'code',
		'autofocus' => 'autofocus',
		'inputmode' => 'numeric',
		'pattern' => '[0-9]*',
	), array(
		'label' => 'Código de verificação',
	));
	return $__finalCompiled;
});