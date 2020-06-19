<?php
// FROM HASH: da11cdbe9e379b4f1cf006359e9b5133
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '// ######################################### TOOLTIPS #######################################

@_tooltip-arrowSize: 5px;
@_tooltip-arrowSizeLarge: 10px;
@_tooltip-zIndex: @zIndex-3;

.tooltip
{
	position: absolute;
	z-index: @_tooltip-zIndex;
	line-height: @xf-lineHeightDefault;
	padding: 0 5px;
	display: none;

	&.tooltip--basic
	{
		max-width: 300px;

		// Tooltip positioning is literal.
		&.tooltip--top
		{
			margin-bottom: 3px;
			padding-bottom: @_tooltip-arrowSize;
		}
		&.tooltip--right
		{
			-ltr-rtl-margin-left: 3px;
			-ltr-rtl-padding-left: @_tooltip-arrowSize;
		}
		&.tooltip--bottom
		{
			margin-top: 3px;
			padding-top: @_tooltip-arrowSize;

			.has-touchevents &.tooltip--selectToQuote
			{
				margin-top: .75em;
			}
		}
		&.tooltip--left
		{
			-ltr-rtl-margin-right: 3px;
			-ltr-rtl-padding-right: @_tooltip-arrowSize;
		}

		.m-textColoredLinks();
	}

	&.tooltip--description
	{
		max-width: 350px;
	}

	&.tooltip--selectToQuote
	{
		.m-hiddenLinks();
	}

	&.tooltip--preview
	{
		max-width: 100%;
		width: 400px;
	}

	&.tooltip--member
	{
		max-width: 100%;
		width: 450px;
		padding: 0 10px;
	}

	&.tooltip--reaction
	{
		max-width: 100%;
		padding: 0 15px;
		margin: 5px 0;
	}

	&.tooltip--bookmark,
	&.tooltip--share
	{
		max-width: 100%;
		width: 340px;
		padding: 0 15px;
	}

	&.tooltip--bookmark,
	&.tooltip--member,
	&.tooltip--preview,
	&.tooltip--share
	{
		// Tooltip positioning is literal.
		&.tooltip--top { padding-bottom: @_tooltip-arrowSizeLarge; }
		&.tooltip--right { -ltr-rtl-padding-left: @_tooltip-arrowSizeLarge; }
		&.tooltip--bottom { padding-top: @_tooltip-arrowSizeLarge; }
		&.tooltip--left { -ltr-rtl-padding-right: @_tooltip-arrowSizeLarge; }
	}
}
.tooltip-content
{
	.tooltip--basic &
	{
		text-align: center;
		.xf-tooltip();
	}

	.tooltip--description &
	{
		text-align: left;
	}

	.tooltip--preview &
	{
		.xf-contentBase();
		padding: @xf-paddingMedium;
		text-align: left;
		border: 1px solid @xf-borderColor;

		.m-dropShadow(0, 5px, 10px, 0, .35);
	}

	.tooltip--bookmark &,
	.tooltip--member &,
	.tooltip--share &
	{
		.xf-contentBase();
		padding: 0;
		text-align: left;
		border: 1px solid @xf-borderColor;

		.m-dropShadow(0, 5px, 10px, 0, .35);
	}

	.tooltip--reaction &
	{
		.xf-contentBase();
		padding: 0;
		text-align: center;
		border: 1px solid @xf-borderColor;
		border-radius: @xf-borderRadiusLarge;
	}
}

.tooltip-content-inner
{
	position: relative;

	.tooltip--basic &
	{
		max-height: 200px;
		overflow: hidden;
	}

	.tooltip--preview &
	{
		max-height: 200px;
		overflow: hidden;

		.tooltip-content-cover
		{
			.m-gradient(fade(@xf-contentBg, 0%), @xf-contentBg, transparent, 160px, 200px);
		}
	}

	.tooltip-content-cover
	{
		position: absolute;
		top: 0;
		bottom: 0;
		left: 0;
		right: 0;
	}
}

// Tooltip side positioning is literal.
/* XF-RTL:disable */
.tooltip-arrow
{
	position: absolute;
	width: 0;
	height: 0;
	border: 0 solid transparent;

	.m-tooltipArrow(
		xf-default(@xf-tooltip--background-color, transparent),
		@_tooltip-arrowSize,
		~\'.tooltip--basic\'
	);

	@classes: tooltip--preview tooltip--member tooltip--share tooltip--bookmark;
	.m-tooltipArrowList(@xf-borderColor, @_tooltip-arrowSizeLarge + 1px, @classes, @xf-contentBg);

	.tooltip--member.tooltip--bottom &:after
	{
		.m-triangleUp(xf-default(@xf-memberTooltipHeader--background-color, transparent), @_tooltip-arrowSizeLarge);
	}
}

.m-tooltipArrowList(@color, @size, @classes, @fillColor: false)
{
	.variations(@classes, @i: 1) when (@i <= length(@classes))
	{
		@variation: extract(@classes, @i);

		.m-tooltipArrow(@color, @size, ~\'.@{variation}\', @fillColor);

		.variations(@classes, @i + 1);
	}

	.variations(@classes);
}
/* XF-RTL:enable */

.tooltipCover
{
	display: none;
	position: absolute;
	opacity: 0;
	z-index: (@_tooltip-zIndex - 1);
	-webkit-tap-highlight-color: rgba(0, 0, 0, 0);

	&.is-active
	{
		display: block;
	}
}';
	return $__finalCompiled;
});