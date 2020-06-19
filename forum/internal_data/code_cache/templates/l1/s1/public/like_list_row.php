<?php
// FROM HASH: 05db323c98bda1364bc7d171fd6801ad
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<i class="likeIcon" title="' . $__templater->filter('Likes', array(array('for_attr', array()),), true) . '" aria-hidden="true"></i>
<span class="u-srOnly">' . 'Likes' . $__vars['xf']['language']['label_separator'] . '</span>
<a href="' . $__templater->escape($__vars['url']) . '" data-xf-click="overlay">' . $__templater->escape($__vars['likes']) . '</a>';
	return $__finalCompiled;
});