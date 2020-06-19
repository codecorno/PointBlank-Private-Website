<?php
// FROM HASH: 06e5dc7cbc7cbc59475a32f3bedce056
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '// navEl / Navigation Elements for header navigation

.m-navElHPadding(@padding)
{
	.p-navEl-link
	{
		padding: 0 @padding;

		&.p-navEl-link--splitMenu
		{
			padding-right: ((@padding) / 4);
		}

		&.p-navEl-link--menuTrigger
		{
			padding-right: ((@padding) / 3); // equal to splitTrigger padding-right

			&:after
			{
				padding-left: ((@padding) / 4 + (@padding) / 3); // equal to splitMenu padding-right + splitTrigger padding-left
			}
		}
	}

	.p-navEl-splitTrigger
	{
		padding: 0 ((@padding) / 3);
	}
}

.p-navEl
{
	.m-clearFix();
}

.p-navEl-linkHolder
{
	float: left;
}

.p-navEl-link
{
	float: left;
	.m-transition(opacity, background; @_nav-elTransitionSpeed);

	&.p-navEl-link--menuTrigger
	{
		cursor: pointer;

		&:after
		{
			.m-menuGadget(); // .58em
			opacity: .5;
			.m-transition(opacity; @_nav-elTransitionSpeed);
		}

		&:hover:after
		{
			opacity: 1;
		}
	}
}

.p-navEl-splitTrigger
{
	float: left;
	opacity: .5;
	cursor: pointer;
	text-decoration: none;
	.m-transition(all; @_nav-elTransitionSpeed);

	&:after
	{
		.m-menuGadget(); // .58em
		line-height: 1;
	}

	&:hover
	{
		opacity: 1;
		text-decoration: none;
	}
}

// HEADER NAV ROW

.p-nav
{
	.xf-publicNav();

	a
	{
		color: inherit;
	}

	.p-navSticky.is-sticky &
	{
		.p-nav-list .p-navEl.is-selected,
		.p-account
		{
			border-radius: 0;
		}
	}
}

.p-nav-inner
{
	.m-pageWidth();
	.m-pageInset(0px);
	.m-clearFix();
	display: flex;
	align-items: flex-end;
}



.p-nav .p-nav-menuTrigger
{
	display: none;
	vertical-align: middle;
	align-self: center;
	margin-left: max(0px, @xf-pageEdgeSpacer - 6px);
	margin-right: 5px;
	padding: @xf-publicNavPaddingV 8px;

	&:hover
	{
		text-decoration: none;
	}

	i:after
	{
		.m-faBase();
		font-size: @xf-fontSizeLargest;
		.m-faContent(@fa-var-bars, .88em);
		vertical-align: bottom;
	}

	.p-nav-menuText
	{
		display: none;
	}
}

.p-nav-smallLogo
{
	display: none;
	max-width: 100px;
	align-self: center;

	img
	{
		display: block;
		max-height: @header-navHeight;
	}
}

.p-nav-scroller
{
	margin-right: auto;
	max-width: 100%;

	.hScroller-scroll:not(.is-calculated)
	{
		// IE11 seems to want to flash a vertical scrollbar without this here
		overflow: hidden;
	}

	.hScroller-action
	{
		.m-hScrollerActionColorVariation(
			xf-default(@xf-publicNav--background-color, transparent),
			xf-default(@xf-publicNav--color, ~""),
			xf-intensify(@xf-publicNav--color, 10%)
		);
	}
}

.p-nav-list
{
	.m-listPlain();
	.m-clearFix();

	font-size: 0;

	// this applies padding that will be contained within the scroller.
	// it needs font-size 0 to not create extra space though
	&:before,
	&:after
	{
		display: inline-block;
		width: @xf-pageEdgeSpacer;
		content: \'\';
	}

	> li
	{
		display: inline-block;
		vertical-align: bottom;
		font-size: @xf-fontSizeNormal;

		&:first-child
		{
			margin-left: 0;
		}
	}

	.m-navElHPadding(@xf-publicNavPaddingH);

	.p-navEl
	{
		.xf-publicNavTab();
		.m-transition(all; @_nav-elTransitionSpeed);

		&.is-selected
		{
			.xf-publicNavSelected();

			.p-navEl-link
			{
				padding-right: @xf-publicNavPaddingH; // since the split trigger is hidden

				&:hover
				{
					background: none;
					text-decoration: none;
				}
			}

			.p-navEl-splitTrigger
			{
				display: none;
			}
		}

		&:not(.is-selected):not(.is-menuOpen)
		{
			&:hover,
			.p-navEl-link:hover,
			.p-navEl-splitTrigger:hover
			{
				.xf-publicNavTabHover();
			}
		}

		&.is-menuOpen
		{
			.xf-publicNavTabMenuOpen();

			border-top-left-radius: xf-default(@xf-publicNavSelected--border-top-left-radius, 0);
			border-top-right-radius: xf-default(@xf-publicNavSelected--border-top-right-radius, 0);

			.m-dropShadow(0, 5px, 10px, 0, .35);

			a
			{
				text-decoration: none;
				opacity: 1;
			}
		}
	}

	.p-navEl-link,
	.p-navEl-splitTrigger
	{
		padding-top: @xf-publicNavPaddingV;
		padding-bottom: @xf-publicNavPaddingV;
	}
}

.p-navSticky--primary.is-sticky
{
	.p-nav-list
	{
		.p-navEl-link.p-navEl-link--splitMenu
		{
			padding-right: ((@xf-publicNavPaddingH) / 4);
		}

		.p-navEl.is-selected .p-navEl-splitTrigger
		{
			display: inline;
			position: relative;

			&:before
			{
				content: \'\';
				position: absolute;
				left: 0;
				top: 5px;
				bottom: 5px;
				width: 0;
				border-left: @xf-borderSize solid fade(xf-default(@xf-publicNavSelected--color, transparent), 35%);
			}
		}
	}
}

@media (max-width: @xf-publicNavCollapseWidth)
{
	.has-js
	{
		.p-nav-inner
		{
			min-height: 45px;
		}

		.p-nav .p-nav-menuTrigger
		{
			display: inline-block;
		}

		.p-nav-smallLogo
		{
			display: inline-block;
		}

		.p-nav-scroller
		{
			display: none;
		}
	}
}

// ACCOUNT/VISITOR/SEARCH SECTION

.p-nav-opposite
{
	margin-left: auto;
	margin-right: @xf-pageEdgeSpacer;
	text-align: right;
	flex-shrink: 0;
}

.p-navgroup
{
	float: left;
	.m-clearFix();
	background: @xf-publicHeaderAdjustColor;
	border-top-left-radius: @xf-borderRadiusMedium;
	border-top-right-radius: @xf-borderRadiusMedium;

	&.p-discovery
	{
		margin-left: .5em;

		&.p-discovery--noSearch
		{
			margin-left: 0;
		}
	}
}

.p-navgroup-link
{
	float: left;
	padding: @xf-publicNavPaddingV @_navAccount-hPadding;
	border-left: 1px solid @xf-publicHeaderAdjustColor;

	&:first-of-type
	{
		border-top-left-radius: @xf-borderRadiusMedium;
		border-left: none;
	}

	&:last-of-type
	{
		border-top-right-radius: @xf-borderRadiusMedium;
	}

	&:hover
	{
		text-decoration: none;
		background: xf-intensify(@xf-publicHeaderAdjustColor, 5%);
	}

	&.p-navgroup-link--user
	{
		.m-overflowEllipsis();
		max-width: 150px;

		.avatar
		{
			.m-avatarSize((@xf-fontSizeNormal) * (@xf-lineHeightDefault));
		}
	}

	&.badgeContainer
	{
		opacity: .6;
		position: relative;

		&:after
		{
			position: absolute;
			left: (@_navAccount-hPadding - 6px);
			top: (@xf-publicNavPaddingV - 2px);
			padding: 1px 3px;
			margin: 0;
			font-size: 10px;
			line-height: 11px;
		}

		&:hover,
		&.badgeContainer--highlighted
		{
			opacity: 1;
		}
	}

	&.is-menuOpen
	{
		.xf-publicNavTabMenuOpen();
		.m-dropShadow(0, 5px, 10px, 0, .35);
		opacity: 1;
	}

	&.p-navgroup-link--iconic
	{
		i:after
		{
			.m-faBase();
			display: inline-block;
			min-width: 1.2em;
			text-align: center;
		}
	}

	&.p-navgroup-link--conversations i:after
	{
		.m-faContent(@fa-var-envelope, 1em);
	}

	&.p-navgroup-link--alerts i:after
	{
		.m-faContent(@fa-var-bell, .88em);
	}

	&.p-navgroup-link--bookmarks i:after
	{
		.m-faContent(@fa-var-bookmark, .75em);
	}

	&.p-navgroup-link--whatsnew i:after
	{
		.m-faContent(@fa-var-bolt, .63em);
	}

	&.p-navgroup-link--search i:after
	{
		.m-faContent(@fa-var-search, 1em);
	}
}

.p-navgroup-link--whatsnew
{
	display: none;

	.p-navgroup-link:first-of-type& + .p-navgroup-link
	{
		border-top-left-radius: @xf-borderRadiusMedium;
		border-left: none;
	}
}

@media (max-width: @xf-responsiveWide)
{
	.p-navgroup-link
	{
		&.p-navgroup-link--iconic .p-navgroup-linkText,
		&.p-navgroup-link--textual i
		{
			display: none;
		}

		&.p-navgroup-link--textual
		{
			.m-overflowEllipsis();
			max-width: 110px;
		}

		&.p-navgroup-link--iconic i:after
		{
			text-align: center;
		}
	}
}

@media (max-width: @xf-publicNavCollapseWidth)
{
	.p-navgroup-link--whatsnew
	{
		display: block;
	}

	.has-js
	{
		.p-nav-opposite
		{
			align-self: center;
			margin-right: max(0px, @xf-pageEdgeSpacer - 6px);

			.p-navgroup
			{
				background: none;
				margin-left: 0;
			}

			.p-navgroup-link
			{
				border: none;
				border-radius: @xf-borderRadiusMedium;

				&.is-menuOpen
				{
					.m-borderBottomRadius(0);
				}

				&.badgeContainer
				{
					opacity: 1;
				}
			}
		}
	}
}

@media (max-width: 359px)
{
	.p-navgroup-link&.p-navgroup-link--user
	{
		display: none;
	}
}

@media (max-width: 374px)
{
	.p-navgroup-link.p-navgroup-link--register
	{
		display: none;
	}
}';
	return $__finalCompiled;
});