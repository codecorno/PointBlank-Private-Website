<?php
// FROM HASH: 05452c4d195ec05bf943e6ac8f74be91
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->includeJs(array(
		'src' => 'xf/captcha.js',
		'min' => '1',
	));
	$__finalCompiled .= '

<div data-xf-init="qa-captcha" data-url="' . $__templater->func('link', array('misc/captcha', ), true) . '">
	';
	if ($__vars['question']['captcha_question_id']) {
		$__finalCompiled .= '
		<div style="margin-top: 4px;">' . $__templater->filter($__vars['question']['question'], array(array('raw', array()),), true) . '</div>
		<div class="u-inputSpacer">
			' . $__templater->formTextBox(array(
			'name' => 'captcha_question_answer',
			'placeholder' => 'Please answer the question above...',
		)) . '
		</div>
	';
	} else {
		$__finalCompiled .= '
		' . 'N/A' . '
	';
	}
	$__finalCompiled .= '
	' . $__templater->formHiddenVal('captcha_question_hash', $__vars['question']['hash'], array(
	)) . '
</div>';
	return $__finalCompiled;
});