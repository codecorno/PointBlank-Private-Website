<?php
// FROM HASH: 548b966340e30ae11c96fa876eb84926
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->includeJs(array(
		'prod' => 'xf/tel_box-compiled.js',
		'dev' => 'vendor/intl-tel-input/intlTelInput.min.js, vendor/intl-tel-input/utils.js, xf/tel_box.js',
	));
	$__finalCompiled .= '

';
	$__templater->includeCss('tel_box.less');
	$__finalCompiled .= '

<div data-xf-init="tel-box">
	<input type="tel" name="' . $__templater->escape($__vars['name']) . '" value="' . $__templater->escape($__vars['value']) . '" class="input js-telInput" ' . $__templater->filter($__vars['attrsHtml'], array(array('raw', array()),), true) . ' />

	' . $__templater->formHiddenVal($__vars['dialCodeName'], '', array(
		'class' => 'js-dialCode',
	)) . '
	' . $__templater->formHiddenVal($__vars['intlNumberName'], '', array(
		'class' => 'js-intlNumb',
	)) . '
</div>';
	return $__finalCompiled;
});