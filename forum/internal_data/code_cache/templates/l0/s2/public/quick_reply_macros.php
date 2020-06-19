<?php
// FROM HASH: 17ee387f64f40d63b35584b261f4b879
return array('macros' => array('body' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'message' => '',
		'attachmentData' => null,
		'forceHash' => '',
		'messageSelector' => '',
		'multiQuoteHref' => '',
		'multiQuoteStorageKey' => '',
		'simple' => false,
		'showPreviewButton' => true,
		'submitText' => '',
		'lastDate' => '0',
		'lastKnownDate' => '0',
		'simpleSubmit' => false,
		'minHeight' => '100',
		'placeholder' => 'Write your reply...',
		'deferred' => false,
	), $__arguments, $__vars);
	$__finalCompiled .= '

	';
	$__templater->includeCss('message.less');
	$__finalCompiled .= '
	';
	$__vars['sticky'] = $__templater->func('property', array('messageSticky', ), false);
	$__finalCompiled .= '

	<div class="message message--quickReply block-topRadiusContent block-bottomRadiusContent' . ($__vars['simple'] ? ' message--simple' : '') . '">
		<div class="message-inner">
			<div class="message-cell message-cell--user">
				<div class="message-user ' . ($__vars['sticky']['user_info'] ? 'is-sticky' : '') . '">
					<div class="message-avatar">
						<div class="message-avatar-wrapper">
							';
	$__vars['user'] = ($__vars['xf']['visitor']['user_id'] ? $__vars['xf']['visitor'] : null);
	$__finalCompiled .= '
							' . $__templater->func('avatar', array($__vars['user'], ($__vars['simple'] ? 's' : 'm'), false, array(
		'defaultname' => '',
	))) . '
						</div>
					</div>
					<span class="message-userArrow"></span>
				</div>
			</div>
			<div class="message-cell message-cell--main">
				<div class="message-editorWrapper">
					';
	$__vars['editorHtml'] = $__templater->preEscaped('
						' . $__templater->callMacro(null, 'editor', array(
		'message' => $__vars['message'],
		'attachmentData' => $__vars['attachmentData'],
		'forceHash' => $__vars['forceHash'],
		'messageSelector' => $__vars['messageSelector'],
		'multiQuoteHref' => $__vars['multiQuoteHref'],
		'multiQuoteStorageKey' => $__vars['multiQuoteStorageKey'],
		'minHeight' => $__vars['minHeight'],
		'placeholder' => $__vars['placeholder'],
		'showPreviewButton' => $__vars['showPreviewButton'],
		'submitText' => $__vars['submitText'],
		'deferred' => $__vars['deferred'],
		'lastDate' => $__vars['lastDate'],
		'lastKnownDate' => $__vars['lastKnownDate'],
		'simpleSubmit' => $__vars['simpleSubmit'],
	), $__vars) . '
					');
	$__finalCompiled .= '

					';
	if ($__vars['deferred']) {
		$__finalCompiled .= '
						<div class="editorPlaceholder" data-xf-click="editor-placeholder">
							<div class="editorPlaceholder-editor is-hidden">' . $__templater->filter($__vars['editorHtml'], array(array('raw', array()),), true) . '</div>
							<div class="editorPlaceholder-placeholder">
								<div class="input"><span class="u-muted"> ' . $__templater->escape($__vars['placeholder']) . '</span></div>
							</div>
						</div>
					';
	} else {
		$__finalCompiled .= '
						' . $__templater->filter($__vars['editorHtml'], array(array('raw', array()),), true) . '
					';
	}
	$__finalCompiled .= '
				</div>
			</div>
		</div>
	</div>
';
	return $__finalCompiled;
},
'editor' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'message' => '',
		'attachmentData' => null,
		'forceHash' => '',
		'messageSelector' => '',
		'multiQuoteHref' => '',
		'multiQuoteStorageKey' => '',
		'minHeight' => '100',
		'placeholder' => '',
		'showPreviewButton' => false,
		'submitText' => '',
		'deferred' => false,
		'lastDate' => '0',
		'lastKnownDate' => '0',
		'simpleSubmit' => false,
	), $__arguments, $__vars);
	$__finalCompiled .= '
	' . $__templater->formEditor(array(
		'name' => 'message',
		'value' => $__vars['message'],
		'attachments' => ($__vars['attachmentData'] ? $__vars['attachmentData']['attachments'] : array()),
		'data-min-height' => $__vars['minHeight'],
		'placeholder' => $__vars['placeholder'],
		'previewable' => '0',
		'data-deferred' => ($__vars['deferred'] ? 'on' : 'off'),
		'data-xf-key' => 'r',
	)) . '

	';
	if (!$__vars['xf']['visitor']['user_id']) {
		$__finalCompiled .= '
		' . $__templater->formTextBoxRow(array(
			'name' => '_xfUsername',
			'data-xf-init' => 'guest-username',
			'maxlength' => $__templater->func('max_length', array($__vars['xf']['visitor'], 'username', ), false),
		), array(
			'rowtype' => 'fullWidth noGutter',
			'label' => 'Name',
		)) . '

		';
		if ($__templater->method($__vars['xf']['visitor'], 'isShownCaptcha', array())) {
			$__finalCompiled .= '
			<div class="js-captchaContainer" data-row-type="fullWidth noGutter"></div>
			<noscript>' . $__templater->formHiddenVal('no_captcha', '1', array(
			)) . '</noscript>
		';
		}
		$__finalCompiled .= '
	';
	}
	$__finalCompiled .= '

	';
	if ($__vars['attachmentData']) {
		$__finalCompiled .= '
		' . $__templater->callMacro('helper_attach_upload', 'uploaded_files_list', array(
			'attachments' => $__vars['attachmentData']['attachments'],
			'listClass' => 'attachUploadList--spaced',
		), $__vars) . '
	';
	}
	$__finalCompiled .= '

	';
	if ($__vars['showPreviewButton']) {
		$__finalCompiled .= '
		<div class="js-previewContainer"></div>
	';
	}
	$__finalCompiled .= '

	<div class="formButtonGroup ' . ($__vars['simpleSubmit'] ? 'formButtonGroup--simple' : '') . '">
		<div class="formButtonGroup-primary">
			' . $__templater->button('
				' . ($__templater->escape($__vars['submitText']) ?: 'Post reply') . '
			', array(
		'type' => 'submit',
		'class' => 'button--primary',
		'icon' => 'reply',
	), '', array(
	)) . '
			';
	if ($__vars['showPreviewButton']) {
		$__finalCompiled .= '
				' . $__templater->button('', array(
			'class' => 'u-jsOnly',
			'data-xf-click' => 'preview-click',
			'icon' => 'preview',
		), '', array(
		)) . '
			';
	}
	$__finalCompiled .= '
		</div>
		';
	$__compilerTemp1 = '';
	$__compilerTemp1 .= '
					';
	if ($__vars['attachmentData']) {
		$__compilerTemp1 .= '
						' . $__templater->callMacro('helper_attach_upload', 'upload_link_from_data', array(
			'attachmentData' => $__vars['attachmentData'],
			'forceHash' => $__vars['forceHash'],
		), $__vars) . '
					';
	}
	$__compilerTemp1 .= '
					';
	if ($__vars['xf']['options']['multiQuote'] AND $__vars['multiQuoteHref']) {
		$__compilerTemp1 .= '
						' . $__templater->callMacro('multi_quote_macros', 'button', array(
			'href' => $__vars['multiQuoteHref'],
			'messageSelector' => $__vars['messageSelector'],
			'storageKey' => $__vars['multiQuoteStorageKey'],
		), $__vars) . '
					';
	}
	$__compilerTemp1 .= '
				';
	if (strlen(trim($__compilerTemp1)) > 0) {
		$__finalCompiled .= '
			<div class="formButtonGroup-extra">
				' . $__compilerTemp1 . '
			</div>
		';
	}
	$__finalCompiled .= '
		' . $__templater->formHiddenVal('last_date', $__vars['lastDate'], array(
		'autocomplete' => 'off',
	)) . '
		' . $__templater->formHiddenVal('last_known_date', $__vars['lastKnownDate'], array(
		'autocomplete' => 'off',
	)) . '
	</div>
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '

' . '
';
	return $__finalCompiled;
});