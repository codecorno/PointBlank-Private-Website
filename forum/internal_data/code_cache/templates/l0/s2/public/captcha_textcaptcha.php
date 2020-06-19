<?php
// FROM HASH: d717745ad25b4f44bc8a356f3d0534be
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
	if ($__vars['question']['q']) {
		$__finalCompiled .= '
		<div style="margin-top: 4px;">' . $__templater->escape($__vars['question']['q']) . '</div>
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