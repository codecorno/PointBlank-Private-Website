<?php
// FROM HASH: 7727cc4faa86b69b7a499f81a430f2f3
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '// ############################ BLOCK LINKS ##################

.blockLink
{
	display: block;
	padding: @xf-blockPaddingV @xf-blockPaddingH;
	.xf-blockLink();

	&.is-selected
	{
		.xf-blockLinkSelected(no-border);
		border-left: @xf-blockLinkSelected--border-width solid @xf-blockLinkSelected--border-color;
		padding-left: (@xf-blockPaddingH - xf-default(@xf-blockLinkSelected--border-width, 0));
	}

	&:hover
	{
		.xf-blockLinkSelected(background);
		text-decoration: inherit;
	}
}

.blockLink-desc
{
	display: block;
	color: @xf-textColorMuted;
	font-size: @xf-fontSizeSmaller;
	font-weight: @xf-fontWeightNormal;
}

.blockLinkSplitToggle
{
	display: flex;
	padding: 0;
	text-decoration: none;
	cursor: pointer;

	&.is-selected
	{
		.xf-blockLinkSelected(no-border);
	}

	&:hover
	{
		.xf-blockLinkSelected(background);
		text-decoration: inherit;
	}
}

.blockLinkSplitToggle-link
{
	display: block;
	padding: @xf-blockPaddingV @xf-blockPaddingH;
	text-decoration: none;
	flex-grow: 1;

	&:hover
	{
		text-decoration: none;
	}

	.blockLinkSplitToggle.is-selected &
	{
		border-left: @xf-blockLinkSelected--border-width solid @xf-blockLinkSelected--border-color;
		padding-left: (@xf-blockPaddingH - xf-default(@xf-blockLinkSelected--border-width, 0));
	}
}

.blockLinkSplitToggle-toggle
{
	display: inline-block;
	padding: @xf-blockPaddingV @xf-blockPaddingH;
	text-decoration: none;
	flex-grow: 0;
	line-height: 1;

	&:hover
	{
		text-decoration: none;
	}

	&:after
	{
		.m-faBase();
		font-size: 80%;
		.m-faContent(@fa-var-chevron-down);
	}

	&.is-active:after
	{
		.m-faContent(@fa-var-chevron-up);
	}
}

.blockLink--iconic
{
	i:after
	{
		.m-faBase();
		display: inline-block;
		min-width: 1em;
		position: absolute;
		left: @xf-blockPaddingH;
		top: 8px;
		display: none !important;
	}

	&--started i:after
	{
		.m-faContent(@fa-var-file-alt);
	}
	&--contributed i:after
	{
		.m-faContent(@fa-var-comments);
	}
	&--watched i:after
	{
		.m-faContent(@fa-var-bookmark);
	}
	&--unanswered i:after
	{
		.m-faContent(@fa-var-question-circle);
	}
}

// ################################ FAUX BLOCK LINKS #######################
// concept from https://codepen.io/BPScott/pen/Erwan and http://codepen.io/IschaGast/pen/Qjxpxo
// z-indexes are bumped to have the link sit on top of positioned elements (without z-index)

.fauxBlockLink
{
	position: relative;

	a,
	.fauxBlockLink-link
	{
		position: relative;
		z-index: 2;
	}

	.fauxBlockLink-blockLink
	{
		position: static;

		&:before
		{
			content: \'\';
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			z-index: 1;
		}
	}

	&.fauxBlockLink--noHover
	{
		.fauxBlockLink-blockLink:hover
		{
			text-decoration: none;
		}
	}
}';
	return $__finalCompiled;
});