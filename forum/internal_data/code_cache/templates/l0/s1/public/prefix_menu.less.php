<?php
// FROM HASH: 6b4f2d51397fb6a9a27fc67e5c972124
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '.menuTrigger.menuTrigger--prefix
{
	text-decoration: none;
}

.menuPrefix,
.menuPrefix.label--hidden
{
	display: block;
	font-size: @xf-fontSizeSmall;
	cursor: default;
	padding: @xf-paddingMedium;
	//margin-bottom: -(@xf-paddingMedium);

	&.label--hidden
	{
		border: 1px solid @xf-borderColorFaint;
	}

	&.menuPrefix--none
	{
		color: @xf-textColorMuted;
		font-style: italic;
		text-decoration: none;
	}
}';
	return $__finalCompiled;
});