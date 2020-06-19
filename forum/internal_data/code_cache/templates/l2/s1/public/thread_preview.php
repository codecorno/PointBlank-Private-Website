<?php
// FROM HASH: 0461f7967958a24aa95476745110fdf5
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<div class="tooltip-content-inner">
	' . $__templater->func('bb_code', array($__vars['firstPost']['message'], 'post:thread_preview', $__vars['firstPost'], array('lightbox' => false, ), ), true) . '
	<span class="tooltip-content-cover"></span>
</div>';
	return $__finalCompiled;
});