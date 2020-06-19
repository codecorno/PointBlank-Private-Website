<?php
// FROM HASH: c800709df521a418376a83a168702ee6
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->callMacro('lightbox_macros', 'single_image', array(
		'canViewAttachments' => true,
		'id' => $__templater->func('unique_id', array(), false),
		'src' => $__vars['imageUrl'],
		'dataUrl' => $__vars['validUrl'],
		'alt' => $__vars['alt'],
		'styleAttr' => $__vars['styleAttr'],
		'alignClass' => $__vars['alignClass'],
	), $__vars) . '
';
	return $__finalCompiled;
});