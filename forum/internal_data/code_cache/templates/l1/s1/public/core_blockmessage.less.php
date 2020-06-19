<?php
// FROM HASH: a85e7ee6de5d4228764107b84858ea73
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '// ############################ BLOCK MESSAGE ###################

.block-rowMessage
{
	margin: @xf-blockPaddingV 0;
	padding: @xf-blockPaddingV @xf-blockPaddingH;
	.xf-blockBorder();
	border-radius: @xf-blockBorderRadius;

	.m-clearFix();

	&:first-child
	{
		margin-top: 0;
	}

	&:last-child
	{
		margin-bottom: 0;
	}

	&.block-rowMessage--small
	{
		font-size: @xf-fontSizeSmall;
		padding: @xf-blockPaddingV/2 @xf-blockPaddingH/2;
	}

	&.block-rowMessage--center
	{
		text-align: center;
	}

	.m-blockMessageVariants();
}

.blockMessage
{
	margin-bottom: @xf-elementSpacer;
	padding: @xf-blockPaddingV @xf-blockPaddingH;
	.xf-contentBase();
	.xf-blockBorder();
	border-radius: @xf-blockBorderRadius;

	.m-clearFix();

	//.m-transition(border, margin;); // edgeSpacerRemoval

	&.blockMessage--none
	{
		background: none;
		border: none;
		color: @xf-textColor;
		padding: 0;
	}

	&.blockMessage--close
	{
		margin-top: 5px;
		margin-bottom: 5px;
	}

	&.blockMessage--small
	{
		font-size: @xf-fontSizeSmall;
		padding: @xf-blockPaddingV/2 @xf-blockPaddingH/2;
	}

	&.blockMessage--center
	{
		text-align: center;
	}

	.m-blockMessageVariants();
}

.blockMessage--iconic,
.block-rowMessage--iconic
{
	text-align: left;
	position: relative;
	padding-left: 4em;
	min-height: 4em;

	&:before
	{
		.m-faBase();
		font-size: 280%; // 2 x line height
		position: absolute;
		top: 0;
		left: @xf-blockPaddingV;
	}
}

.m-blockMessageVariants()
{
	// note: the double && is correct here -- it enforces output like ".x.x--variant". The extra specificity helps
	// prevent issues from things like media query overrides.

	&&--highlight
	{
		.xf-contentHighlightBase();
	}
	&--highlight&--iconic:before
	{
		.m-faContent(@fa-var-info-circle);
	}

	&&--important
	{
		.xf-contentAccentBase();
		border-left: @xf-borderSizeFeature solid @xf-borderColorAttention;

		a { .xf-contentAccentLink(); }
	}
	&--important&--iconic:before
	{
		.m-faContent(@fa-var-exclamation-circle);
		color: @xf-importantFeatureColor;
	}

	&&--success
	{
		border-left: @xf-borderSizeFeature solid @xf-successFeatureColor;
		background: @xf-successBg;
		color: @xf-successColor;

		.m-textColoredLinks();
	}
	&--success&--iconic:before
	{
		.m-faContent(@fa-var-check-circle);
		color: @xf-successFeatureColor;
	}

	&&--warning
	{
		border-left: @xf-borderSizeFeature solid @xf-warningFeatureColor;
		background: @xf-warningBg;
		color: @xf-warningColor;

		.m-textColoredLinks();
	}
	&--warning&--iconic:before
	{
		.m-faContent(@fa-var-exclamation-triangle);
		color: @xf-warningFeatureColor;
	}

	&&--error
	{
		border-left: @xf-borderSizeFeature solid @xf-errorFeatureColor;
		background: @xf-errorBg;
		color: @xf-errorColor;

		.m-textColoredLinks();
	}
	&--error&--iconic:before
	{
		.m-faContent(@fa-var-times-circle);
		color: @xf-errorFeatureColor;
	}
}';
	return $__finalCompiled;
});