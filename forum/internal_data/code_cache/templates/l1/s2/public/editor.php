<?php
// FROM HASH: 79c931cfbdba31f9b1c2b8d124bba539
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->includeCss('editor.less');
	$__finalCompiled .= '

';
	if ($__vars['fullEditorJs']) {
		$__finalCompiled .= '
	';
		$__templater->includeJs(array(
			'src' => 'vendor/froala/froala-compiled.full.js, xf/editor.js',
		));
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	';
		$__templater->includeJs(array(
			'prod' => 'xf/editor-compiled.js',
			'dev' => 'vendor/froala/froala-compiled.js, xf/editor.js',
		));
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '
' . '

';
	if ($__templater->func('is_editor_capable', array(), false)) {
		$__finalCompiled .= '

	<script class="js-editorToolbars" type="application/json">' . $__templater->filter($__vars['editorToolbars'], array(array('json', array()),array('raw', array()),), true) . '</script>
	<script class="js-editorDropdowns" type="application/json">' . $__templater->filter($__vars['editorDropdowns'], array(array('json', array()),array('raw', array()),), true) . '</script>

	<script class="js-editorLanguage" type="application/json">
		{
			"Align Center": "' . $__templater->filter('Align center', array(array('escape', array('json', )),), true) . '",
			"Align Left": "' . $__templater->filter('Align left', array(array('escape', array('json', )),), true) . '",
			"Align Right": "' . $__templater->filter('Align right', array(array('escape', array('json', )),), true) . '",
			"Align": "' . $__templater->filter('Alignment', array(array('escape', array('json', )),), true) . '",
			"Alignment": "' . $__templater->filter('Alignment', array(array('escape', array('json', )),), true) . '",
			"Back": "' . $__templater->filter('Back', array(array('escape', array('json', )),), true) . '",
			"Bold": "' . $__templater->filter('Bold', array(array('escape', array('json', )),), true) . '",
			"By URL": "' . $__templater->filter('By URL', array(array('escape', array('json', )),), true) . '",
			"Clear Formatting": "' . $__templater->filter('Remove formatting', array(array('escape', array('json', )),), true) . '",
			"Code": "' . $__templater->filter('Code', array(array('escape', array('json', )),), true) . '",
			"Colors": "' . $__templater->filter('Text color', array(array('escape', array('json', )),), true) . '",
			"Decrease Indent": "' . $__templater->filter('Outdent', array(array('escape', array('json', )),), true) . '",
			"Delete Draft": "' . $__templater->filter('Delete draft', array(array('escape', array('json', )),), true) . '",
			"Drafts": "' . $__templater->filter('Drafts', array(array('escape', array('json', )),), true) . '",
			"Drop image": "' . $__templater->filter('Drop image', array(array('escape', array('json', )),), true) . '",
			"Drop video": "' . $__templater->filter('Drop video', array(array('escape', array('json', )),), true) . '",
			"Edit Link": "' . $__templater->filter('Edit link', array(array('escape', array('json', )),), true) . '",
			"Font Family": "' . $__templater->filter('Font family', array(array('escape', array('json', )),), true) . '",
			"Font Size": "' . $__templater->filter('Font size', array(array('escape', array('json', )),), true) . '",
			"Increase Indent": "' . $__templater->filter('Indent', array(array('escape', array('json', )),), true) . '",
			"Inline Code": "' . $__templater->filter('Inline code', array(array('escape', array('json', )),), true) . '",
			"Inline Spoiler": "' . $__templater->filter('Inline spoiler', array(array('escape', array('json', )),), true) . '",
			"Insert Image": "' . $__templater->filter('Insert image', array(array('escape', array('json', )),), true) . '",
			"Insert Link": "' . $__templater->filter('Insert link', array(array('escape', array('json', )),), true) . '",
			"Insert": "' . $__templater->filter('Insert', array(array('escape', array('json', )),), true) . '",
			"Italic": "' . $__templater->filter('Italic', array(array('escape', array('json', )),), true) . '",
			"List": "' . $__templater->filter('List', array(array('escape', array('json', )),), true) . '",
			"Loading image": "' . $__templater->filter('Loading image', array(array('escape', array('json', )),), true) . '",
			"Media": "' . $__templater->filter('Media', array(array('escape', array('json', )),), true) . '",
			"Open Link": "' . $__templater->filter('Open link', array(array('escape', array('json', )),), true) . '",
			"or click": "' . $__templater->filter('Or click here', array(array('escape', array('json', )),), true) . '",
			"Ordered List": "' . $__templater->filter('Ordered list', array(array('escape', array('json', )),), true) . '",
			"Quote": "' . $__templater->filter('Quote', array(array('escape', array('json', )),), true) . '",
			"Redo": "' . $__templater->filter('Redo', array(array('escape', array('json', )),), true) . '",
			"Remove": "' . $__templater->filter('Remove', array(array('escape', array('json', )),), true) . '",
			"Replace": "' . $__templater->filter('Replace', array(array('escape', array('json', )),), true) . '",
			"Save Draft": "' . $__templater->filter('Save draft', array(array('escape', array('json', )),), true) . '",
			"Smilies": "' . $__templater->filter('Smilies', array(array('escape', array('json', )),), true) . '",
			"Something went wrong. Please try again.": "' . $__templater->filter('Something went wrong. Please try again or contact the administrator.', array(array('escape', array('json', )),), true) . '",
			"Spoiler": "' . $__templater->filter('Spoiler', array(array('escape', array('json', )),), true) . '",
			"Strikethrough": "' . $__templater->filter('Strike-through', array(array('escape', array('json', )),), true) . '",
			"Text": "' . $__templater->filter('Text', array(array('escape', array('json', )),), true) . '",
			"Toggle BB Code": "' . $__templater->filter('Toggle BB code', array(array('escape', array('json', )),), true) . '",
			"Underline": "' . $__templater->filter('Underline', array(array('escape', array('json', )),), true) . '",
			"Undo": "' . $__templater->filter('Undo', array(array('escape', array('json', )),), true) . '",
			"Unlink": "' . $__templater->filter('Unlink', array(array('escape', array('json', )),), true) . '",
			"Unordered List": "' . $__templater->filter('Unordered list', array(array('escape', array('json', )),), true) . '",
			"Update": "' . $__templater->filter('Update', array(array('escape', array('json', )),), true) . '",
			"Upload Image": "' . $__templater->filter('Upload image', array(array('escape', array('json', )),), true) . '",
			"Uploading": "' . $__templater->filter('Uploading', array(array('escape', array('json', )),), true) . '",
			"URL": "' . $__templater->filter('URL', array(array('escape', array('json', )),), true) . '",
			"Insert Table": "' . $__templater->filter('Insert table', array(array('escape', array('json', )),), true) . '",
			"Table Header": "' . $__templater->filter('Table header', array(array('escape', array('json', )),), true) . '",
			"Remove Table": "' . $__templater->filter('Remove table', array(array('escape', array('json', )),), true) . '",
			"Row": "' . $__templater->filter('Row', array(array('escape', array('json', )),), true) . '",
			"Column": "' . $__templater->filter('Column', array(array('escape', array('json', )),), true) . '",
			"Insert row above": "' . $__templater->filter('Insert row above', array(array('escape', array('json', )),), true) . '",
			"Insert row below": "' . $__templater->filter('Insert row below', array(array('escape', array('json', )),), true) . '",
			"Delete row": "' . $__templater->filter('Delete row', array(array('escape', array('json', )),), true) . '",
			"Insert column before": "' . $__templater->filter('Insert column before', array(array('escape', array('json', )),), true) . '",
			"Insert column after": "' . $__templater->filter('Insert column after', array(array('escape', array('json', )),), true) . '",
			"Delete column": "' . $__templater->filter('Delete column', array(array('escape', array('json', )),), true) . '",
			"Ctrl": "' . $__templater->filter('Ctrl', array(array('escape', array('json', )),), true) . '",
			"Shift": "' . $__templater->filter('Shift', array(array('escape', array('json', )),), true) . '",
			"Alt": "' . $__templater->filter('Alt', array(array('escape', array('json', )),), true) . '",
			"Insert Video": "' . $__templater->filter('Insert video', array(array('escape', array('json', )),), true) . '",
			"Upload Video": "' . $__templater->filter('Upload video', array(array('escape', array('json', )),), true) . '",
			"Width": "' . $__templater->filter('Width', array(array('escape', array('json', )),), true) . '",
			"Height": "' . $__templater->filter('Height', array(array('escape', array('json', )),), true) . '",
			"Change Size": "' . $__templater->filter('Change size', array(array('escape', array('json', )),), true) . '",
			"None": "' . $__templater->filter('None', array(array('escape', array('json', )),), true) . '",
			"Alternative Text": "' . $__templater->filter('Alt text', array(array('escape', array('json', )),), true) . '",
			"__lang end__": ""
		}
	</script>

	<script class="js-editorCustom" type="application/json">
		' . $__templater->filter($__vars['customIcons'], array(array('json', array()),array('raw', array()),), true) . '
	</script>

	<script class="js-xfSmilieMenu" type="text/template">
		<div class="menu menu--emoji" data-menu="menu" aria-hidden="true"
			data-href="' . $__templater->func('link', array('editor/smilies-emoji', ), true) . '"
			data-load-target=".js-xfSmilieMenuBody">
			<div class="menu-content">
				<div class="js-xfSmilieMenuBody">
					<div class="menu-row">' . 'Loading' . $__vars['xf']['language']['ellipsis'] . '</div>
				</div>
			</div>
		</div>
	</script>

	<textarea name="' . $__templater->escape($__vars['htmlName']) . '"
		class="input js-editor u-jsOnly"
		data-xf-init="editor"
		data-original-name="' . $__templater->escape($__vars['name']) . '"
		data-buttons-remove="' . $__templater->filter($__vars['removeButtons'], array(array('join', array(',', )),), true) . '"
		style="visibility: hidden; height: ' . ($__vars['height'] + 37) . 'px; ' . $__templater->escape($__vars['styleAttr']) . '"
		aria-label="' . $__templater->filter('Rich text box', array(array('for_attr', array()),), true) . '"
		' . $__templater->filter($__vars['attrsHtml'], array(array('raw', array()),), true) . '>' . $__templater->escape($__vars['htmlValue']) . '</textarea>

	' . '

	<input type="hidden" value="' . $__templater->escape($__vars['value']) . '" data-bb-code="' . $__templater->escape($__vars['name']) . '" />

	<noscript>
		<textarea name="' . $__templater->escape($__vars['name']) . '" class="input" aria-label="' . $__templater->filter('Rich text box', array(array('for_attr', array()),), true) . '">' . $__templater->escape($__vars['value']) . '</textarea>
	</noscript>

';
	} else {
		$__finalCompiled .= '

	<textarea name="' . $__templater->escape($__vars['name']) . '" class="input input--fitHeight js-editor" style="min-height: ' . $__templater->escape($__vars['height']) . 'px; ' . $__templater->escape($__vars['styleAttr']) . '" data-xf-init="textarea-handler user-mentioner emoji-completer" aria-label="' . $__templater->filter('Rich text box', array(array('for_attr', array()),), true) . '" ' . $__templater->filter($__vars['attrsHtml'], array(array('raw', array()),), true) . '>' . $__templater->escape($__vars['value']) . '</textarea>

';
	}
	$__finalCompiled .= '
';
	if ($__vars['previewable']) {
		$__finalCompiled .= '
	<div class="js-previewContainer"></div>
';
	}
	return $__finalCompiled;
});