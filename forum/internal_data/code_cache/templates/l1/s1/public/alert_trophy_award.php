<?php
// FROM HASH: 5f971eaffdf0051a1b3b63cdc35b9aec
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= 'You have been awarded a trophy: ' . (((('<a href="' . $__templater->func('link', array('members/trophies', $__vars['xf']['visitor'], ), true)) . '" class="fauxBlockLink-blockLink" data-xf-click="overlay">') . $__templater->escape($__vars['content']['title'])) . '</a>') . '';
	return $__finalCompiled;
});