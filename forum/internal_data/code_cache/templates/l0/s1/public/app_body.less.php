<?php
// FROM HASH: c46ddd84cbc0b488a24eb520d574e973
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '// ######################################### MAIN BODY #################################

.p-body
{
	display: flex;
	align-items: stretch;
	flex-grow: 1;
	min-height: 1px; // IE11 workaround - related to #139187
}

.p-body-inner
{
	display: flex;
	flex-direction: column;
	width: 100%;

	.m-pageWidth();
	.m-pageInset();
	.m-clearFix();

	padding-top: ((@xf-elementSpacer) / 2);
	padding-bottom: @xf-elementSpacer;
}

.p-body-header
{
	margin-bottom: ((@xf-elementSpacer) / 2);
}

.p-body-main
{
	display: table;
	table-layout: fixed;
	width: 100%;
	margin-bottom: auto;
	min-height: 1px; // IE11 workaround - related to #139187
}

.p-body-content
{
	display: table-cell;
	vertical-align: top;

	.p-body-main--withSideNav &
	{
		padding-left: @xf-sidebarSpacer;
	}

	.p-body-main--withSidebar &
	{
		padding-right: @xf-sidebarSpacer;
	}

	.p-body-main--withSidebar &,
	.p-body-main--withSideNav &
	{
		// don\'t let the ad overflow the sidebar area -- this can happen due to how the Adsense JS works
		ins.adsbygoogle
		{
			// -10px gives a little buffer or helps account for no scrollbar being considered
			max-width: ~"calc(100vw - 10px - @{xf-pageEdgeSpacer} - @{xf-pageEdgeSpacer} - @{xf-sidebarWidth} - @{xf-sidebarSpacer})";

			@media (min-width: @xf-pageWidthMax)
			{
				// window wider than the max width, so limit to the display area without the sidebar
				max-width: ~"calc(@{xf-pageWidthMax} - @{xf-pageEdgeSpacer} - @{xf-pageEdgeSpacer} - @{xf-sidebarWidth} - @{xf-sidebarSpacer})";
			}

			@media (max-width: @xf-responsiveWide)
			{
				// sidebar/sidenav have been moved/hidden
				max-width: 100vw;
			}
		}
	}
}

.p-body-pageContent
{
	> .tabs--standalone:first-child
	{
		margin-bottom: (@xf-elementSpacer) / 2;
	}
}

.p-body-sideNav
{
	display: table-cell;
	vertical-align: top;
	width: @xf-sidebarWidth;
}

.p-body-sideNavTrigger
{
	display: none;
}

.p-body-sidebar
{
	display: table-cell;
	vertical-align: top;
	width: @xf-sidebarWidth;

	.contentRow-figure .avatar--m
	{
		// make these avatars a bit smaller in the sidebar so the content has more space
		.m-avatarSize(@avatar-m * 2 / 3);
	}
}

.p-body-content,
.p-body-pageContent,
.p-body-sideNav,
.p-body-sideNavContent,
.p-body-sidebar
{
	> :first-child
	{
		margin-top: 0;
	}

	> :last-child
	{
		margin-bottom: 0;
	}
}

@media (max-width: @xf-responsiveWide)
{
	.p-body-main,
	.p-body-content
	{
		display: block;
	}

	.p-body-sideNav
	{
		display: block;
		width: auto;
	}

	.p-body-sideNavTrigger
	{
		margin-bottom: ((@xf-elementSpacer) / 2);
		text-align: center;

		.button:before
		{
			.m-faBase();
			font-size: 120%;
			vertical-align: middle;
			display: inline-block;
			margin: -4px 6px -4px 0;
			.m-faContent(@fa-var-bars, .88em);
		}

		.has-js &
		{
			display: block;
		}
	}

	.has-js .p-body-sideNavInner:not(.offCanvasMenu)
	{
		display: none;

		.m-transitionFadeDown();
	}

	.has-no-js .p-body-sideNavInner
	{
		margin-bottom: @xf-elementSpacer;
	}

	.p-body-sidebar
	{
		display: flex;
		flex-wrap: wrap;
		align-items: stretch;
		margin: (@xf-elementSpacer) -((@xf-pageEdgeSpacer) / 2) -(@xf-elementSpacer);
		width: auto;

		> *
		{
			margin: 0 ((@xf-pageEdgeSpacer) / 2) @xf-elementSpacer;
			min-width: @xf-sidebarWidth;
			flex: 1 1 @xf-sidebarWidth;

			&:last-child
			{
				margin-bottom: @xf-elementSpacer;
			}
		}

		// add an invisible block to ensure that the last row has the correct widths
		&:after
		{
			display: block;
			content: \'\';
			height: 0;
			margin: 0 ((@xf-pageEdgeSpacer) / 2);
			min-width: @xf-sidebarWidth;
			flex: 1 1 @xf-sidebarWidth;
		}

		.block-container
		{
			display: flex;
			flex-direction: column;
			height: 100%;

			.block-footer
			{
				margin-top: auto;
			}
		}
	}

	.p-body-main--withSideNav,
	.p-body-main--withSidebar
	{
		.p-body-content { padding: 0; }
	}
}

@media (max-width: @xf-responsiveEdgeSpacerRemoval)
{
	.p-body-sideNavContent
	{
		// this is likely to contain blocks that overflow the container so account for that
		margin: 0 -@xf-pageEdgeSpacer;
		padding: 0 @xf-pageEdgeSpacer;

		.offCanvasMenu &
		{
			margin: 0;
			padding: 0;
		}
	}

	.p-body-sidebar
	{
		display: block;
		margin-left: 0;
		margin-right: 0;

		> *
		{
			margin-left: 0;
			margin-right: 0;
			min-width: 0;
		}
	}
}';
	return $__finalCompiled;
});