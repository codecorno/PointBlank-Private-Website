<?php
// FROM HASH: a54c695be093322d8f5f7039162dbb33
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '.tagCloud-tag
{
	display: inline-block;
	margin-right: 8px;

	&:last-child
	{
		margin-right: 0;
	}

	&Level1 { font-size: 100%; color: xf-diminish(@xf-linkColor, 10%); }
	&Level2 { font-size: 100%; }
	&Level3 { font-size: 125%; }
	&Level4 { font-size: 150%; }
	&Level5 { font-size: 175%; }
	&Level6 { font-size: 200%; }
	&Level7 { font-size: 225%; color: xf-intensify(@xf-linkColor, 10%); }
}';
	return $__finalCompiled;
});