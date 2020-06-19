<?php
// FROM HASH: c26b923e719aa87fc4ca8bcb15e9b6dd
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '// ################################## OFF CANVAS MENU #########################

@_offCanvas-animationLength: (2 * (@xf-animationSpeed));

.offCanvasMenu
{
	display: none;
	position: fixed;
	top: 0;
	bottom: 0;
	left: 0;
	right: 0;
	z-index: @zIndex-5;
	.m-transition(none; @_offCanvas-animationLength); // needed to keep the children displayed through animation
	.m-transform(scale(1)); // forces instant repaint in iOS

	// every tap on iOS causes a brief highlight, disable it for off canvas menu
	// then restore it for some tappable elements to retain it
	-webkit-tap-highlight-color: rgba(0, 0, 0, 0);

	a
	{
		-webkit-tap-highlight-color: initial;
	}

	&.is-transitioning
	{
		display: block;
	}

	&.is-active
	{
		display: block;
	}

	.offCanvasMenu-hidden
	{
		display: none;
	}

	.offCanvasMenu-shown
	{
		display: block;
	}

	.offCanvasMenu-closer
	{
		float: right;
		cursor: pointer;
		text-decoration: none;
		-webkit-tap-highlight-color: initial;
		padding: @xf-paddingLarge;
		margin: -@xf-paddingLarge;

		&:hover
		{
			text-decoration: none;
		}

		&:after
		{
			.m-faBase();
			.m-faContent(@fa-var-times);
		}
	}

	.block-container,
	.blockMessage
	{
		margin-left: 0;
		margin-right: 0;
		border-radius: 0;
		border-left: none;
		border-right: none;
	}
}

.offCanvasMenu-shown
{
	display: none;
}

.offCanvasMenu-backdrop
{
	position: absolute;
	top: 0;
	bottom: 0;
	left: 0;
	right: 0;
	background: rgba(0, 0, 0, .25);
	opacity: 0;
	.m-transition(all; @_offCanvas-animationLength; ease-in-out);

	.is-active &
	{
		opacity: 1;
	}
}

.offCanvasMenu-content
{
	position: relative;
	width: 280px;
	max-width: 85%;
	height: 100%;
	padding-bottom: 44px;
	overflow: auto;
	.m-transition(all; @_offCanvas-animationLength; ease-in-out);
	-webkit-overflow-scrolling: touch;

	& when(@ltr)
	{
		.m-dropShadow(2px, 0, 5px, 0, .25);
		.m-transform(translateX(-280px));
	}

	& when(@rtl)
	{
		.m-dropShadow(-2px, 0, 5px, 0, .25);
		.m-transform(translateX(280px));
	}

	.is-active &
	{
		.m-transform(translateX(0));
	}

	.p-nav-content {
		margin-bottom: 96px;
	}
}

.offCanvasMenu-header
{
	padding: @xf-paddingLarge;
	margin: 0;
	font-size: @xf-fontSizeLarger;
	font-weight: @xf-fontWeightNormal;

	.m-clearFix();
	.m-hiddenLinks();

	&.offCanvasMenu-header--separated
	{
		margin-bottom: @xf-paddingLarge;
	}
}

.offCanvasMenu-row
{
	padding: @xf-paddingLarge;
}

.offCanvasMenu-separator
{
	padding: 0;
	margin: 0;
	border: none;
	border-top: 1px solid transparent;
}

.offCanvasMenu-link
{
	display: block;
	padding: @xf-paddingLarge;
	font-size: @xf-fontSizeLarge;
	text-decoration: inherit;

	&:hover
	{
		text-decoration: inherit;
	}

	&.offCanvasMenu-link--splitToggle
	{
		position: relative;
		text-decoration: inherit;

		&:before
		{
			content: \'\';
			position: absolute;
			left: 0;
			top: (@xf-paddingLarge - 4px);
			bottom: (@xf-paddingLarge - 4px);
			width: 0;
			border-left: 1px solid currentColor;
		}

		&:after
		{
			.m-faBase();
			.m-faContent(@fa-var-chevron-down, 1em);
		}

		&.is-active:after
		{
			.m-faContent(@fa-var-chevron-up, 1em);
		}
	}
}

.offCanvasMenu-linkHolder
{
	display: flex;

	&.is-selected
	{
		a
		{
			color: inherit;
		}

		.offCanvasMenu-link:first-child
		{
			padding-left: @xf-paddingLarge;
		}
	}

	.offCanvasMenu-link
	{
		flex-grow: 1;

		&.offCanvasMenu-link--splitToggle
		{
			flex-grow: 0;
		}

		&:hover
		{
			background: none;
		}
	}
}

.offCanvasMenu-list
{
	.m-listPlain();

	> li
	{
		border-top: @xf-borderSize solid transparent;
	}

	&:first-child > li:first-child
	{
		border-top: none;
	}
}

.offCanvasMenu-subList
{
	.m-listPlain();
	.m-transitionFadeDown();

	padding-bottom: @xf-paddingLargest;

	.offCanvasMenu-link
	{
		padding-left: @xf-paddingLarge;
		padding-top: @xf-paddingMedium;
		padding-bottom: @xf-paddingMedium;
		font-size: @xf-fontSizeSmall;
	}
}

.offCanvasMenu--blocks
{
	.offCanvasMenu-content
	{
		.xf-pageBackground();
		color: @xf-textColor;
	}

	.offCanvasMenu-header
	{
		color: @xf-textColorEmphasized;
		background: @xf-contentHighlightBg;
		border-bottom: @xf-borderSize solid @xf-borderColorHeavy;
	}

	.offCanvasMenu-separator
	{
		border-top-color: @xf-borderColor;
	}

	.offCanvasMenu-list > li
	{
		border-top-color: @xf-borderColor;
	}
}

.offCanvasMenu--nav
{
	.offCanvasMenu-content
	{
		.xf-publicNav();

		a
		{
			color: inherit;
		}
	}

	.offCanvasMenu-header
	{
		background: @xf-publicHeaderAdjustColor;
		border-bottom: @xf-borderSize solid fadein(@xf-publicHeaderAdjustColor, 10%);
	}

	.offCanvasMenu-list
	{
		border-bottom: @xf-borderSize solid fadein(@xf-publicHeaderAdjustColor, 10%);
	}

	.offCanvasMenu-separator
	{
		border-top-color: fadein(@xf-publicHeaderAdjustColor, 10%);
	}

	.offCanvasMenu-link.offCanvasMenu-link--splitToggle:before
	{
		border-left-color: fadein(@xf-publicHeaderAdjustColor, 1%);
	}

	.offCanvasMenu-linkHolder
	{
		text-decoration: none;

		&:hover
		{
			background: fadeout(@xf-publicHeaderAdjustColor, 6%);
		}

		&.is-selected
		{
			.xf-publicNavSelected(no-border, no-border-radius);

			.offCanvasMenu-link.offCanvasMenu-link--splitToggle:before
			{
				border-left-color: fade(xf-default(@xf-publicNavSelected--color, transparent), 20%);
			}
		}
	}

	.offCanvasMenu-subList
	{
		background: @xf-publicHeaderAdjustColor;

		.offCanvasMenu-link:hover
		{
			text-decoration: none;
			background: @xf-publicHeaderAdjustColor;
		}
	}

	.offCanvasMenu-list > li
	{
		border-top-color: @xf-publicHeaderAdjustColor;
	}
}';
	return $__finalCompiled;
});