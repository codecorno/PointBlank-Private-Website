<?php
// FROM HASH: 249ef82e1bd74b9ecce032692bfae05d
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->method($__vars['scope'], 'isInsert', array())) {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Add API scope');
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Edit API scope' . ': ' . $__templater->escape($__vars['permission']['title']));
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	if ($__templater->method($__vars['scope'], 'isUpdate', array())) {
		$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('', array(
			'href' => $__templater->func('link', array('api-scopes/delete', $__vars['scope'], ), false),
			'icon' => 'delete',
			'overlay' => 'true',
		), '', array(
		)) . '
');
	}
	$__finalCompiled .= '

' . $__templater->form('
	<div class="block-container">
		<div class="block-body">

			' . $__templater->formTextBoxRow(array(
		'name' => 'api_scope_id',
		'value' => $__vars['scope']['api_scope_id'],
		'maxlength' => $__templater->func('max_length', array($__vars['scope'], 'api_scope_id', ), false),
		'dir' => 'ltr',
	), array(
		'label' => 'Scope ID',
	)) . '

			' . $__templater->formTextBoxRow(array(
		'name' => 'description',
		'value' => ($__templater->method($__vars['scope'], 'exists', array()) ? $__vars['scope']['MasterDescription']['phrase_text'] : ''),
	), array(
		'label' => 'Description',
	)) . '

			' . $__templater->callMacro('addon_macros', 'addon_edit', array(
		'addOnId' => $__vars['scope']['addon_id'],
	), $__vars) . '
		</div>

		' . $__templater->formSubmitRow(array(
		'sticky' => 'true',
		'icon' => 'save',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('api-scopes/save', $__vars['scope'], ), false),
		'class' => 'block',
		'ajax' => 'true',
	));
	return $__finalCompiled;
});