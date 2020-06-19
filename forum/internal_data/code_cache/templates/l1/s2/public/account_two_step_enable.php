<?php
// FROM HASH: 458a79867f24910d0214366adc5b3a76
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Two-step verification setup' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['provider']['title']));
	$__finalCompiled .= '

';
	$__templater->wrapTemplate('account_wrapper', $__vars);
	$__finalCompiled .= '

' . $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->filter($__templater->method($__vars['provider'], 'render', array('setup', $__vars['xf']['visitor'], $__vars['providerData'], $__vars['triggerData'], )), array(array('raw', array()),), true) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'submit' => 'Confirm',
	), array(
	)) . '
	</div>
	' . $__templater->formHiddenVal('step', 'confirm', array(
	)) . '
', array(
		'action' => $__templater->func('link', array('account/two-step/enable', $__vars['provider'], ), false),
		'class' => 'block',
		'ajax' => 'true',
	));
	return $__finalCompiled;
});