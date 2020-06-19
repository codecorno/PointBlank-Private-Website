<?php
// FROM HASH: 7b2744e2aba934498d78b597e05ad27f
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Verificação em duas etapas');
	$__finalCompiled .= '

';
	$__templater->wrapTemplate('account_wrapper', $__vars);
	$__finalCompiled .= '

';
	if ($__vars['backupAdded']) {
		$__finalCompiled .= '
	<div class="blockMessage blockMessage--important blockMessage--iconic">
		' . 'Verification backup codes have automatically been generated. Each of these codes can be used once in case you don\'t have access to other means of verification. These codes should be saved in a secure location.' . '
		<a href="' . $__templater->func('link', array('account/two-step/manage', array('provider_id' => 'backup', ), ), true) . '">' . 'Ver seus códigos de backup.' . '</a>
		<a href="' . $__templater->func('link', array('account/two-step/backup-codes', ), true) . '" data-xf-click="overlay" data-overlay-config="' . $__templater->filter(array('backdropClose' => false, 'escapeClose' => false, ), array(array('json', array()),), true) . '" data-load-auto-click="true" style="display: none"></a>
	</div>
';
	}
	$__finalCompiled .= '

';
	$__templater->pageParams['pageDescription'] = $__templater->preEscaped('A verificação em duas etapas aumenta a segurança da sua conta, exigindo que você forneça um código adicional para concluir o processo de login. Se a sua senha for comprometida, esta verificação ajudará a impedir o acesso não autorizado à sua conta.');
	$__templater->pageParams['pageDescriptionMeta'] = true;
	$__finalCompiled .= '

<div class="block">
	<div class="block-container">
		<div class="block-body">
			';
	if ($__templater->isTraversable($__vars['providers'])) {
		foreach ($__vars['providers'] AS $__vars['provider']) {
			if ($__templater->method($__vars['provider'], 'isEnabled', array()) OR $__templater->method($__vars['provider'], 'canEnable', array())) {
				$__finalCompiled .= '
				<div class="block-row block-row--separated">
					<div class="contentRow">
						<div class="contentRow-main contentRow-main--close">
							<div class="contentRow-extra">
								';
				if ($__templater->method($__vars['provider'], 'canEnable', array())) {
					$__finalCompiled .= '
									' . $__templater->form('
										' . $__templater->button('Enable', array(
						'type' => 'submit',
					), '', array(
					)) . '
									', array(
						'action' => $__templater->func('link', array('account/two-step/enable', $__vars['provider'], ), false),
					)) . '
								';
				}
				$__finalCompiled .= '
								';
				if ($__templater->method($__vars['provider'], 'canDisable', array())) {
					$__finalCompiled .= '
									' . $__templater->button('
										' . 'Desativar' . '
									', array(
						'href' => $__templater->func('link', array('account/two-step/disable', $__vars['provider'], ), false),
						'overlay' => 'true',
					), '', array(
					)) . '
								';
				}
				$__finalCompiled .= '
								';
				if ($__templater->method($__vars['provider'], 'canManage', array())) {
					$__finalCompiled .= '
									' . $__templater->button('
										' . 'Gerenciar' . '
									', array(
						'href' => $__templater->func('link', array('account/two-step/manage', $__vars['provider'], ), false),
					), '', array(
					)) . '
								';
				}
				$__finalCompiled .= '
							</div>
							<h2 class="contentRow-title">' . $__templater->escape($__vars['provider']['title']) . '</h2>
							<div class="contentRow-minor">' . $__templater->escape($__vars['provider']['description']) . '</div>
						</div>
					</div>
				</div>
			';
			}
		}
	}
	$__finalCompiled .= '
		</div>
		';
	if ($__vars['xf']['visitor']['Option']['use_tfa']) {
		$__finalCompiled .= '
			<div class="block-footer">
				<span class="block-footer-controls">' . $__templater->button('
					' . 'Desativar a verificação em duas etapas' . '
				', array(
			'href' => $__templater->func('link', array('account/two-step/disable', ), false),
			'overlay' => 'true',
		), '', array(
		)) . '</span>
			</div>
		';
	}
	$__finalCompiled .= '
	</div>
</div>

';
	if ($__vars['currentTrustRecord'] OR $__vars['hasOtherTrusted']) {
		$__finalCompiled .= '
	<div class="block">
		<div class="block-container">
			<h2 class="block-header">' . 'Trusted devices' . '</h2>
			<div class="block-body">
				';
		if ($__vars['currentTrustRecord']) {
			$__finalCompiled .= '
					<div class="block-row block-row--separated">
						' . 'Este dispositivo é atualmente confiável até ' . $__templater->func('date', array($__vars['currentTrustRecord']['trusted_until'], ), true) . '. Você não precisará concluir a verificação em duas etapas a partir deste dispositivo até então. Você pode optar por deixar de confiar neste dispositivo para que você seja solicitado a concluir a verificação em duas etapas quando você fizer o login seguinte.' . '

						' . $__templater->form('
							' . $__templater->button('Stop trusting this device', array(
				'type' => 'submit',
			), '', array(
			)) . '
						', array(
				'action' => $__templater->func('link', array('account/two-step/trusted-disable', ), false),
			)) . '
					</div>
				';
		}
		$__finalCompiled .= '
				';
		if ($__vars['hasOtherTrusted']) {
			$__finalCompiled .= '
					<div class="block-row block-row--separated">
						' . 'Outros dispositivos são atualmente confiáveis. Você não será solicitado a concluir a verificação em duas etapas a partir desses dispositivos. Se você perdeu o acesso a um dispositivo confiável, é recomendável que você pare de confiar nesses dispositivos.' . '
						';
			if ($__vars['currentTrustRecord']) {
				$__finalCompiled .= 'This device will remain trusted.';
			}
			$__finalCompiled .= '

						' . $__templater->form('
							' . $__templater->button('Stop trusting other devices', array(
				'type' => 'submit',
			), '', array(
			)) . '
							' . $__templater->formHiddenVal('others', '1', array(
			)) . '
						', array(
				'action' => $__templater->func('link', array('account/two-step/trusted-disable', ), false),
			)) . '
					</div>
				';
		}
		$__finalCompiled .= '
			</div>
		</div>
	</div>
';
	}
	return $__finalCompiled;
});