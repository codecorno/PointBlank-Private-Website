<?php
// FROM HASH: fd167b2d280bbc2b32857862531a5f5d
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '@_adminPage-maxWidth: 1100px; // this does not include the navigation sidebar

.m-pageWidth()
{
	width: 100%;
	max-width: @_adminPage-maxWidth;
	margin: 0 auto;
}

.m-pageInset(@defaultVPadding: @xf-paddingLarge, @defaultHPadding: @xf-pageEdgeSpacer)
{
	padding: @defaultVPadding @defaultHPadding;

	// iPhone X/Xr/Xs support
	@supports(padding: max(0px))
	{
		&
		{
			padding-left: ~"max(@{defaultHPadding}, env(safe-area-inset-left))";
			padding-right: ~"max(@{defaultHPadding}, env(safe-area-inset-right))";
		}
	}
}

html
{
	.xf-pageBackground();
}

// ##################################### HEADER ###############################

@_adminHeader-height: 40px;
@_adminHeader-shadowHeight: 8px;
@_adminHeader-offset: 10px;
@_adminHeader-bg: @xf-paletteColor5;

.u-anchorTarget
{
	height: (@_adminHeader-height + @_adminHeader-shadowHeight + @_adminHeader-offset);
	margin-top: -(@_adminHeader-height + @_adminHeader-shadowHeight + @_adminHeader-offset);
}

.p-header
{
	position: fixed;
	top: 0;
	left: 0;
	right: 0;
	height: @_adminHeader-height;
	line-height: @_adminHeader-height;
	.m-clearFix();
	z-index: @zIndex-4;
	background: @_adminHeader-bg;
	color: contrast(@_adminHeader-bg);
	text-align: center;
	.m-dropShadow(0, 0, @_adminHeader-shadowHeight, 3px, 0.3);
}

.p-header-buttons
{
	&.p-header-buttons--main
	{
		float: left;
	}

	&.p-header-buttons--opposite
	{
		float: right;
	}
}

.p-header-button
{
	display: inline-block;
	color: inherit;
	cursor: pointer;
	text-decoration: none;
	text-align: center;
	min-width: @_adminHeader-height;
	overflow: hidden;
	.m-transition();

	.m-pageInset(0px);

	&:hover,
	&:active,
	&.is-active
	{
		background: xf-diminish(@_adminHeader-bg, 8%);
		color: inherit;
		text-decoration: none;
	}

	&.p-header-button--title
	{
		.m-overflowEllipsis();
		max-width: 250px;
	}

	&.p-header-button--nav
	{
		display: none;
	}
}

@media (max-width: @_adminNav-hideWidth)
{
	.p-header-button.p-header-button--nav
	{
		display: inline-block;
	}
}

@media (max-width: @xf-responsiveNarrow)
{
	.p-header-button.p-header-button--title
	{
		max-width: 160px;
	}
}

// ##################################### BODY AREA SETUP ##########################

.p-body-container
{
}

.p-body
{
	display: flex;
}

@media (max-width: @_adminNav-hideWidth)
{
	.p-body
	{
		display: block;
	}
}

// ###################################### SIDEBAR NAVIGATION #######################

@_adminNav-bg: @xf-paletteColor5;
@_adminNav-strongBg: xf-intensify(@xf-paletteColor5, 8%);
@_adminNav-color: @xf-paletteColor2;
@_adminNav-strongColor: @xf-paletteColor1;
@_adminNav-selectedBorder: @xf-borderColorFeature;
@_adminNav-width: 260px;
@_adminNav-hideWidth: @xf-responsiveWide;
@_adminNav-activelElBg: rgba(0, 0, 0, .1);

.p-nav
{
	min-height: 100vh;
	width: @_adminNav-width;
	padding-top: @_adminHeader-height;
	background: @_adminNav-bg;
	color: @_adminNav-strongColor;
	vertical-align: top;
	flex-shrink: 0;
}

.p-nav-tester
{
	display: none;
	font-family: \'default\';
}

.p-nav-inner
{
	height: 100%;
}

.p-nav-listRoot
{
	.m-listPlain();
	padding-bottom: 30px;
}

.p-nav-section
{
	.m-transition();

	.p-nav-listSection
	{
		.m-transitionFadeDown();
	}

	&.is-active
	{
		padding-bottom: 10px;
		.m-gradient(@_adminNav-strongBg, mix(@_adminNav-strongBg, @_adminNav-bg), @_adminNav-strongBg);
		box-shadow: inset 0 -10px 15px -10px rgba(0, 0, 0, .3), inset 0 10px 15px -10px rgba(0, 0, 0, .3);

		.p-nav:not(.offCanvasMenu) &:first-child
		{
			box-shadow: inset 0 -10px 15px -10px rgba(0, 0, 0, .3);
		}
	}
}

.p-nav-sectionHeader
{
	display: flex;
	color: @_adminNav-color;
	font-size: @xf-fontSizeLarge;
	.m-transition();
	.m-highResolution({
		font-weight: @xf-fontWeightLight;
	});

	&:hover
	{
		color: xf-diminish(@_adminNav-color, 3%);
		background: xf-intensify(@_adminNav-bg, 3%);
		text-decoration: none;
	}

	.p-nav-section.is-active &:hover
	{
		background: none;
	}

	a
	{
		color: inherit;

		&:hover
		{
			text-decoration: none;
		}
	}

	.p-nav-sectionLink
	{
		display: block;
		padding: 20px 10px;
		border-top: @xf-borderSize solid xf-intensify(@_adminNav-bg, 4%);
		flex-grow: 1;

		.far, .fal, .fas, .fab
		{
			color: @_adminNav-strongColor;
			font-size: @xf-fontSizeLarger;
			vertical-align: -2px;
		}
	}

	.p-nav-sectionToggle
	{
		display: inline-block;
		padding: 20px 10px;
		border-top: @xf-borderSize solid xf-intensify(@_adminNav-bg, 4%);
		text-decoration: none;
		flex-grow: 0;

		&:hover
		{
			text-decoration: none;
		}

		&:after
		{
			.m-faBase(\'Pro\', @faWeight-solid);
			font-size: 80%;
			.m-faContent(@fa-var-chevron-down, 1em);
		}

		.p-nav-section.is-active &:after
		{
			.m-faContent(@fa-var-chevron-up, 1em);
		}
	}
}

.p-nav-listSection
{
	.m-listPlain();
}

.p-nav-subSection
{
	margin-top: 15px;
	border-top: @xf-borderSize solid transparent;
	overflow: hidden;
	.m-transition();

	&:first-child
	{
		margin-top: 0;
	}

	> span,
	> a
	{
		display: block;
		padding: 3px 10px;
		font-size: @xf-fontSizeSmaller;
		font-weight: @xf-fontWeightHeavy;
		color: @_adminNav-strongColor;
		position: relative;

		> .far, > .fal, > .fas
		{
			color: fade(@_adminNav-strongColor, 2.5%);
			font-size: 60px;

			position: absolute;
			top: 5px;
			right: .05em;
			pointer-events: none;

			.m-transition();
		}
	}

	+ .p-nav-el
	{
		margin-top: 15px;
	}

	&:hover
	{
		background: linear-gradient(180deg, fade(@_adminNav-strongColor, 7.5%), transparent 30%);
		border-top-color: fade(@_adminNav-strongColor, 10%);
		.m-transition();

		> span,
		> a
		{
			> .far, > .fal, > .fas
			{
				color: fade(@_adminNav-strongColor, 10%);

				top: 10px;
				right: .1em;

				.m-transition();
			}
		}
	}
}

.p-nav-subList
{
	.m-listPlain();
}

.p-nav-el
{
	> span,
	> a
	{
		display: block;
		padding: 6px 10px;
		color: @_adminNav-color;
		font-size: @xf-fontSizeSmaller;
		.m-transition();

		&:hover
		{
			background: @_adminNav-activelElBg;
			text-decoration: none;
		}
	}

	&.is-active > a
	{
		background: @_adminNav-activelElBg;
		border-right: 4px solid @_adminNav-selectedBorder;
	}
}

@media (max-width: @_adminNav-hideWidth)
{
	.p-nav
	{
		display: none;
		position: fixed;
		top: 0;
		bottom: 0;
		left: 0;
		right: 0;
		z-index: @zIndex-7;
		padding: 0;
		background: none;
		height: 100vh;
		width: auto;
		overflow: auto;

		-webkit-tap-highlight-color: rgba(0, 0, 0, 0);

		.p-nav-sectionLink,
		.p-nav-sectionToggle
		{
			//padding: 10px;
		}
	}

	.p-nav-tester
	{
		// let\'s us know in JS when this breakpoint has triggered
		font-family: \'off-canvas\';
	}

	.p-nav-listRoot
	{
		padding-bottom: 0;
	}
}

// ############################ OFF CANVAS SIDEBAR VARIANT ####################

.offCanvasMenu--adminNav
{
	.offCanvasMenu-content
	{
		background: @_adminNav-bg;
	}

	.offCanvasMenu-header
	{
		background: xf-intensify(@_adminNav-bg, 4%);
		border-bottom-color: xf-intensify(@_adminNav-bg, 8%);
		color: @_adminNav-color;
	}
}

// ###################################### MAIN COLUMN #########################

.p-main
{
	min-height: 100vh;
	vertical-align: top;
	padding-top: @_adminHeader-height;
	flex-grow: 1;
	min-width: 0;
}

.p-main-inner
{
	.m-pageWidth();
	.m-pageInset();
}

.p-breadcrumbs
{
	.m-listPlain();
	.m-clearFix();

	margin-bottom: 5px;
	line-height: 1.5;

	> li
	{
		float: left;
		margin-right: .5em;
		font-size: @xf-fontSizeSmall;

		a
		{
			display: inline-block;
			color: @xf-textColorMuted;
			vertical-align: bottom;
			max-width: 300px;
			.m-overflowEllipsis();
		}

		&:after,
		&:before
		{
			.m-faBase();
			font-size: 90%;
			color: @xf-textColorMuted;
		}

		&:after
		{
			.m-faContent(@fa-var-angle-right, .5em, ltr);
			.m-faContent(@fa-var-angle-left, .5em, rtl);
			margin-left: .5em;
		}

		&:last-child
		{
			margin-right: 0;

			a
			{
				font-weight: @xf-fontWeightHeavy;
			}
		}
	}
}

.p-main-header
{
	margin-bottom: ((@xf-elementSpacer) / 2);
}

.p-title
{
	display: flex;
	flex-wrap: wrap;
	align-items: center;
	max-width: 100%;
	margin-bottom: -5px;

	&.p-title--noH1
	{
		flex-direction: row-reverse;
	}
}

.p-title-value
{
	padding: 0;
	margin: 0 0 5px 0;
	font-size: @xf-fontSizeLargest;
	font-weight: @xf-fontWeightNormal;
	min-width: 0;
	margin-right: auto;
}

.p-title-pageAction
{
	margin-bottom: 5px;
}

.p-description
{
	margin: 5px 0 0;
	padding: 0;
	font-size: @xf-fontSizeSmall;
	color: @xf-textColorMuted;
}

.p-content
{
	margin: 0;

	> :first-child
	{
		margin-top: 0;
	}
	> :last-child
	{
		margin-bottom: 0;
	}
}

@media (max-width: @_adminNav-hideWidth)
{
	.p-main
	{
		display: block;
		height: auto;
		min-height: 100vh;
	}
}

@media (max-width: @xf-responsiveMedium)
{
	.p-breadcrumbs > li a
	{
		max-width: 200px;
	}
}

@media (max-width: @xf-responsiveNarrow)
{
	.p-breadcrumbs
	{
		> li
		{
			display: none;
			font-size: @xf-fontSizeSmallest;

			&:last-child
			{
				display: block;
			}

			a
			{
				max-width: 90vw;
			}

			&:after
			{
				display: none;
			}

			&:before
			{
				.m-faContent(@fa-var-chevron-left, .72em, ltr);
				.m-faContent(@fa-var-chevron-right, .72em, rtl);
				margin-right: .5em;
			}
		}
	}

	.p-title-value
	{
		font-size: @xf-fontSizeLarger;
	}
}

// ####################################### FOOTER AREA ########################

@_adminFooter-bg: xf-intensify(@_adminHeader-bg, 12%);
@_adminFooter-color: @xf-paletteColor2;
@_adminFooter-linkColor: @xf-paletteColor1;

.p-footer
{
	background: @_adminFooter-bg;
	border-top: @xf-borderSize solid xf-intensify(@_adminFooter-bg, 4%);
	color: @_adminFooter-color;
	font-size: @xf-fontSizeSmall;

	.m-pageInset();

	a
	{
		color: @_adminFooter-linkColor;
	}
}

.p-footer-row
{
	.m-clearFix();

	margin-bottom: -@xf-paddingLarge;

	a
	{
		padding: 2px 4px;
		border-radius: @xf-borderRadiusSmall;

		&:hover
		{
			text-decoration: none;
			background-color: fade(@_adminFooter-linkColor, 10%);
		}
	}
}

.p-footer-row-main
{
	float: left;
	margin-bottom: @xf-paddingLarge;
	margin-left: -2px;
}

.p-footer-row-opposite
{
	float: right;
	margin-bottom: @xf-paddingLarge;
	margin-right: -2px;
}

.p-footer-copyright
{
	margin-top: @xf-paddingLarge;
	text-align: center;
	font-size: @xf-fontSizeSmallest;
}

.p-footer-debug
{
	margin-top: @xf-paddingLarge;
	text-align: right;
	font-size: @xf-fontSizeSmallest;

	.pairs > dt { color: inherit; }
}

.p-footer-version {}

@media (max-width: @xf-responsiveMedium)
{
	.p-footer-row
	{
		margin-bottom: @xf-paddingLarge;
	}

	.p-footer-row-main,
	.p-footer-row-opposite
	{
		float: none;
		display: inline;
	}

	.p-footer-copyright
	{
		text-align: left;
		padding: 0 4px; // aligns with other links
	}
}

// ##################################### QUICK SEARCH RESULTS ################

.p-quickSearchResultsWrapper
{
	&.is-active
	{
		border-top: @xf-borderSize solid @xf-borderColor;
	}
}

.p-quickSearchResults
{
	.m-transitionFadeDown();
}

.p-quickSearchResultSet
{
	margin: 0;
	padding: 0;

	display: table;
	width: 100%;
	table-layout: fixed;

	> dt
	{
		display: table-cell;
		width: 140px;
		padding: @xf-paddingMedium;
		margin: 0;
		background: @xf-contentAltBg;
		border-right: @xf-borderSize solid @xf-borderColor;
		text-align: right;
		font-size: @xf-fontSizeSmall;
	}

	> dd
	{
		display: table-cell;
		padding: 0;
		margin: 0;

	}

	@media (max-width: 400px)
	{
		display: block;

		> dt
		{
			display: block;
			width: auto;
			text-align: left;
			font-weight: @xf-fontWeightHeavy;
			border-right: none;
			border-bottom: @xf-borderSize solid @xf-borderColorFaint;
		}

		> dd
		{
			display: block;
			border-bottom: @xf-borderSize solid @xf-borderColorHeavy;
		}

		&:last-child > dd
		{
			border-bottom: none;
		}
	}
}

.p-quickSearchResultList
{
	.m-listPlain();

	a
	{
		display: block;
		padding: @xf-paddingMedium;
		font-size: @xf-fontSizeSmall;

		&:hover,
		&.is-active
		{
			text-decoration: none;
			background: @xf-contentHighlightBg;
		}

		span
		{
			font-size: @xf-fontSizeSmaller;
			color: fade(@xf-textColorMuted, 75%);
		}
	}
}

// ##################################### ADMIN LOGIN #####################

@_adminLogin-bg: @xf-paletteColor5;
@_adminLogin-inputColor: @xf-paletteColor1;
@_adminLogin-color: @xf-paletteColor2;

.p-adminLogin
{
	min-height: 100%;
	background: @_adminLogin-bg;
	color: @_adminLogin-color;

	.m-textColoredLinks();

	.adminLogin-wrapper
	{
		display: flex;
		padding: 2vh 2vw;
		height: 100vh;
		min-height: 300px;
		max-width: 100%;
		align-items: center;
		justify-content: center;
	}

	.adminLogin-content
	{
		width: 100%;
		max-width: 400px;

		&.adminLogin-content--wide
		{
			max-width: 800px;
		}
	}

	.adminLogin-contentForm
	{
		background: xf-intensify(@_adminLogin-bg, 3%);
		border: 1px solid xf-intensify(@_adminLogin-bg, 6%);
		padding: 20px 40px;
		border-radius: 10px;
		color: @_adminLogin-color;

		.m-dropShadow(0, 5px, 20px);
	}

	.adminLogin-row
	{
		margin: 20px 0;

		> dt
		{
			margin-bottom: 5px;
			display: none;
		}
		> dd
		{
			margin: 0;
			position: relative;

			input
			{
				border: none;
				padding: 10px;
				padding-left: 30px;
				background: xf-intensify(@_adminLogin-bg, 6%);
				font-weight: @xf-fontWeightHeavy;
				font-size: 16px;
				color: @_adminLogin-inputColor;
				.m-placeholder({color: fade(@_adminLogin-color, 60%); });

				& + .far,
				& + .fal,
				& + .fas
				{
					position: absolute;
					top: 0;
					left: 0;
					bottom: 0;
					width: 30px;
					display: flex;
					align-items: center;
					justify-content: center;

					color: @_adminLogin-color;
					font-size: 16px;
					line-height: @xf-lineHeightDefault;
					text-shadow: 1px 1px 2px rgba(0, 0, 0, .5);
				}

				&:-webkit-autofill
				{
					+ .far,
					+ .fal,
					+ .fas
					{
						color: black;
					}

					~ .inputGroup-text
					{
						color: black;
						background: rgb(250, 255, 189); // this is the color used by Chrome autofill
						border-color: transparent;
					}
				}
			}

			.inputGroup-text.inputGroup-text // ugly specificity hack ;)
			{
				border: none;
				border-left: @xf-borderSize solid xf-intensify(@_adminLogin-bg, 9%);
				background: xf-intensify(@_adminLogin-bg, 6%);
				color: fade(@_adminLogin-color, 80%);
			}
		}
	}

	.adminLogin-row--submit
	{
		//text-align: right;

		.button
		{
			width: 100%;
		}
	}

	.adminLogin-boardTitle
	{
		text-align: center;
		font-size: @xf-fontSizeSmaller;
		margin-top: 5px;
		color: fade(@_adminLogin-color, 50%);
	}

	.adminLogin-debug
	{
		text-align: center;
		margin: 2em auto 0;
		font-size: @xf-fontSizeSmallest;
		.m-hiddenLinks();

		.pairs > dt { color: inherit; }
	}
}

// ----------------

.p-runJob
{
	min-height: 100%;
}

.p-runJobContent
{
	width: 600px;
	max-width: 100%;
	margin: 10px auto 10px;
	margin-top: 10vh;
	padding: 0 10px;
}

.p-runJobTitle
{
	margin: 0;
	padding: 0;
	font-size: @xf-fontSizeLargest;
	font-weight: @xf-fontWeightNormal;
}

.p-runJobDebug
{
	text-align: center;
	margin: 10px auto 0;
	font-size: @xf-fontSizeSmallest;
}

// ################################## MISC STYLES ########################

.iconicLinks
{
	margin-bottom: @xf-elementSpacer;
}

.iconicLinks-list
{
	.m-listPlain();
	margin-bottom: -@xf-paddingMedium;
	margin-right: -@xf-paddingMedium;
	font-size: 0;

	display: flex;
	flex-flow: row wrap;

	> li
	{
		flex: auto;

		width: 160px;
		max-width: 320px;

		position: relative;
		overflow: hidden;

		margin-right: @xf-paddingMedium;
		margin-bottom: @xf-paddingMedium;
		vertical-align: top;
		text-align: center;
		font-size: @xf-fontSizeNormal;
		.xf-contentBase();
		.xf-blockBorder();
		border-radius: @xf-blockBorderRadius;

		&.iconicLinks-placeholder
		{
			margin-top: 0;
			margin-bottom: 0;
			height: 0;
			border: none;
		}

		> a
		{
			display: block;
			padding: @xf-paddingMedium;
			background: fade(@xf-contentHighlightBg, 0%);
			.m-transition();

			&:hover
			{
				background: @xf-contentHighlightBg;
				text-decoration: none;

				.iconicLinkList-icon
				{
					opacity: 1;
				}
			}
		}
	}
}

.iconicLinks-icon
{
	font-size: 3em;
	opacity: .6;
	.m-transition(opacity);
}

.iconicLinks-title
{
	.m-overflowEllipsis();
}

.graphList
{
	.m-listPlain();
	display: flex;
	flex-wrap: wrap;
	justify-content: center;

	> li
	{
		width: 50%;
	}

	@media (max-width: @xf-responsiveMedium)
	{
		display: block;

		> li
		{
			width: 100%;
		}
	}
}

.nodeIcon
{
	.m-faBase();
	color: @xf-textColorDimmed;

	&--Forum:before
	{
		.m-faContent(@fa-var-comments);
	}

	&--Category:before
	{
		.m-faContent(@fa-var-bars);
	}

	&--LinkForum:before
	{
		.m-faContent(@fa-var-link);
	}

	&--Page:before
	{
		.m-faContent(@fa-var-file-alt);
	}
}';
	return $__finalCompiled;
});