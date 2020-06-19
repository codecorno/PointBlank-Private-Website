<?php
// FROM HASH: 34710a676a73bd3b5f51e80a739c930a
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '.listColumns
{
	&.contentTypes
	{
		margin-bottom: 14px;
	}
}

.ct-chart
{
	svg
	{
		-webkit-backface-visibility: hidden;
	}

	.ct-series-a
	{
		.ct-line,
		.ct-point
		{
			stroke: @xf-paletteColor3;
		}
	}

	.ct-series-b
	{
		.ct-line,
		.ct-point
		{
			stroke: @xf-paletteAccent2;
		}
	}

	.ct-series-c
	{
		.ct-line,
		.ct-point
		{
			stroke: @xf-paletteColor5;
		}
	}

	.ct-series-d
	{
		.ct-line,
		.ct-point
		{
			stroke: @xf-paletteAccent3;
		}
	}

	.ct-grid
	{
		stroke: fade(@xf-textColor, 15%);
	}

	.ct-label
	{
		color: @xf-textColor;
		font-size: @xf-fontSizeSmallest;
	}

	.ct-line
	{
		stroke-width: 2px;
	}

	.ct-point
	{
		stroke-width: 8px;
	}

	.ct-label.ct-horizontal.ct-end
	{
		.m-transform(translateX(-50%));
		text-align: center;
		justify-content: center;
	}
}

.ct-chart--small
{
	.ct-point
	{
		opacity: 0;
	}
}

.ct-legend
{
	.m-listPlain();
	text-align: center;
	font-size: @xf-fontSizeSmaller;

	> li
	{
		display: inline-block;
		position: relative;
        padding-left: 16px;
		margin-right: 2em;

		&:last-child
		{
			margin-right: 0;
		}

		i
		{
			content: \'\';
			position: absolute;
			left: 0;
			top: 3px;
			width: 12px;
			height: 12px;
			border-radius: @xf-borderRadiusSmall;
			background: transparent;
		}
	}
}';
	return $__finalCompiled;
});