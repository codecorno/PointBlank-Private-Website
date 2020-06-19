<?php
// FROM HASH: 8dfb978900bb710610e99eae1de7d27b
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '// #################################### INLINE MOD BUTTON ################

.inlineModButton
{
	display: inline-block;
	position: relative;

	i:before
	{
		display: inline-block;
		.m-faBase();
		.m-faContent(@fa-var-check-square, .88em);
		vertical-align: -1px;
		font-size: 1.2em;
		margin: -.2em 0;
		line-height: 1;
	}

	&.inlineModButton--noIcon i
	{
		display: none;
	}

	&.is-mod-active
	{
		color: @xf-textColorAttention;

		.inlineModButton-count
		{
			display: inline;
		}
	}

	&:not(.inlineModButton--withLabel)
	{
		.inlineModButton-label
		{
			.m-visuallyHidden();
		}
	}
}

.inlineModButton-count
{
	display: none;
	position: absolute;
	top: -3px;
	left: -6px;
	.xf-contentAccentBase();
	border: @xf-borderSize solid @xf-borderColorAccentContent;
	border-radius: @xf-borderRadiusSmall;
	padding: 1px 2px;
	font-size: 9px;
	line-height: 1;

	.inlineModButton--noIcon &
	{
		position: static;
		font-size: 80%;
		padding-top: 0;
		padding-bottom: 0;
	}
}

// #################################### INLINE MOD BAR ################

.inlineModBar
{
	.xf-inlineModBar();
	.m-transitionFadeDown();
	.m-clearFix();

	.input,
	.button
	{
		padding-top: @xf-paddingSmall;
		padding-bottom: @xf-paddingSmall;
	}

	&.is-active:first-of-type
	{
		.m-dropShadow(0, 0, 8px, 3px, 0.3);
	}
}

.inlineModBar-inner
{
	.m-pageWidth();
	.m-pageInset();

	display: flex;
	align-items: center;
}

.inlineModBar-controls
{
	.m-listPlain();
	margin-right: auto;

	display: flex;
	align-items: center;
	flex-wrap: wrap;
	min-height: 35px;
	max-width: 100%;

	> li
	{
		float: left;
		display: inline-block;
		margin-right: 1em;

		&:last-child
		{
			margin-right: 0;
		}
	}
}

.inlineModBar-close
{
	float: right;
	margin-left: 1em;
	order: 2;
}

.inlineModBar-title
{
	font-weight: @xf-fontWeightHeavy;
}

.button.inlineModBar-goButton
{
	font-size: @xf-fontSizeNormal;
	line-height: 1.5; // matches select
	min-width: 0;
}

.inlineModBarCover
{
	position: fixed;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	z-index: -1;
	cursor: pointer;
}';
	return $__finalCompiled;
});