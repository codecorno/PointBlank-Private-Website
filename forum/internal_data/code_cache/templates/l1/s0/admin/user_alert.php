<?php
// FROM HASH: 72d49e3753e5d759345dfdb2f6b8b1fe
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Alert users');
	$__finalCompiled .= '
';
	$__templater->pageParams['pageDescription'] = $__templater->preEscaped('You can use this form to send an alert to the users which match the criteria specified below.');
	$__templater->pageParams['pageDescriptionMeta'] = true;
	$__finalCompiled .= '

';
	if ($__vars['sent']) {
		$__finalCompiled .= '
	<div class="blockMessage blockMessage--success blockMessage--iconic">
		' . 'Your alert was sent to ' . $__templater->filter($__vars['sent'], array(array('number', array()),), true) . ' users.' . '
	</div>
';
	}
	$__finalCompiled .= '

' . $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formTextBoxRow(array(
		'name' => 'from_user',
		'value' => $__vars['xf']['visitor']['username'],
		'ac' => 'single',
	), array(
		'label' => 'From user',
		'explain' => 'If you would like this alert to appear from a specific user, enter their name above. If no name is specified, the alert will be sent anonymously.',
	)) . '

			<hr class="formRowSep" />

			' . $__templater->formTextBoxRow(array(
		'name' => 'link_url',
		'type' => 'url',
		'dir' => 'ltr',
	), array(
		'label' => 'Link URL',
	)) . '

			' . $__templater->formTextBoxRow(array(
		'name' => 'link_title',
	), array(
		'label' => 'Link title',
		'explain' => 'If you provide a URL, you can use it in your alert as the main pop up link. You can either insert it yourself in the alert body with <b>{link}</b> or it will be appended at the end automatically.',
	)) . '

			' . $__templater->formCodeEditorRow(array(
		'name' => 'alert_body',
		'mode' => 'html',
		'data-line-wrapping' => 'true',
		'class' => 'codeEditor--autoSize codeEditor--proportional',
	), array(
		'label' => 'Alert body',
		'hint' => 'You may use HTML',
		'explain' => 'The following placeholders will be replaced in the message: {name}, {id}, {link}.' . ' ' . 'You may also use {phrase:phrase_title} which will be replaced with the phrase text in the recipient\'s language.',
	)) . '
		</div>

		<h2 class="block-formSectionHeader"><span class="block-formSectionHeader-aligner">' . 'User criteria' . '</span></h2>
		<div class="block-body">
			' . $__templater->includeTemplate('helper_user_search_criteria', $__vars) . '
		</div>

		' . $__templater->formSubmitRow(array(
		'submit' => 'Proceed' . $__vars['xf']['language']['ellipsis'],
		'sticky' => 'true',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('users/alert/confirm', ), false),
		'class' => 'block',
	));
	return $__finalCompiled;
});