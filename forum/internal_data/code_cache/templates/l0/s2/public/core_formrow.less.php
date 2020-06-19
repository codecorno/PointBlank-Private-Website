<?php
// FROM HASH: c481698e85f51b61d4f5da62ecb96b9b
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '// ########################################### FORM ROWS #################################

@_form-labelShiftInput: @xf-paddingMedium + @xf-borderSize; // technically input border-top-width + padding-top
@_form-labelButtonInput: 5px; // technically button border-top-width + padding-top, adjusted for text size differences
@_form-elementSpacer: @xf-paddingMedium;

.m-formRowBlockStyle()
{
	display: block;

	> dt,
	> dd
	{
		width: auto;
		display: block;
		padding: ((@xf-formRowPaddingV) / 2) @xf-formRowPaddingHOuter @xf-formRowPaddingV;
	}

	> dt
	{
		background: none;
		border: none;
		text-align: left;
		padding-bottom: 0;

		.formRow-hint
		{
			display: inline;
		}
	}

	&.formRow--input,
	&.formRow--button
	{
		> dt
		{
			padding-top: ((@xf-formRowPaddingV) / 2);
		}
	}

	> dd
	{
		padding-top: ((@xf-formRowPaddingV) / 2);
	}

	> dd > .inputChoices:first-child
	{
		padding-top: 0;
	}
}

.formRow
{
	display: table;
	table-layout: fixed;
	width: 100%;
	margin: 0;
	position: relative;

	> dt,
	> dd
	{
		display: table-cell;
		vertical-align: top;
		margin: 0;
	}

	> dt
	{
		border-right: @xf-borderSize solid transparent;
		.xf-formLabel();
		width: @xf-formLabelWidth;
		padding: @xf-formRowPaddingV @xf-formRowPaddingHInner @xf-formRowPaddingV @xf-formRowPaddingHOuter;
	}

	> dd
	{
		width: (100% - @xf-formLabelWidth);
		padding: @xf-formRowPaddingV @xf-formRowPaddingHOuter @xf-formRowPaddingV @xf-formRowPaddingHInner;
	}

	&.formRow--input > dt
	{
		padding-top: (@xf-formRowPaddingV + @_form-labelShiftInput);
	}

	&.formRow--button > dt
	{
		padding-top: (@xf-formRowPaddingV + @_form-labelButtonInput);
	}

	&.formRow--inputLabelPair > dt
	{
		padding-top: (@xf-formRowPaddingV * 2);
	}

	&.formRow--valueToEdge > dd
	{
		padding-left: 0;
		padding-right: 0;
	}

	&.formRow--noValuePadding > dd
	{
		padding: 0;
	}

	&.formRow--limited
	{
		display: none;
	}

	&.formRow--fullWidth
	{
		.m-formRowBlockStyle();

		& + .formRow,
		.formRow + &,
		& + * > .formRow:first-of-type, // allows formRows to be wrapped
		.formRow + * > &:first-of-type //  in a single-depth grouping element
		{
			border-top: @xf-borderSize solid @xf-borderColorLight;
		}

		&.formRow--noLabel
		{
			> dt
			{
				display: none;
			}

			> dd
			{
				padding-top: @xf-formRowPaddingV;
			}
		}

		& + .formRow--mergePrev,
		& + * > .formRow:first-of-type.formRow--mergePrev,
		&.formRow--mergeNext + &,
		&.formRow--mergeNext + * > &:first-of-type
		{
			border-top: 0;

			> dt
			{
				padding-top: 0;
			}
		}

		&.formRow--noGutter
		{
			> dt,
			> dd
			{
				padding-left: 0;
				padding-right: 0;
			}
		}

		&.formRow--noPadding
		{
			> dt,
			> dd
			{
				padding: 0;
			}
		}

		&.formRow--noPadding + &,
		&.formRow--noPadding + * > &:first-of-type
		{
			> dt
			{
				padding-top: @xf-formRowPaddingV;
			}
		}
	}

	&.formRow--inputMultiLine > dd
	{
		> .input,
		> .inputGroup
		{
			margin-bottom: @_form-elementSpacer;

			&:last-child
			{
				margin-bottom: 0;
			}
		}
	}

	.formRow-label
	{
		.m-appendColon();
	}

	&.formRow--noColon .formRow-label:after
	{
		content: "";
	}

	.formRow-explain
	{
		margin: @_form-elementSpacer 0 0;
		.m-formElementExplain();
	}

	&.formRow--explainOffset .formRow-explain
	{
		margin-top: (@_form-elementSpacer) * 2;
	}

	.formRow-hint
	{
		display: block;
		font-style: normal;
		.xf-formHint();

		.m-textColoredLinks();

		.formRow-hint-featured
		{
			display: block;
			//font-weight: @xf-fontWeightHeavy;
			color: @xf-textColorFeature;
		}
	}

	+ .formInfoRow
	{
		border-top: @xf-borderSize solid @xf-borderColorLight;
	}

	@media (max-width: @xf-formResponsive)
	{
		.m-formRowBlockStyle();
	}
}

.formButtonGroup
{
	display: flex;
	flex-wrap: wrap;
	margin-top: @xf-paddingLarge;
	margin-bottom: -5px;

	&.formButtonGroup--close
	{
		margin-top: 0;
	}
}

.formButtonGroup-primary
{
	order: 2;
	margin-left: auto;
	margin-bottom: 5px;

	.formButtonGroup--simple &
	{
		margin-left: 0;
	}
}

.formButtonGroup-extra
{
	order: 1;
	margin-bottom: 5px;
}

.formInfoRow
{
	padding: @xf-formRowPaddingV @xf-formRowPaddingHOuter;

	&.formInfoRow--close
	{
		padding-top: ((@xf-formRowPaddingV) / 2);
		padding-bottom: ((@xf-formRowPaddingV) / 2);
	}

	&.formInfoRow--noPadding
	{
		padding: 0;
	}

	&.formInfoRow--confirm
	{
		text-align: center;

		strong
		{
			display: block;
			margin: .5em 0;
			font-size: @xf-fontSizeLarger;

			&:last-child
			{
				margin-bottom: 0;
			}
		}
	}

	+ .formRow
	{
		border-top: @xf-borderSize solid @xf-borderColorLight;
	}

	&.u-hidden:not(.is-active)
	{
		+ .formRow
		{
			border-top: none;
		}
	}

	> .blockMessage
	{
		&:first-child { margin-top: 0; }
		&:last-child { margin-bottom: 0; }
	}
}

.formSubmitRow
{
	position: relative;

	> dt
	{
		display: none;
	}

	> dd
	{
		width: 100%;
		padding: 0;
	}

	&.formSubmitRow--sticky.is-sticky
	{
		.formSubmitRow-main
		{
			position: fixed;
			bottom: 0;
			z-index: @zIndex-2;
		}

		.formSubmitRow-bar
		{
			box-shadow: 0px -5px 15px fade(black, 15%);
		}
	}

	&.formSubmitRow--simple,
	&.formSubmitRow--standalone
	{
		> dt
		{
			visibility: hidden;
		}

		.formSubmitRow-controls
		{
			text-align: center;
			padding-left: 0;
			margin-left: 0;
		}
	}

	&.formSubmitRow--standalone
	{
		.formSubmitRow-bar
		{
			border: @xf-borderSize solid @xf-borderColor;
			border-radius: @block-borderRadius-inner;

			@media (max-width: @xf-responsiveEdgeSpacerRemoval)
			{
				border-radius: 0;
				border-left: none;
				border-right: none;
			}
		}

		&.is-sticky
		{
			.formSubmitRow-bar
			{
				border-bottom: 0;
			}
		}
	}

	.block-body--collapsible:not(.is-active) + &:not(.formSubmitRow--simple, .formSubmitRow--standalone)
	{
		> dt
		{
			visibility: hidden;
		}
	}
}

.formSubmitRow-main
{
	position: relative;
}

.formSubmitRow-bar
{
	position: absolute;
	top: 0;
	right: 0;
	bottom: 0;
	left: 0;

	.xf-formSubmitRow();
}

.formSubmitRow-controls
{
	position: relative;
	padding-left: @xf-formLabelWidth;
	padding-top: @xf-paddingMedium;
	padding-bottom: @xf-paddingMedium;
	margin-left: @xf-formRowPaddingHInner;
	margin-right: @xf-formRowPaddingHOuter;

	> .button:first-child:last-child
	{
		min-width: 120px;
	}
}

@media (max-width: @xf-formResponsive)
{
	.formSubmitRow-controls
	{
		padding-left: 0;
		text-align: center;
	}
}

.formRowSep
{
	margin: -1px @xf-formRowPaddingHOuter 0;
	border: none;
	border-top: @xf-borderSize solid @xf-borderColor;

	+ .formRowSep,
	&:last-child
	{
		display: none;
	}

	.block-body > &:first-child
	{
		display: none;
	}
}';
	return $__finalCompiled;
});