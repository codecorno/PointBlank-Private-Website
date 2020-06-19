<?php
// FROM HASH: 709a85c26b1b393a3d8a440a61b2ca9a
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Avatar');
	$__finalCompiled .= '

';
	$__templater->includeCss('account_avatar.less');
	$__finalCompiled .= '
';
	$__templater->includeJs(array(
		'prod' => 'xf/avatar-compiled.js',
		'dev' => 'vendor/hammer/hammer.js, vendor/cropbox/jquery.cropbox.js, xf/avatar.js',
	));
	$__finalCompiled .= '

';
	$__templater->wrapTemplate('account_wrapper', $__vars);
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	if ($__vars['xf']['options']['gravatarEnable']) {
		$__compilerTemp1 .= '
				<li class="block-row block-row--separated avatarControl">
					<div class="avatarControl-preview">
						<span class="avatar avatar--m">
							<img src="' . $__templater->func('gravatar_url', array($__vars['xf']['visitor'], 'm', ), true) . '" class="js-gravatarPreview" />
						</span>
					</div>
					<div class="avatarControl-inputs">
						' . $__templater->formRadio(array(
			'name' => 'use_custom',
		), array(array(
			'value' => '0',
			'selected' => $__vars['xf']['visitor']['gravatar'],
			'label' => 'Use Gravatar',
			'_dependent' => array('
									<div class="inputGroup">
										' . $__templater->formTextBox(array(
			'name' => 'gravatar',
			'value' => ($__vars['xf']['visitor']['gravatar'] ?: $__vars['xf']['visitor']['email']),
			'type' => 'email',
			'maxlength' => $__templater->func('max_length', array($__vars['xf']['visitor'], 'gravatar', ), false),
			'class' => 'js-gravatar',
		)) . '
										<div class="inputGroup-text u-jsOnly">
											' . $__templater->button('
												' . 'Test' . '
											', array(
			'type' => 'submit',
			'name' => 'test_gravatar',
			'value' => '1',
		), '', array(
		)) . '
										</div>
									</div>
									<dfn class="inputChoices-explain">
										' . 'Enter the email address of the Gravatar you want to use.' . '
										<div><a href="https://gravatar.com" rel="nofollow" target="_blank">' . 'What\'s a Gravatar?' . '</a></div>
									</dfn>
								'),
			'_type' => 'option',
		))) . '
					</div>
				</li>
			';
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<ul class="block-body">
			<li class="block-row block-row--separated avatarControl">
				<div class="avatarControl-preview">
					<div class="avatarCropper" style="width: ' . $__templater->escape($__vars['maxSize']) . 'px; height: ' . $__templater->escape($__vars['maxSize']) . 'px;">
						' . $__templater->func('avatar', array($__vars['xf']['visitor'], 'o', false, array(
		'href' => '',
		'style' => $__vars['maxDimension'] . ': ' . $__vars['maxSize'] . 'px; left: -' . $__vars['x'] . 'px; top: -' . $__vars['y'] . 'px;',
		'data-x' => $__vars['x'],
		'data-y' => $__vars['y'],
		'data-size' => $__vars['maxSize'],
		'class' => 'js-avatar js-avatarCropper',
		'innerclass' => 'js-croppedAvatar',
		'forcetype' => 'custom',
		'data-xf-init' => 'avatar-cropper',
	))) . '
						' . $__templater->formHiddenVal('avatar_crop_x', $__vars['x'], array(
		'class' => 'js-avatarX',
	)) . '
						' . $__templater->formHiddenVal('avatar_crop_y', $__vars['y'], array(
		'class' => 'js-avatarY',
	)) . '
					</div>
				</div>
				<div class="avatarControl-inputs">
					' . $__templater->formRadio(array(
		'name' => 'use_custom',
		'id' => 'useCustom',
	), array(array(
		'value' => '1',
		'selected' => !$__vars['xf']['visitor']['gravatar'],
		'label' => 'Use a custom avatar',
		'hint' => 'Drag this image to crop it, then click <i>Okay</i> to confirm, or upload a new avatar below.',
		'_dependent' => array('
								<label>' . 'Upload new custom avatar' . $__vars['xf']['language']['label_separator'] . '</label>
								' . $__templater->formUpload(array(
		'name' => 'upload',
		'class' => 'js-uploadAvatar',
		'accept' => '.gif,.jpeg,.jpg,.jpe,.png',
	)) . '
								<dfn class="inputChoices-explain">
									' . 'It is recommended that you use an image that is at least ' . 400 . 'x' . 400 . ' pixels.' . '
								</dfn>
							'),
		'_type' => 'option',
	))) . '
				</div>
			</li>
			' . $__compilerTemp1 . '
		</ul>
		' . $__templater->formSubmitRow(array(
		'submit' => 'Okay',
		'class' => 'js-overlayClose',
	), array(
		'rowtype' => 'simple',
		'html' => '
				' . $__templater->button('', array(
		'type' => 'submit',
		'name' => 'delete_avatar',
		'value' => '1',
		'class' => 'js-deleteAvatar',
		'icon' => 'delete',
	), '', array(
	)) . '
			',
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('account/avatar', ), false),
		'upload' => 'true',
		'ajax' => 'true',
		'class' => 'block',
		'data-xf-init' => 'avatar-upload',
	));
	return $__finalCompiled;
});