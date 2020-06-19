<?php
// FROM HASH: 1b420ad2f3c73d4efdccd18fbf176a1e
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '.bbWrapper
{
	// This approach is needed to normalize browser differences that normalize.css won\'t handle within BB code/rich text
	// sections. You may need higher specificity to override some situations because of this.

	ol:not(.is-structureList),
	ul:not(.is-structureList)
	{
		margin-top: 1em;
		margin-bottom: 1em;
		overflow: hidden;
	}

	ol:not(.is-structureList) ol:not(.is-structureList),
	ol:not(.is-structureList) ul:not(.is-structureList),
	ul:not(.is-structureList) ol:not(.is-structureList),
	ul:not(.is-structureList) ul:not(.is-structureList)
	{
		margin-top: 0;
		margin-bottom: 0;
	}
}

.bbImage
{
	max-width: 100%;
}

// classes to handle images being floated left and right via BB code attributes
.bbImage,
.lbContainer--inline
{
	&.bbImageAligned--left
	{
		float: left;
		margin: @xf-bbCodeImgFloatMargin @xf-bbCodeImgFloatMargin @xf-bbCodeImgFloatMargin 0;
	}

	&.bbImageAligned--right
	{
		float: right;
		margin: @xf-bbCodeImgFloatMargin 0 @xf-bbCodeImgFloatMargin @xf-bbCodeImgFloatMargin;
	}
}

.bbMediaWrapper,
.bbMediaJustifier
{
	width: 560px;
	max-width: 100%;
	margin: 0;

	&.fb_iframe_widget
	{
		display: block;
	}

	// we want this to still be a block element but to inherit the alignment a user has set - this approximates that
	[style="text-align: center"] &
	{
		margin-left: auto;
		margin-right: auto;
	}

	[style="text-align: left"] &
	{
		-ltr-rtl-margin-left: 0;
		-ltr-rtl-margin-right: auto;
	}

	[style="text-align: right"] &
	{
		-ltr-rtl-margin-left: auto;
		-ltr-rtl-margin-right: 0;
	}

	&.bbImageAligned--left
	{
		float: left;
		margin: @xf-bbCodeImgFloatMargin @xf-bbCodeImgFloatMargin @xf-bbCodeImgFloatMargin 0;
	}

	&.bbImageAligned--right
	{
		float: right;
		margin: @xf-bbCodeImgFloatMargin 0 @xf-bbCodeImgFloatMargin @xf-bbCodeImgFloatMargin;
	}
}

.bbMediaWrapper-inner
{
	position: relative;
	padding-bottom: 56.25%; /* 16:9 ratio */
	height: 0;

	&.bbMediaWrapper-inner--4to3
	{
		padding-bottom: 75%; /* 4:3 ratio */
	}

	&.bbMediaWrapper-inner--104px
	{
		padding-bottom: 104px;
	}

	&.bbMediaWrapper-inner--110px
	{
		padding-bottom: 110px;
	}

	&.bbMediaWrapper-inner--500px
	{
		padding-bottom: 500px;
	}

	iframe,
	object,
	embed,
	video,
	audio,
	.bbMediaWrapper-fallback
	{
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
	}
}

.bbMediaWrapper-fallback
{
	display: flex;
	justify-content: center;
	align-items: center;
	max-width: 100%;
	.xf-minorBlockContent();
}

.bbOembed
{
	margin: auto;
	width: 500px;
	max-width: 100%;

	&.bbOembed--loaded
	{
		display: block;
	}

	.reddit-card
	{
		margin: 0;
	}
}

.bbTable
{
	max-width: 100%;
	overflow: auto;

	.m-tableBase();
}

.bbCodePlainUnfurl
{
	&.link
	{
		display: block;
	}
}';
	return $__finalCompiled;
});