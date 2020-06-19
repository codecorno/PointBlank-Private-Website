<?php
// FROM HASH: 169626308cbf7f702be43b88efcb2c62
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Export a user');
	$__finalCompiled .= '

' . $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formInfoRow('
				' . 'This page will allow you to download a user\'s standard personal information in XML format for the purposes of transferring that data to another system.' . '
			', array(
	)) . '
			' . $__templater->formTextBoxRow(array(
		'name' => 'username',
		'value' => $__vars['user']['username'],
		'required' => 'required',
		'ac' => 'single',
	), array(
		'label' => 'User name',
	)) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'icon' => 'export',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('data-portability/export', ), false),
		'class' => 'block',
	));
	return $__finalCompiled;
});