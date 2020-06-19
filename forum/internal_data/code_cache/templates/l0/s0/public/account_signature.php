<?php
// FROM HASH: 6469bdf8386a54275f24464cf6a39f74
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Edit signature');
	$__finalCompiled .= '

';
	$__templater->wrapTemplate('account_wrapper', $__vars);
	$__finalCompiled .= '

' . $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formEditorRow(array(
		'name' => 'signature',
		'value' => $__vars['xf']['visitor']['Profile']['signature_'],
		'removebuttons' => $__vars['disabledButtons'],
	), array(
		'rowtype' => 'fullWidth noLabel',
		'label' => 'Signature',
	)) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'icon' => 'save',
	), array(
		'rowtype' => 'simple',
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('account/signature', ), false),
		'ajax' => 'true',
		'class' => 'block',
		'data-redirect' => 'off',
	));
	return $__finalCompiled;
});