<?php
// FROM HASH: c2236c12c0119d135aec610ec7240881
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Post thread');
	$__finalCompiled .= '

';
	$__templater->setPageParam('head.' . 'metaNoindex', $__templater->preEscaped('<meta name="robots" content="noindex" />'));
	$__finalCompiled .= '

';
	$__templater->breadcrumbs($__templater->method($__vars['forum'], 'getBreadcrumbs', array()));
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	if ($__vars['attachmentData']) {
		$__compilerTemp1 .= '
						' . $__templater->callMacro('helper_attach_upload', 'upload_block', array(
			'attachmentData' => $__vars['attachmentData'],
			'forceHash' => $__vars['forum']['draft_thread']['attachment_hash'],
		), $__vars) . '
					';
	}
	$__compilerTemp2 = '';
	if ($__vars['xf']['options']['multiQuote']) {
		$__compilerTemp2 .= '
						' . $__templater->callMacro('multi_quote_macros', 'button', array(
			'href' => $__templater->func('link', array('threads/multi-quote', $__vars['thread'], ), false),
			'messageSelector' => '.js-post',
			'storageKey' => 'multiQuoteThread',
		), $__vars) . '
					';
	}
	$__compilerTemp3 = '';
	$__compilerTemp4 = '';
	$__compilerTemp4 .= '
						' . $__templater->callMacro('custom_fields_macros', 'custom_fields_edit', array(
		'type' => 'threads',
		'set' => $__vars['thread']['custom_fields'],
		'editMode' => $__templater->method($__vars['thread'], 'getFieldEditMode', array()),
		'onlyInclude' => $__vars['forum']['field_cache'],
		'requiredOnly' => ($__vars['inlineMode'] ? true : false),
	), $__vars) . '
					';
	if (strlen(trim($__compilerTemp4)) > 0) {
		$__compilerTemp3 .= '
					<hr class="formRowSep" />
					' . $__compilerTemp4 . '
				';
	}
	$__compilerTemp5 = '';
	if ($__templater->method($__vars['forum'], 'canEditTags', array())) {
		$__compilerTemp5 .= '
					<hr class="formRowSep" />
					';
		$__compilerTemp6 = '';
		if ($__vars['forum']['min_tags']) {
			$__compilerTemp6 .= '
								' . 'This content must have at least ' . $__templater->escape($__vars['forum']['min_tags']) . ' tag(s).' . '
							';
		}
		$__compilerTemp5 .= $__templater->formTokenInputRow(array(
			'name' => 'tags',
			'value' => ($__vars['thread']['tags'] ? $__templater->filter($__vars['thread']['tags'], array(array('join', array(', ', )),), false) : $__vars['forum']['draft_thread']['tags']),
			'href' => $__templater->func('link', array('misc/tag-auto-complete', ), false),
			'min-length' => $__vars['xf']['options']['tagLength']['min'],
			'max-length' => $__vars['xf']['options']['tagLength']['max'],
			'max-tokens' => $__vars['xf']['options']['maxContentTags'],
		), array(
			'label' => 'Tags',
			'explain' => '
							' . 'Multiple tags may be separated by commas.' . '
							' . $__compilerTemp6 . '
						',
		)) . '
				';
	}
	$__compilerTemp7 = '';
	if (!$__vars['xf']['visitor']['user_id']) {
		$__compilerTemp7 .= '
					<hr class="formRowSep" />
					' . $__templater->formTextBoxRow(array(
			'name' => '_xfUsername',
			'data-xf-init' => 'guest-username',
			'maxlength' => $__templater->func('max_length', array($__vars['xf']['visitor'], 'username', ), false),
		), array(
			'label' => 'Name',
		)) . '

					' . $__templater->formRowIfContent($__templater->func('captcha', array(false)), array(
			'label' => 'Verification',
		)) . '
				';
	} else {
		$__compilerTemp7 .= '
					<hr class="formRowSep" />
					' . $__templater->callMacro('helper_thread_options', 'watch_input', array(
			'thread' => $__vars['thread'],
		), $__vars) . '
					' . $__templater->callMacro('helper_thread_options', 'thread_status', array(
			'thread' => $__vars['thread'],
		), $__vars) . '
				';
	}
	$__compilerTemp8 = '';
	if ($__templater->method($__vars['forum'], 'canCreatePoll', array())) {
		$__compilerTemp8 .= '
			<h2 class="block-formSectionHeader">
				<span class="collapseTrigger collapseTrigger--block' . ($__vars['forum']['draft_thread']['poll'] ? ' is-active' : '') . '" data-xf-click="toggle" data-target="< :up :next">
					<span class="block-formSectionHeader-aligner">' . 'Post a poll' . '</span>
				</span>
			</h2>
			<div class="block-body block-body--collapsible' . ($__vars['forum']['draft_thread']['poll'] ? ' is-active' : '') . '">
				' . $__templater->callMacro('poll_macros', 'add_edit_inputs', array(
			'draft' => $__vars['forum']['draft_thread']['poll'],
		), $__vars) . '
			</div>
		';
	}
	$__finalCompiled .= $__templater->form('

	<div class="block-container">
		<div class="block-body">

			' . $__templater->formPrefixInputRow($__vars['prefixes'], array(
		'type' => 'thread',
		'prefix-value' => ($__vars['forum']['draft_thread']['prefix_id'] ?: ($__vars['thread']['prefix_id'] ?: $__vars['forum']['default_prefix_id'])),
		'textbox-value' => (($__vars['title'] ?: $__vars['thread']['title']) ?: $__vars['forum']['draft_thread']['title']),
		'textbox-class' => 'input--title',
		'placeholder' => $__vars['forum']['thread_prompt'],
		'autofocus' => 'autofocus',
		'maxlength' => $__templater->func('max_length', array('XF:Thread', 'title', ), false),
	), array(
		'label' => 'Title',
		'rowtype' => 'fullWidth noLabel',
	)) . '

			<div class="js-inlineNewPostFields">
				' . $__templater->formEditorRow(array(
		'name' => 'message',
		'value' => ($__vars['post']['message'] ?: $__vars['forum']['draft_thread']['message']),
		'attachments' => ($__vars['attachmentData'] ? $__vars['attachmentData']['attachments'] : array()),
	), array(
		'rowtype' => 'fullWidth noLabel mergePrev',
		'label' => 'Message',
	)) . '

				' . $__templater->formRow('
					' . $__compilerTemp1 . '

					' . $__compilerTemp2 . '

					' . $__templater->button('', array(
		'class' => 'button--link u-jsOnly',
		'data-xf-click' => 'preview-click',
		'icon' => 'preview',
	), '', array(
	)) . '
				', array(
	)) . '

				' . $__compilerTemp3 . '

				' . $__compilerTemp5 . '

				' . $__compilerTemp7 . '
			</div>
		</div>

		' . $__compilerTemp8 . '

		' . $__templater->formSubmitRow(array(
		'submit' => 'Post thread',
		'icon' => 'write',
		'sticky' => 'true',
	), array(
		'html' => '
				' . $__templater->button('', array(
		'class' => 'u-jsOnly',
		'data-xf-click' => 'preview-click',
		'icon' => 'preview',
	), '', array(
	)) . '
			',
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('forums/post-thread', $__vars['forum'], ), false),
		'ajax' => 'true',
		'class' => 'block',
		'data-xf-init' => 'attachment-manager',
		'draft' => $__templater->func('link', array('forums/draft', $__vars['forum'], ), false),
		'data-preview-url' => $__templater->func('link', array('forums/thread-preview', $__vars['forum'], ), false),
	));
	return $__finalCompiled;
});