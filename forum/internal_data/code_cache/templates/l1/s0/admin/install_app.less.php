<?php
// FROM HASH: c0d950d049f1692af62e3cfcc669b191
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '@_installPage-maxWidth: 1100px; // this does not include the navigation sidebar

.mixin-pageWidth()
{
	width: 100%;
	max-width: @_installPage-maxWidth;
	margin: 0 auto;
}

// ##################################### HEADER ###############################

@_installHeader-bg: @xf-paletteColor5;
@_installHeader-height: 40px;
@_installHeader-shadowHeight: 8px;
@_installHeader-buttonPaddingH: 10px;

.p-header
{
	position: fixed;
	top: 0;
	left: 0;
	right: 0;
	height: @_installHeader-height;
	line-height: @_installHeader-height;
	.m-clearFix();
	z-index: @zIndex-4;
	background: @_installHeader-bg;
	color: contrast(@_installHeader-bg);
	text-align: center;
	.m-dropShadow(0, 0, @_installHeader-shadowHeight, 3px, 0.3);
}

.p-header-logo
{
	vertical-align: middle;
	margin-right: auto;
}

// ##################################### BODY AREA SETUP ##########################

.p-pageWrapper
{
	position: relative;
	display: flex;
	flex-direction: column;
	min-height: 100vh;
}

.p-body
{
	display: flex;
	align-items: stretch;
	flex-grow: 1;
}


// ###################################### MAIN COLUMN #########################

.p-main
{
	vertical-align: top;
	padding-top: @_installHeader-height;
	flex-grow: 1;
	min-width: 0;
}

.p-main-inner
{
	.mixin-pageWidth();
	padding: @xf-paddingLarge @xf-pageEdgeSpacer;
}

.p-title
{
	display: flex;
	flex-wrap: wrap;
	align-items: center;
	max-width: 100%;
	margin-bottom: 5px;
}

.p-title-value
{
	padding: 0;
	margin: 0 0 3px 0;
	font-size: @xf-fontSizeLargest;
	font-weight: @xf-fontWeightNormal;
	min-width: 0;
}

.p-description
{
	margin: 0;
	padding: 0;
	font-size: @xf-fontSizeSmall;
	color: @xf-textColorMuted;
}

.p-content
{
	margin: @xf-paddingLarge 0;

	> :first-child
	{
		margin-top: 0;
	}
	> :last-child
	{
		margin-bottom: 0;
	}
}

// ####################################### FOOTER AREA ########################

@_adminFooter-bg: darken(@_installHeader-bg, 12%);
@_adminFooter-color: @xf-paletteColor2;
@_adminFooter-linkColor: @xf-paletteColor1;

.p-footer
{
	background: @_adminFooter-bg;
	border-top: @xf-borderSize solid darken(@_adminFooter-bg, 4%);
	color: @_adminFooter-color;
	font-size: @xf-fontSizeSmall;
	padding: @xf-paddingLarge @xf-pageEdgeSpacer;

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
}';
	return $__finalCompiled;
});