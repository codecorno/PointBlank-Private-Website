<?php
// FROM HASH: 7a1ecfbdb70fdcfe0dc1585ba24fb7d8
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '@_stylePropertiesInputWidth: 300px;

.cssCustomHighlight
{
	&.cssCustomHighlight--custom
	{
		color: red;
	}

	&.cssCustomHighlight--inherited
	{
		color: orange;
	}

	&.cssCustomHighlight--added
	{
		color: green;
	}
}

.cssPropertyWrapper
{
	.formRow-hint--customState
	{
		float: right;

		> span
		{
			font-size: @xf-fontSizeSmallest;
			font-weight: @xf-fontWeightHeavy;
			vertical-align: .3em;
		}
	}
}

.cssPropertyDescription
{
	.m-clearFix();

	font-size: @xf-fontSizeSmall;
	color: @xf-textColorDimmed;
}

.cssProperty
{
	margin: 0;
	padding: 0;
	list-style: none;
	display: flex;
	flex-wrap: wrap;

	> li
	{
		display: inline-block;
		vertical-align: top;
		margin-top: @xf-paddingLargest;
		margin-right: 20px;
		max-width: 100%;
	}
}

.cssPropertyExtra
{
	.cssPropertyHeader
	{
		margin-bottom: 3px;
	}

	.input,
	.codeEditor
	{
		width: 450px;
		max-width: 100%;
	}
}

.m-cssPropertyHeaderText()
{
	font-size: @xf-fontSizeLarge;
	font-weight: @xf-fontWeightNormal;
	color: @xf-textColorFeature;
}

.cssPropertyHeader
{
	margin: 0;
	padding: 0;
	.m-cssPropertyHeaderText();
	padding-bottom: 2px;
	border-bottom: 1px solid @xf-borderColor;
}

.cssPropertyRevert
{
	font-size: @xf-fontSizeSmaller;
	float: right;
}

table.cssPropertySet
{
	border: none;
	border-spacing: 0;
	border-collapse: collapse;

	td
	{
		padding: 3px;

		&:first-child
		{
			padding-left: 0;
		}

		&:last-child
		{
			padding-right: 0;
		}
	}
}

.cssPropertySet-headerRow
{
	th
	{
		font-size: @xf-fontSizeSmaller;
		font-weight: @xf-fontWeightNormal;
		text-align: left;
		vertical-align: baseline;
		padding: 0 4px;

		&.cssPropertySet-headerSetLabel
		{
			.m-cssPropertyHeaderText();
			padding-left: 0;
			padding-right: 6px;
		}
	}

	&.cssPropertySet-headerRow--separated th
	{
		border-bottom: 1px solid @xf-borderColor;
		padding-bottom: 2px;
	}
}

.cssPropertySet-rowLabel
{
	font-size: @xf-fontSizeSmall;
	font-weight: @xf-fontWeightNormal;
	text-align: right;
}

.input
{
	&.input--cssProp
	{
		font-size: @xf-fontSizeSmall;
		padding: @xf-paddingSmall;
	}

	&.input--cssLength
	{
		text-align: right;
		width: 120px;
	}

	&.input--colorWidthMatched
	{
		width: @_stylePropertiesInputWidth;
		max-width: 100%;
	}

	.p-styleProperties &.input--number
	{
		width: @_stylePropertiesInputWidth;
		max-width: 100%;
		text-align: left;

		&.js-numberBoxTextInput
		{
			width: (@_stylePropertiesInputWidth) - 70px;
		}
	}
}

.p-styleProperties .inputGroup--color
{
	//width: @_stylePropertiesInputWidth;

	.inputGroup-text--rgbTxt
	{
		width: 45px;
		font-size: @xf-fontSizeSmallest;
		font-weight: bold;
		color: @xf-textColorMuted;
		//background-color: @xf-contentAltBg;
		justify-content: center;
	}
}

.inputChoices
{
	&.inputChoices--cssTextOptions
	{
		> li
		{
			display: inline-block;
			margin-bottom: 0;
			margin-right: 10px;
			font-size: @xf-fontSizeSmall;

			&:last-child
			{
				margin-right: 0;
			}
		}
	}
}

.formRow
{
	.formRow-revert
	{
		margin-top: @xf-paddingMedium;
		font-size: @xf-fontSizeSmaller;
	}

	.formRow-hint--customState
	{
		display: block;
		font-size: @xf-fontSizeSmallest;

		.iconic-label
		{
			font-weight: @xf-fontWeightHeavy;
		}
	}
}

@media (max-width: @xf-responsiveNarrow)
{
	.p-styleProperties .inputGroup--color,
	.input.input--colorWidthMatched
	{
		width: 200px;
	}

	.cssProperty
	{
		display: block;

		> li
		{
			display: block;
			margin-right: 0;
		}
	}

	.cssPropertySet
	{
		width: 100%;

		.input--cssLength
		{
			width: 80px;
		}

		.inputGroup.inputGroup--colorSmall
		{
			width: 130px;
		}

		.colorPickerBox
		{
			width: 30px;
		}
	}

	.cssPropertyExtra .input,
	.cssPropertyExtra .codeEditor
	{
		width: ~\'calc(100vw - 45px)\';
	}
}';
	return $__finalCompiled;
});