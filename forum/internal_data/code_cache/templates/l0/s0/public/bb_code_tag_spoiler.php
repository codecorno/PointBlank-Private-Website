<?php
// FROM HASH: 5ec9c8418429e0cf949d73d5a76f524e
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->includeCss('bb_code.less');
	$__finalCompiled .= '

<div class="bbCodeSpoiler">
	';
	$__compilerTemp1 = '';
	if ($__vars['title']) {
		$__compilerTemp1 .= ': <span class="bbCodeSpoiler-button-title">' . $__templater->escape($__vars['title']) . '</span>';
	}
	$__finalCompiled .= $__templater->button('

		<span>' . 'Spoiler' . $__compilerTemp1 . '</span>
	', array(
		'class' => 'bbCodeSpoiler-button' . ($__vars['title'] ? ' button--longText' : ''),
		'data-xf-click' => 'toggle',
		'data-xf-init' => 'tooltip',
		'title' => 'Click to reveal or hide spoiler',
	), '', array(
	)) . '
	<div class="bbCodeSpoiler-content">
		<div class="bbCodeBlock bbCodeBlock--spoiler">
			<div class="bbCodeBlock-content">' . $__templater->escape($__vars['content']) . '</div>
		</div>
	</div>
</div>';
	return $__finalCompiled;
});