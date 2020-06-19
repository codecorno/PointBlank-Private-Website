<?php
// FROM HASH: 300c7e8c2b68788dd7a493f8affc5352
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '.eventDescription
{
	font-size: @xf-fontSizeSmall;
	color: @xf-textColorDimmed;

	code, pre
	{
		font-family: @xf-fontFamilyCode;
		direction: ltr;
	}

	code
	{
		color: black;
		font-weight: @xf-fontWeightHeavy;
		white-space: normal;

		em
		{
			font-weight: @xf-fontWeightNormal;
			font-style: normal;
			color: @xf-textColorMuted;
		}
	}

	pre
	{
		color: black;
		overflow: auto;
	}
}';
	return $__finalCompiled;
});