<?php
// FROM HASH: fe464064f3a2be8296713c7f1c1523fd
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->includeJs(array(
		'src' => 'xf/captcha.js',
		'min' => '1',
	));
	$__finalCompiled .= '

<div data-xf-init="re-captcha" data-sitekey="' . $__templater->escape($__vars['siteKey']) . '" data-invisible="' . $__templater->escape($__vars['invisible']) . '"></div>

<noscript>
	<div>
		<div style="width: 302px; height: 422px; position: relative;">
			<div style="width: 302px; height: 422px; position: absolute;">
				<iframe src="https://www.google.com/recaptcha/api/fallback?k=' . $__templater->filter($__vars['siteKey'], array(array('urlencode', array()),), true) . '"
					frameborder="0" scrolling="no"
					style="width: 302px; height:422px; border-style: none;"></iframe>
			</div>
		</div>
		<div style="width: 300px; height: 60px; border-style: none;
			bottom: 12px; left: 25px; margin: 0px; padding: 0px; right: 25px;
			background: #f9f9f9; border: 1px solid #c1c1c1; border-radius: 3px;">
			<textarea name="g-recaptcha-response" id="g-recaptcha-response"
				class="g-recaptcha-response"
				style="width: 250px; height: 40px; border: 1px solid #c1c1c1;
				margin: 10px 25px; padding: 0px; resize: none;"></textarea>
		</div>
	</div>
</noscript>';
	return $__finalCompiled;
});