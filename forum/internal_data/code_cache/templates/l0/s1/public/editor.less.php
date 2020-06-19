<?php
// FROM HASH: b70d031a3063e60c951baa4f8465b1e8
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '/* XF-RTL:disable */
' . $__templater->includeTemplate('editor_base.less', $__vars) . '
/* XF-RTL:enable */

// this allows us to ensure that when we scroll the editor into view, this goes below the fixed header
.fr-box.is-scrolling-to:before
{
	display: block;
	content: \'\';

	.m-stickyHeaderConfig(@xf-publicNavSticky);
	height: (@_stickyHeader-height + @_stickyHeader-offset);
	margin-top: -(@_stickyHeader-height + @_stickyHeader-offset);

	@media (max-height: 360px)
	{
		display: none;
	}
}

.fr-view
{
	.m-inputZoomFix();

	img.fr-draggable:not(.smilie),
	.bbImage
	{
		max-width: 100%;
		height: auto;
	}

	// remove image margins from images - they give the illusion of added spaces
	// extra specificity required for correct behaviour
	&&.fr-element img.fr-dii
	{
		margin: 0;
	}

	.fr-video
	{
		position: relative;

		video
		{
			width: 560px;
			max-width: 100%;
		}
	}

	p
	{
		margin-bottom: 0;
		margin-top: 0;
	}

	&.fr-element
	{
		.fr-disabled
		{
			.xf-inputDisabled() !important;
		}

		@attach-margin: @xf-bbCodeImgFloatMargin;

		img.fr-dii
		{
			display: inline-block;
			float: none;
			margin-left: @attach-margin;
			margin-right: @attach-margin;
			max-width: calc(100% - (2 * @attach-margin));

			&.fr-fil {
				float: left;
				margin: @attach-margin @attach-margin @attach-margin 0;
				max-width: calc(100% - @attach-margin);
			}

			&.fr-fir {
				float: right;
				margin: @attach-margin 0 @attach-margin @attach-margin;
				max-width: calc(100% - @attach-margin);
			}
		}

		.fr-video.fr-dvi
		{
			display: inline-block;
			float: none;
			margin-left: @attach-margin;
			margin-right: @attach-margin;
			max-width: calc(100% - (2 * @attach-margin));

			&.fr-fvl {
				float: left;
				margin: @attach-margin @attach-margin @attach-margin 0;
				max-width: calc(100% - @attach-margin);
			}

			&.fr-fvr {
				float: right;
				margin: @attach-margin 0 @attach-margin @attach-margin;
				max-width: calc(100% - @attach-margin);
			}
		}
	}

	.m-tableBase();
}

.fr-command.fr-btn + .fr-dropdown-menu
{
	display: none;

    .fr-dropdown-wrapper
	{
		background: @xf-contentBg;
		border: @xf-borderSize solid @xf-borderColor;

		.fr-dropdown-content ul.fr-dropdown-list li a img
		{
			// note: this selector roughly matches a core froala selector
			height: 1em;
		}
	}
}

.fr-toolbar .fr-btn.fr-active[data-cmd="xfBbCode"]
{
	color: @xf-textColorAttention;
}

.fr-popup
{
	background: @xf-contentBg;
}

.fr-popup .fr-input-line
{
	padding: 16px 0 8px;

	input[type="text"],
	textarea
	{
		.xf-input();
		margin: 0;
		line-height: @xf-lineHeightDefault;
		.m-transition(background, color;);

		&:focus
		{
			.xf-input(border);
			.xf-inputFocus();
		}

		.m-inputZoomFix();
	}

	input + label,
	textarea + label
	{
		line-height: 1.2;
		font-size: 12px;
		background: transparent;
	}

	input.fr-not-empty:focus + label,
	textarea.fr-not-empty:focus + label
	{
		color: @xf-textColorMuted;
	}
}

.fr-popup .fr-color-set
{
	> span .fr-selected-color
	{
		font-family: \'Font Awesome 5 Pro\';
		font-weight: @xf-fontAwesomeWeight;
	}
}

.fr-popup .fr-color-hex-layer
{
	.fr-input-line
	{
		padding-top: 16px;
		width: 150px;
	}

	.fr-action-buttons
	{
		margin-top: 18px;

		.m-frCommandStyle();
	}
}

.fr-popup .fr-action-buttons
{
	height: auto;

	.m-frCommandStyle();
}

.m-frCommandStyle()
{
	button.fr-command
	{
		.m-buttonBase();
		.xf-buttonPrimary();
		.m-buttonBlockColorVariationSimple(xf-default(@xf-buttonPrimary--background-color, transparent));
		height: auto;
		min-width: 0;
		line-height: @xf-lineHeightDefault;

		&:hover,
		&:active,
		&:focus
		{
			// overriding Froala\'s hover
			color: @xf-buttonPrimary--color;
		}
	}
}

@_menu-arrowSize: 8px;

.fr-popup.fr-active
{
	margin-top: 15px;

	border-left: 0;
	border-right: 0;
	border-bottom: 0;
	border-radius: @xf-menuBorderRadius;
	opacity: 1;
	.m-dropShadow(0, 5px, 10px, 0, .35);

	.fr-buttons
	{
		border: 0;
	}
}

.fr-popup .fr-arrow
{
	top: -@_menu-arrowSize - 3px;
	border-left-width: @_menu-arrowSize;
	border-right-width: @_menu-arrowSize;
	border-bottom-width: @_menu-arrowSize;
	margin-left: -@_menu-arrowSize;
}

// RTE disabled case
.fr-box textarea.input
{
	border-top: none;
	.border-radius(0 0 @border-radius @border-radius);
}

.editorDraftIndicator
{
	.m-transition();
	opacity: 0;
	position: absolute;
	bottom: 7px;
	right: 12px;
	width: 7px;
	height: 7px;
	border-radius: 3.5px;
	background: rgb(127, 185, 0);

	&.is-active
	{
		opacity: 1;
	}
}

@editorSmiliesBg: xf-intensify(@xf-contentBg, 1%);

.editorSmilies
{
	display: none;
	border: @xf-borderSize solid @xf-borderColorHeavy;
	border-top: none;
	background: @xf-editorToolbarBg;
	overflow: hidden;
	.m-transition(all, -xf-height;);
	height: 0;

	&.is-active
	{
		display: block;
		height: auto;
	}

	&.is-transitioning
	{
		display: block;
	}

	.smilie
	{
	//	cursor: pointer;
	}

	.tabPanes > li
	{
		padding: @xf-blockPaddingV @xf-blockPaddingH;
	}
}

.tabs--editor // takes some hints from .tabs--standalone
{
	color: @xf-paletteColor4;
	background: @editorSmiliesBg;
	font-weight: @xf-fontWeightNormal;
	border-bottom:  @xf-borderSize solid @xf-borderColor;

	.m-tabsTogether(@xf-fontSizeSmall);

	.tabs-tab
	{
		padding: @xf-blockPaddingV @xf-blockPaddingH max(0px, @xf-blockPaddingV - @xf-borderSizeFeature);
		border-bottom: @xf-borderSizeFeature solid transparent;

		&:hover
		{
			color: @xf-standaloneTab--color;
		}

		&.is-active
		{
			color: @xf-textColorFeature;
			border-color: @xf-borderColorFeature;
		}
	}

	.hScroller-action
	{
		.m-hScrollerActionColorVariation(
			@editorSmiliesBg,
			xf-default(@xf-standaloneTab--color, ~""),
			xf-default(@xf-standaloneTabSelected--color, ~"")
		);
	}
}

@_menu-padding: @xf-paddingMedium;
@_menu-padding-large: @xf-paddingLarge;

.menu--emoji
{
	width: 412px;
	max-width: ~"calc(100% - @{xf-pageEdgeSpacer})";

	.menu-content
	{
		position: relative;
	}

	.menu-scroller
	{
		max-height: 250px;
		border-top: 0;
	}

	.menu-row
	{
		padding: @_menu-padding @_menu-padding;

		&.menu-row--insertedMessage
		{
			.m-hiddenEl(true);
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			border-bottom: @xf-borderSize solid @xf-borderColorLight;
		}
	}

	.menu-emojiInsertedMessage
	{
		display: flex;
		align-items: center;
		justify-content: center;
		min-height: 35px;
		font-size: @xf-fontSizeNormal;

		img { max-height: 32px; }
		span { margin-left: .5em }
	}

	.menu-header
	{
		background: none;
		.xf-formSectionHeader();
		background-color: @xf-contentBg;
		z-index: @zIndex-1;
		font-size: @xf-fontSizeNormal;
		position: -webkit-sticky;
		position: sticky;
		padding: @_menu-padding @_menu-padding-large;
		top: 0;
	}

	.block-body--emoji
	{
		border-top: @xf-borderSize solid @xf-borderColorLight;
	}

	.is-hidden
	{
		display: none;
	}
}

.emojiList
{
	.m-listPlain();

	display: flex;
	flex-wrap: wrap;
	justify-content: flex-start;

	margin-right: -3px;
	margin-bottom: -3px;

	> li
	{
		min-width: 32px;
		margin-right: 3px;
		margin-bottom: 3px;
		
		border-radius: @xf-borderRadiusMedium;
		cursor: pointer;

		&:hover,
		&:focus
		{
			background-color: @xf-paletteColor2;
		}

		a
		{
			min-width: 32px;
			height: 32px;
			font-size: 24px;

			display: flex;
			justify-content: center;
			align-items: center;
			cursor: pointer;
			overflow: hidden;

			&:hover,
			&:focus
			{
				text-decoration: none;
			}

			img
			{
				max-height: 32px;
			}

			.smilie.smilie--emoji
			{
				width: 22px;
			}

			.smilie--lazyLoad
			{
				visibility: hidden;
			}
		}
	}
}

.editorPlaceholder
{
	.is-hidden
	{
		display: none;
	}

	cursor: text;

	.editorPlaceholder-placeholder
	{
		.input
		{
			padding: @xf-paddingLarge;
			//border-radius: 0;
			//border-top: 3px solid #47a7eb;

			span
			{
				&:before
				{
					.m-faBase();
					.m-faContent(@fa-var-pen);
				}
			}
		}
	}
}';
	return $__finalCompiled;
});