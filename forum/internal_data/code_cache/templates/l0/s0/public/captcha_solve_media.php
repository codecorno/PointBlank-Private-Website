<?php
// FROM HASH: 2681ffc980634aba3aceeae159c9bc82
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->includeJs(array(
		'src' => 'xf/captcha.js',
		'min' => '1',
	));
	$__finalCompiled .= '

<div class="j-jsOnly" data-xf-init="solve-captcha" data-ckey="' . $__templater->escape($__vars['cKey']) . '" id="' . $__templater->func('unique_id', array(), true) . '">
	' . 'Loading' . $__vars['xf']['language']['ellipsis'] . '
</div>
<noscript>
	<iframe src="https://api-secure.solvemedia.com/papi/challenge.noscript?k=' . $__templater->escape($__vars['cKey']) . '" height="300" width="500" frameborder="0"></iframe><br />
	' . $__templater->formTextArea(array(
		'name' => 'adcopy_challenge',
		'rows' => '3',
		'cols' => '40',
	)) . '
	' . $__templater->formHiddenVal('adcopy_response', 'manual_challenge', array(
	)) . '
</noscript>';
	return $__finalCompiled;
});