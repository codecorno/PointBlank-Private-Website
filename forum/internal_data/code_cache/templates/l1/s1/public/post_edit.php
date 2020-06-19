<?php
// FROM HASH: eebef0f22ad142e92069a34203af0e9a
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Edit post');
	$__finalCompiled .= '

';
	$__templater->breadcrumbs($__templater->method($__vars['thread'], 'getBreadcrumbs', array()));
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	if ($__templater->method($__vars['post'], 'isFirstPost', array()) AND $__templater->method($__vars['thread'], 'canEdit', array())) {
		$__compilerTemp1 .= '
				' . $__templater->formPrefixInputRow($__vars['prefixes'], array(
			'type' => 'thread',
			'prefix-value' => $__vars['thread']['prefix_id'],
			'textbox-value' => $__vars['thread']['title'],
			'placeholder' => 'Title' . $__vars['xf']['language']['ellipsis'],
			'maxlength' => $__templater->func('max_length', array($__vars['thread'], 'title', ), false),
		), array(
			'label' => 'Title',
			'rowtype' => ($__vars['quickEdit'] ? 'fullWidth' : ''),
		)) . '
			';
	}
	$__compilerTemp2 = '';
	if ($__vars['attachmentData']) {
		$__compilerTemp2 .= '
					' . $__templater->callMacro('helper_attach_upload', 'upload_block', array(
			'attachmentData' => $__vars['attachmentData'],
		), $__vars) . '
				';
	}
	$__compilerTemp3 = '';
	if ($__templater->method($__vars['post'], 'isFirstPost', array()) AND $__templater->method($__vars['thread'], 'canEdit', array())) {
		$__compilerTemp3 .= '
				' . $__templater->callMacro('custom_fields_macros', 'custom_fields_edit', array(
			'type' => 'threads',
			'set' => $__vars['thread']['custom_fields'],
			'editMode' => $__templater->method($__vars['thread'], 'getFieldEditMode', array()),
			'onlyInclude' => $__vars['forum']['field_cache'],
			'rowType' => ($__vars['quickEdit'] ? 'fullWidth' : ''),
		), $__vars) . '
			';
	}
	$__compilerTemp4 = '';
	if ($__templater->method($__vars['post'], 'canEditSilently', array())) {
		$__compilerTemp4 .= '
				' . $__templater->formRow('
					' . $__templater->callMacro('helper_action', 'edit_type', array(
			'canEditSilently' => $__templater->method($__vars['post'], 'canEditSilently', array()),
		), $__vars) . '
				', array(
			'rowtype' => ($__vars['quickEdit'] ? 'fullWidth noLabel' : ''),
		)) . '
			';
	}
	$__compilerTemp5 = '';
	if ($__templater->method($__vars['post'], 'canSendModeratorActionAlert', array())) {
		$__compilerTemp5 .= '
				' . $__templater->formRow('
					' . $__templater->callMacro('helper_action', 'author_alert', array(
			'row' => false,
		), $__vars) . '
				', array(
			'rowtype' => ($__vars['quickEdit'] ? 'fullWidth noLabel' : ''),
		)) . '
			';
	}
	$__compilerTemp6 = '';
	if ($__vars['quickEdit']) {
		$__compilerTemp6 .= '
					' . $__templater->button('Cancel', array(
			'class' => 'js-cancelButton',
		), '', array(
		)) . '
				';
	}
	$__finalCompiled .= $__templater->form('

	<div class="block-container">
		<div class="block-body">
			<span class="u-anchorTarget js-editContainer"></span>
			' . $__compilerTemp1 . '

			' . $__templater->formEditorRow(array(
		'name' => 'message',
		'value' => $__vars['post']['message'],
		'attachments' => $__vars['attachmentData']['attachments'],
		'data-min-height' => '100',
	), array(
		'rowtype' => ($__vars['quickEdit'] ? 'fullWidth noLabel' : ''),
		'label' => 'Message',
	)) . '

			' . $__templater->formRow('
				' . $__compilerTemp2 . '
				' . $__templater->button('', array(
		'class' => 'button--link u-jsOnly',
		'data-xf-click' => 'preview-click',
		'icon' => 'preview',
	), '', array(
	)) . '
			', array(
		'rowtype' => ($__vars['quickEdit'] ? 'fullWidth noLabel' : ''),
	)) . '

			' . $__compilerTemp3 . '

			' . $__compilerTemp4 . '

			' . $__compilerTemp5 . '
		</div>
		' . $__templater->formSubmitRow(array(
		'icon' => 'save',
		'sticky' => 'true',
	), array(
		'rowtype' => ($__vars['quickEdit'] ? 'simple' : ''),
		'html' => '
				' . $__templater->button('', array(
		'class' => 'u-jsOnly',
		'data-xf-click' => 'preview-click',
		'icon' => 'preview',
	), '', array(
	)) . '
				' . $__compilerTemp6 . '
			',
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('posts/edit', $__vars['post'], ), false),
		'ajax' => 'true',
		'class' => 'block',
		'data-xf-init' => 'attachment-manager' . (($__templater->method($__vars['post'], 'isFirstPost', array()) AND $__templater->method($__vars['thread'], 'canEdit', array())) ? ' post-edit' : ''),
		'data-preview-url' => $__templater->func('link', array('posts/preview', $__vars['post'], ), false),
	));
	return $__finalCompiled;
});