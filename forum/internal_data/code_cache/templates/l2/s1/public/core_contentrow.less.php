<?php
// FROM HASH: bd8df75c025e754e6d1e9c74c8254802
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '// ########################################### CONTENT ROWS ############################

@_contentRow-faderHeight: 150px;
@_contentRow-faderCoverHeight: (@_contentRow-faderHeight) / 2;

.contentRow
{
	display: flex;

	&.contentRow--alignMiddle
	{
		align-items: center;
	}

	&.is-deleted
	{
		opacity: .7;

		.contentRow-header,
		.contentRow-title
		{
			text-decoration: line-through;
		}
	}
}

.m-figureFixed(@size)
{
	width: @size;

	img,
	i.fa,
	i.fal,
	i.far,
	i.fas,
	i.fab,
	.avatar
	{
		max-height: @size;
	}
}

.contentRow-figure
{
	vertical-align: top;
	white-space: nowrap;
	word-wrap: normal;
	text-align: center;

	img,
	i.fa,
	i.fal,
	i.far,
	i.fas,
	i.fab,
	.avatar
	{
		vertical-align: bottom;
	}

	&.contentRow-figure--fixedBookmarkIcon
	{
		.m-figureFixed(48px);
	}

	&.contentRow-figure--fixedSmall
	{
		.m-figureFixed(60px);
	}

	&.contentRow-figure--fixedMedium
	{
		.m-figureFixed(100px);

		&.contentRow-figure--fixedMedium--fluidWidth
		{
			width: auto;
			max-width: 200px;
		}
	}

	&.contentRow-figure--fixedLarge
	{
		.m-figureFixed(200px);
	}

	&.contentRow-figure--text
	{
		font-size: @xf-fontSizeLargest;
	}
}

.contentRow-figureContainer
{
	position: relative;

	.contentRow-figureSeparated
	{
		position: absolute;
		right: -5px;
		bottom: -5px;

		.m-avatarSize(@avatar-s / 2 + 2px);
	}
}

.contentRow-figureIcon
{
	text-align: center;
	color: @xf-textColorFeature;


	img,
	i.fa,
	i.fal,
	i.far,
	i.fas,
	i.fab
	{
		width: 64px;
		overflow: hidden;
		white-space: nowrap;
		word-wrap: normal;
		border-radius: @xf-borderRadiusMedium;

		.contentRow-figure--fixedBookmarkIcon &
		{
			width: 48px;
		}
	}
}

.contentRow-main
{
	flex: 1;
	min-width: 0;
	vertical-align: top;
	padding-left: @xf-paddingLarge;

	&:before
	{
		// because of line height, there appears to be extra space at the top of this
		content: \'\';
		display: block;
		margin-top: -.18em;
	}

	&.contentRow-main--close
	{
		padding-left: @xf-paddingMedium;
	}

	&:first-child
	{
		padding-left: 0;
	}
}

.contentRow-header
{
	margin: 0;
	padding: 0;
	font-weight: @xf-fontWeightHeavy;
	font-size: @xf-fontSizeLarge;
}

.contentRow-title
{
	margin: 0;
	padding: 0;
	font-weight: @xf-fontWeightNormal;
	font-size: @xf-fontSizeLarge;
}

.contentRow-snippet
{
	font-size: @xf-fontSizeSmall;
	font-style: italic;
	margin: .25em 0;
}

.contentRow-muted
{
	color: @xf-textColorMuted;
}

.contentRow-lesser
{
	font-size: @xf-fontSizeSmall;
}

.contentRow-suffix
{
	padding-left: @xf-paddingMedium;
	white-space: nowrap;
	word-wrap: normal;
}

.contentRow-faderContainer
{
	position: relative;
	overflow: hidden;
}

.contentRow-faderContent
{
	max-height: 150px;
	overflow: hidden;
}

.contentRow-fader
{
	position: absolute;
	top: (@_contentRow-faderHeight) + ((@xf-paddingMedium) * 2) - (@_contentRow-faderCoverHeight);
	left: 0;
	right: 0;
	height: @_contentRow-faderCoverHeight;

	.m-gradient(fade(@xf-contentBg, 0%), @xf-contentBg, transparent, 0%, 80%);
}

.contentRow-minor
{
	font-size: @xf-fontSizeSmall;
	color: @xf-textColorMuted;

	&.contentRow-minor--hideLinks
	{
		.m-hiddenLinks();
	}

	&.contentRow-minor--smaller
	{
		font-size: @xf-fontSizeSmaller;
	}

	&.contentRow-minor--singleLine
	{
		.m-overflowEllipsis();
	}
}

.contentRow-spaced
{
	margin: .5em 0;

	&:last-child
	{
		margin-bottom: 0;
	}
}

.contentRow-extra
{
	float: right;
	padding-left: @xf-paddingMedium;
	font-size: @xf-fontSizeSmallest;

	&.contentRow-extra--small
	{
		font-size: @xf-fontSizeSmall;
		color: @xf-textColorMuted;
	}

	&.contentRow-extra--normal
	{
		font-size: @xf-fontSizeNormal;
		color: @xf-textColorMuted;
	}

	&.contentRow-extra--large
	{
		font-size: @xf-fontSizeLarge;
		color: @xf-textColorMuted;
	}

	&.contentRow-extra--larger
	{
		font-size: @xf-fontSizeLarger;
		color: @xf-textColorMuted;
	}

	&.contentRow-extra--largest
	{
		font-size: @xf-fontSizeLargest;
		color: @xf-textColorMuted;
	}
}

@media (max-width: @xf-responsiveNarrow)
{
	.contentRow-figure
	{
		&.contentRow-figure--fixedBookmarkIcon
		{
			width: @avatar-xs;
		}

		.avatar--s
		{
			.m-avatarSize(@avatar-xs);
		}
	}

	.contentRow--hideFigureNarrow
	{
		.contentRow-figure
		{
			display: none;
		}

		.contentRow-main
		{
			padding-left: 0;
		}
	}
}';
	return $__finalCompiled;
});