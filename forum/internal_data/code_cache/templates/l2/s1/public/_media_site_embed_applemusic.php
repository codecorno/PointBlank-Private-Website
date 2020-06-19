<?php
// FROM HASH: 3efd5bcbec98000126cdf4e765b19849
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<div class="bbMediaWrapper">
	<div class="bbMediaWrapper-inner bbMediaWrapper-inner--' . $__templater->escape($__vars['height']) . 'px">
		<iframe src="' . $__templater->escape($__vars['url']) . '"
			style="' . $__templater->escape($__vars['style']) . '"
			height="' . $__templater->escape($__vars['height']) . 'px"
			frameborder="0"
			scrolling="' . $__templater->escape($__vars['scrolling']) . '"></iframe>
	</div>
</div>';
	return $__finalCompiled;
});