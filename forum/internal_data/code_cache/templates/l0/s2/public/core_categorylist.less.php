<?php
// FROM HASH: b17e39ff7adc917fb859fc27183aab8e
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '// ################################ CATEGORY LIST #######################

@_categoryListTogglerWidth: 1em;
@_categoryListTogglerPaddingH: @xf-paddingMedium;
@_categoryListItemPaddingV: @xf-nlBlockLinkPaddingV;

.categoryList
{
	display: none;
	.m-listPlain();

	&.is-active
	{
		display: block;
	}
}

.categoryList-item
{
	padding: 0;
	text-decoration: none;
	font-size: @xf-fontSizeNormal;

	&.categoryList-item--small
	{
		font-size: @xf-fontSizeSmall;
	}

	.categoryList
	{
		padding-left: @xf-paddingLarge;
	}
}

.categoryList-itemDesc
{
	display: block;
	font-size: @xf-fontSizeSmaller;
	font-weight: @xf-fontWeightNormal;
	color: @xf-textColorMuted;
	margin-top: -@xf-blockPaddingV;

	.m-overflowEllipsis();
}

.categoryList-header
{
	padding: @xf-blockPaddingV 0;
	margin: 0;
	color: @xf-textColorFeature;
	text-decoration: none;
	font-weight: @xf-fontWeightHeavy;

	&.categoryList-header--muted
	{
		color: @xf-textColorMuted;
	}

	.m-clearFix();
	.m-hiddenLinks();
}

.categoryList-itemRow
{
	display: flex;
	min-width: 0;
}

.categoryList-link
{
	display: block;
	flex-grow: 1;
	padding: @_categoryListItemPaddingV @_categoryListTogglerPaddingH;
	text-decoration: none;

	.m-overflowEllipsis();

	&:hover
	{
		text-decoration: none;
	}

	&.is-selected
	{
		font-weight: @xf-fontWeightHeavy;
	}

	.categoryList-toggler + &,
	.categoryList-togglerSpacer + &
	{
		padding-left: 0;
	}
}

.categoryList-label
{
	margin-left: auto;
	align-self: center;
	padding-right: @_categoryListTogglerPaddingH;
}

.categoryList-toggler
{
	display: inline-block;
	padding: @_categoryListItemPaddingV @_categoryListTogglerPaddingH;
	text-decoration: none;
	flex-grow: 0;
	line-height: normal;

	&:hover
	{
		text-decoration: none;
	}

	&:after
	{
		.m-faBase();
		font-size: 80%;
		.m-faContent(@fa-var-chevron-down, @_categoryListTogglerWidth);
	}

	&.is-active:after
	{
		.m-faContent(@fa-var-chevron-up, @_categoryListTogglerWidth);
	}
}

.categoryList-togglerSpacer
{
	display: inline-block;
	visibility: hidden;
	padding: @_categoryListItemPaddingV @_categoryListTogglerPaddingH;

	&:after
	{
		.m-faBase();
		font-size: 80%;
		.m-faContent(@fa-var-chevron-down, @_categoryListTogglerWidth);
	}
}

';
	if ($__templater->func('property', array('nlBlockLinkType', ), false) == 'lineHeight') {
		$__finalCompiled .= '
.categoryList-link
{
	padding: 0;
	color: inherit;
}
.categoryList-toggler
{
	padding: 0 @_categoryListTogglerPaddingH 0 0 ;
    height: @xf-nlBlockLinkHeight;
    line-height: (@xf-nlBlockLinkHeight - 2);
	color: inherit;
}
.categoryList-togglerSpacer
{
	padding: @_categoryListItemPaddingV @_categoryListTogglerPaddingH 0 0;
	
	.toggleTarget .toggleTarget & {
		padding-left: @_categoryListTogglerPaddingH;
	}
}
';
	}
	return $__finalCompiled;
});