<?php
// FROM HASH: a7a411477cf91df98c81ab210a165420
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Two-step verification required');
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	if ($__templater->isTraversable($__vars['providers'])) {
		foreach ($__vars['providers'] AS $__vars['tabProvider']) {
			$__compilerTemp1 .= '
				<a href="' . $__templater->func('link', array('login/two-step', null, array('provider' => $__vars['tabProvider']['provider_id'], 'remember' => ($__vars['remember'] ? 1 : null), '_xfRedirect' => $__vars['redirect'], ), ), true) . '" class="tabs-tab ' . (($__vars['tabProvider']['provider_id'] == $__vars['providerId']) ? 'is-active' : '') . '">' . $__templater->escape($__vars['tabProvider']['title']) . '</a>
			';
		}
	}
	$__finalCompiled .= $__templater->form('

	<div class="block-container">
		<h2 class="block-tabHeader tabs hScroller" data-xf-init="h-scroller">
			<span class="hScroller-scroll">
			' . $__compilerTemp1 . '
			</span>
		</h2>

		<div class="block-body">
			' . $__templater->formRow($__templater->escape($__vars['user']['username']), array(
		'label' => 'Logging in as',
	)) . '

			' . $__templater->filter($__templater->method($__vars['provider'], 'render', array('login', $__vars['user'], $__vars['providerData'], $__vars['triggerData'], )), array(array('raw', array()),), true) . '

			' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'trust',
		'value' => '1',
		'checked' => $__vars['trustChecked'],
		'label' => 'Trust this device for 30 days',
		'hint' => 'If selected, you will not need to complete two-step verification from this device for the next 30 days.',
		'_type' => 'option',
	)), array(
	)) . '
		</div>

		' . $__templater->formSubmitRow(array(
		'submit' => 'Confirm',
		'icon' => 'login',
	), array(
	)) . '
	</div>

	' . $__templater->formHiddenVal('confirm', '1', array(
	)) . '
	' . $__templater->formHiddenVal('provider', $__vars['providerId'], array(
	)) . '
	' . $__templater->formHiddenVal('remember', ($__vars['remember'] ? 1 : 0), array(
	)) . '

	' . $__templater->func('redirect_input', array($__vars['redirect'], null, true)) . '
', array(
		'action' => $__templater->func('link', array('login/two-step', ), false),
		'class' => 'block',
		'ajax' => 'true',
	));
	return $__finalCompiled;
});