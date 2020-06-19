<?php
// FROM HASH: 18d53b411fcf7df29b666b6f27fc4035
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '.bbCodeDemoBlock
{
	.m-clearFix();
	padding: .25em 0;
}

.bbCodeDemoBlock-item
{
	width: 50%;
	margin: 0;
	padding: 0;
	padding-left: 8px;
	float: left;

	&:first-child
	{
		padding-left: 0;
	}

	> dt
	{
		font-size: @xf-fontSizeSmall;
		color: @xf-textColorMuted;
	}

	> dd
	{
		margin: 0;
		.xf-minorBlockContent();
		padding: @xf-paddingMedium;
		zoom: 1;
	}
}';
	return $__finalCompiled;
});