<?php
// FROM HASH: 1dd941610e357da2ca43c450dceb5bf7
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Test image proxy');
	$__finalCompiled .= '
';
	$__templater->pageParams['pageDescription'] = $__templater->preEscaped('This tool helps diagnose errors relating to the image proxy system by giving more detailed information than can be given when displaying an image.');
	$__templater->pageParams['pageDescriptionMeta'] = true;
	$__finalCompiled .= '

';
	if ($__vars['results']) {
		$__finalCompiled .= '
	';
		if ($__vars['results']['valid']) {
			$__finalCompiled .= '
		<div class="blockMessage blockMessage--success blockMessage--iconic">' . '' . $__templater->escape($__vars['url']) . ' was fetched successfully.' . '</div>
	';
		} else {
			$__finalCompiled .= '
		<div class="blockMessage blockMessage--error blockMessage--iconic">
			' . '' . $__templater->escape($__vars['url']) . ' could not be fetched or is not a valid image. The specific error message was: ' . $__templater->escape($__vars['results']['error']) . '' . '
		</div>
	';
		}
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

' . $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formTextBoxRow(array(
		'name' => 'url',
		'value' => $__vars['url'],
		'type' => 'url',
		'dir' => 'ltr',
	), array(
		'label' => 'URL',
	)) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'submit' => 'Run test',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('tools/test-image-proxy', ), false),
		'class' => 'block',
	));
	return $__finalCompiled;
});