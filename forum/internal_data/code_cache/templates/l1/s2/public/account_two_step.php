<?php
// FROM HASH: 7b2744e2aba934498d78b597e05ad27f
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Two-step verification');
	$__finalCompiled .= '

';
	$__templater->wrapTemplate('account_wrapper', $__vars);
	$__finalCompiled .= '

';
	if ($__vars['backupAdded']) {
		$__finalCompiled .= '
	<div class="blockMessage blockMessage--important blockMessage--iconic">
		' . 'Verification backup codes have automatically been generated. Each of these codes can be used once in case you don\'t have access to other means of verification. These codes should be saved in a secure location.' . '
		<a href="' . $__templater->func('link', array('account/two-step/manage', array('provider_id' => 'backup', ), ), true) . '">' . 'View your backup codes.' . '</a>
		<a href="' . $__templater->func('link', array('account/two-step/backup-codes', ), true) . '" data-xf-click="overlay" data-overlay-config="' . $__templater->filter(array('backdropClose' => false, 'escapeClose' => false, ), array(array('json', array()),), true) . '" data-load-auto-click="true" style="display: none"></a>
	</div>
';
	}
	$__finalCompiled .= '

';
	$__templater->pageParams['pageDescription'] = $__templater->preEscaped('Two-step verification increases the security of your account by requiring you to provide an additional code to complete the login process. If your password is ever compromised, this verification will help prevent unauthorized access to your account.');
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
										' . 'Disable' . '
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
										' . 'Manage' . '
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
					' . 'Disable two-step verification' . '
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
						' . 'This device is currently trusted until ' . $__templater->func('date', array($__vars['currentTrustRecord']['trusted_until'], ), true) . '. You will not need to complete two-step verification from this device until then. You may choose to stop trusting this device so that you will be prompted to complete two-step verification when you next log in.' . '

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
						' . 'Other devices are currently trusted. You will not be prompted to complete two-step verification from these devices. If you have lost access to a trusted device, it is recommended that you stop trusting these devices.' . '
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