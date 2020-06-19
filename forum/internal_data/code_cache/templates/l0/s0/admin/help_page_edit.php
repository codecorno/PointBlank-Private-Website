<?php
// FROM HASH: 546d238a133701b0c16e307ac181981b
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->method($__vars['page'], 'isInsert', array())) {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Add help page');
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Edit help page' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['page']['title']));
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	if ($__templater->method($__vars['page'], 'isUpdate', array()) AND $__templater->method($__vars['page'], 'canEdit', array())) {
		$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('', array(
			'href' => $__templater->func('link', array('help-pages/delete', $__vars['page'], ), false),
			'icon' => 'delete',
			'overlay' => 'true',
		), '', array(
		)) . '
');
	}
	$__finalCompiled .= '

';
	if (!$__templater->method($__vars['page'], 'canEdit', array())) {
		$__finalCompiled .= '
	<div class="blockMessage blockMessage--important blockMessage--iconic">
		' . 'Only a limited number of fields in this item may be edited.' . '
	</div>
';
	}
	$__finalCompiled .= '

' . $__templater->form('
	<div class="block-container">
		<div class="block-body">

			' . $__templater->formTextBoxRow(array(
		'name' => 'page_name',
		'value' => $__vars['page']['page_name'],
		'readonly' => (!$__templater->method($__vars['page'], 'canEdit', array())),
		'maxlength' => $__templater->func('max_length', array($__vars['page'], 'page_name', ), false),
	), array(
		'label' => 'URL portion',
		'explain' => '
					' . 'This represents the portion of the URL after the <i>help/</i> part that identifies this page uniquely. You may use a-z, 0-9, _ and - characters only.' . '
				',
	)) . '

			' . $__templater->formHiddenVal('page_id', $__vars['page']['page_id'], array(
	)) . '

			' . $__templater->formTextBoxRow(array(
		'name' => 'title',
		'value' => ($__templater->method($__vars['page'], 'exists', array()) ? $__vars['page']['MasterTitle']['phrase_text'] : ''),
		'readonly' => (!$__templater->method($__vars['page'], 'canEdit', array())),
	), array(
		'label' => 'Title',
	)) . '

			' . $__templater->formTextBoxRow(array(
		'name' => 'description',
		'value' => ($__templater->method($__vars['page'], 'exists', array()) ? $__vars['page']['MasterDescription']['phrase_text'] : ''),
		'readonly' => (!$__templater->method($__vars['page'], 'canEdit', array())),
	), array(
		'label' => 'Description',
	)) . '

			' . $__templater->callMacro('display_order_macros', 'row', array(
		'value' => $__vars['page']['display_order'],
		'explain' => (($__vars['xf']['development'] AND $__vars['page']['addon_id']) ? 'The value of this field will not be changed when this add-on is upgraded.' : ''),
	), $__vars) . '

			' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'advanced_mode',
		'value' => '1',
		'selected' => $__vars['page']['advanced_mode'],
		'readonly' => (!$__templater->method($__vars['page'], 'canEdit', array())),
		'label' => 'Advanced mode',
		'hint' => 'If enabled, the HTML for your page will not be contained within a block.',
		'_type' => 'option',
	)), array(
	)) . '

			' . $__templater->formCodeEditorRow(array(
		'name' => 'content',
		'value' => ($__templater->method($__vars['page'], 'isUpdate', array()) ? $__vars['page']['MasterTemplate']['template'] : ''),
		'mode' => 'html',
		'class' => 'codeEditor--short',
		'readonly' => (!$__templater->method($__vars['page'], 'canEdit', array())),
	), array(
		'label' => 'Page content',
		'explain' => 'You may use XenForo template syntax here.',
	)) . '

			' . $__templater->formRow('
				' . $__templater->callMacro('helper_callback_fields', 'callback_fields', array(
		'data' => $__vars['page'],
		'readOnly' => (!$__templater->method($__vars['page'], 'canEdit', array())),
	), $__vars) . '
			', array(
		'rowtype' => 'input',
		'label' => 'PHP callback',
		'explain' => 'You may optionally specify a PHP callback here in order to fetch more data or alter the controller response for your page.<br />
<br />
Callback arguments:
<ol>
	<li><code>\\XF\\Pub\\Controller\\AbstractController $controller</code><br />The controller instance. From this you can inspect the request, response etc.</li>
	<li><code>\\XF\\Mvc\\Reply\\AbstractReply &$reply</code><br />The standard reply from the page controller.</li>
</ol>',
	)) . '

			' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'active',
		'value' => '1',
		'selected' => $__vars['page']['active'],
		'hint' => (($__vars['xf']['development'] AND $__vars['page']['addon_id']) ? 'The value of this field will not be changed when this add-on is upgraded.' : ''),
		'label' => '
					' . 'Enabled' . '
				',
		'_type' => 'option',
	)), array(
	)) . '

			' . $__templater->callMacro('addon_macros', 'addon_edit', array(
		'addOnId' => $__vars['page']['addon_id'],
	), $__vars) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'sticky' => 'true',
		'icon' => 'save',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('help-pages/save', $__vars['page'], ), false),
		'ajax' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});