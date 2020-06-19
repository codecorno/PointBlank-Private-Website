<?php
// FROM HASH: 7d4d2378220ffab756b663bf40770b1f
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '// MAIN HEADER ROW

.p-header
{
	.xf-publicHeader();

	a
	{
		color: inherit;
	}
}

.p-header-inner
{
	.m-pageWidth();
	.m-pageInset();
}

.p-header-content
{
	padding: @xf-paddingMedium 0;

	display: flex;
	flex-wrap: wrap;
	justify-content: space-between;
	align-items: center;
	max-width: 100%;
}

.p-header-logo
{
	vertical-align: middle;
	margin-right: auto;

	a
	{
		color: inherit;
		text-decoration: none;
	}

	&.p-header-logo--text
	{
		font-size: @xf-fontSizeLargest;
	}

	&.p-header-logo--image
	{
		img
		{
			vertical-align: bottom;
			max-width: 100%;
			max-height: 200px;
		}
	}
}

@media (max-width: @xf-publicNavCollapseWidth)
{
	.has-js .p-header
	{
		display: none;
	}
}

@media (max-width: @xf-responsiveNarrow)
{
	.p-header-logo
	{
		max-width: 100px;

		&.p-header-logo--text
		{
			font-size: @xf-fontSizeLarge;
			font-weight: @xf-fontWeightNormal;
			.m-overflowEllipsis();
		}
	}
}';
	return $__finalCompiled;
});