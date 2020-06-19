<?php
// FROM HASH: a2b320366f3c34b0b3d2f23a2398d5e0
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '.smilieText
{
	display: inline-block;
	padding: 2px 3px;
	min-width: 16px;
	text-align: center;
	vertical-align: text-bottom;
	border-radius: @xf-borderRadiusMedium;
	font-size: @xf-fontSizeSmallest;
	.xf-chip();

	&:hover
	{
		.xf-chipHover();
	}
}';
	return $__finalCompiled;
});