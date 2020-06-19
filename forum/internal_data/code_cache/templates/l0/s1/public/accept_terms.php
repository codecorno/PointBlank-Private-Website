<?php
// FROM HASH: 12082f74616c42237369b1fbefe7419a
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Terms and rules');
	$__finalCompiled .= '

<div class="blockMessage blockMessage--iconic blockMessage--important">
	' . 'Please read and accept our terms and rules before continuing.' . ' ' . 'Last updated' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->func('date_dynamic', array($__vars['xf']['options']['termsLastUpdate'], array(
	))) . '.
</div>

' . $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'accept',
		'required' => 'required',
		'label' => 'I have read and accept your <a href="' . $__templater->escape($__vars['xf']['tosUrl']) . '" target="_blank">terms and rules</a>.',
		'_type' => 'option',
	)), array(
		'label' => '',
	)) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'icon' => 'confirm',
		'submit' => 'Accept',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('misc/accept-terms', ), false),
		'ajax' => 'true',
		'class' => 'block',
		'data-force-flash-message' => 'on',
	)) . '

';
	if ($__vars['termsOption']['type'] == 'default') {
		$__finalCompiled .= '
	';
		if ($__vars['page']['advanced_mode']) {
			$__finalCompiled .= '
		' . $__templater->filter($__vars['templateHtml'], array(array('raw', array()),), true) . '
	';
		} else {
			$__finalCompiled .= '
		<div class="block">
			<div class="block-container">
				<div class="block-body block-row">
					' . $__templater->filter($__vars['templateHtml'], array(array('raw', array()),), true) . '
				</div>
			</div>
		</div>
	';
		}
		$__finalCompiled .= '
';
	}
	return $__finalCompiled;
});