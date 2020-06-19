<?php
// FROM HASH: 74c9b16fa701b3b301349470c49a3363
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Confirm sending alert');
	$__finalCompiled .= '

' . $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formRow('
				' . $__templater->filter($__vars['total'], array(array('number', array()),), true) . '
				<span role="presentation" aria-hidden="true">&middot;</span>
				<a href="' . $__templater->func('link', array('users/list', null, array('criteria' => $__vars['criteria'], ), ), true) . '">' . 'View full list' . '</a>
			', array(
		'label' => 'Number of users matching criteria',
	)) . '
			' . $__templater->formRow('
				' . $__templater->button('Send test', array(
		'type' => 'submit',
		'name' => 'test',
		'value' => '1',
	), '', array(
	)) . '
			', array(
		'label' => 'Test',
	)) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'submit' => 'Send',
	), array(
	)) . '
	</div>

	' . $__templater->formHiddenVal('json_criteria', $__templater->filter($__vars['criteria'], array(array('json', array()),), false), array(
	)) . '

	' . $__templater->formHiddenVal('total', $__vars['total'], array(
	)) . '

	' . $__templater->formHiddenVal('from_user', $__vars['user']['username'], array(
	)) . '

	' . $__templater->formHiddenVal('link_url', $__vars['alert']['link_url'], array(
	)) . '
	' . $__templater->formHiddenVal('link_title', $__vars['alert']['link_title'], array(
	)) . '
	' . $__templater->formHiddenVal('alert_body', $__vars['alert']['alert_body'], array(
	)) . '
', array(
		'action' => $__templater->func('link', array('users/alert/send', ), false),
		'class' => 'block',
	));
	return $__finalCompiled;
});