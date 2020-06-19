<?php
// FROM HASH: 8eff499ca7944f411a8fec4b991ee9dd
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '// ################################## TABS ####################################

.tabs
{
	display: flex;

	&.tabs--wrappable
	{
		flex-wrap: wrap;
	}
}

.tabs-tab
{
	display: inline-block;
	cursor: pointer;
	white-space: nowrap;
	color: inherit;
	font-weight: inherit;
	text-decoration: none;
	.m-transition();

	&:hover
	{
		text-decoration: none;
	}
}

.tabs-extra
{
	float: right;
	position: relative;
	margin-left: auto;
	padding: 0 0 0 @xf-paddingMedium;
	align-self: center;
	white-space: nowrap;

	a
	{
		color: inherit;
		text-decoration: none;
		display: block;
		padding: @xf-paddingMedium;
	}
}

.tabPanes
{
	.m-listPlain();

	> li
	{
		display: none;

		&.is-active
		{
			display: block;
		}
	}
}

// ################################# OUTER TABS #########################

.tabs--standalone
{
	padding: 0;
	margin-bottom: @xf-elementSpacer;
	font-weight: @xf-fontWeightNormal;
	.xf-blockBorder();
	.xf-standaloneTab();
	.m-tabsTogether(xf-default(@xf-standaloneTab--font-size, @xf-fontSizeNormal));

	.tabs-tab
	{
		padding: @xf-blockPaddingV @xf-blockPaddingH max(0px, @xf-blockPaddingV - @xf-borderSizeFeature);
		border-bottom: @xf-borderSizeFeature solid transparent;

		&:hover
		{
			color: @xf-standaloneTabSelected--color;
		}

		&.is-active
		{
			.xf-standaloneTabSelected();
		}
	}

	.hScroller-action
	{
		.m-hScrollerActionColorVariation(
			xf-default(@xf-standaloneTab--background-color, transparent),
			xf-default(@xf-standaloneTab--color, ~""),
			xf-default(@xf-standaloneTabSelected--color, ~"")
		);
	}
}

@media (max-width: @xf-responsiveEdgeSpacerRemoval)
{
	.tabs--standalone
	{
		margin-left: -@xf-pageEdgeSpacer;
		margin-right: -@xf-pageEdgeSpacer;
		border-radius: 0;
		border-left: none;
		border-right: none;
	}
}';
	return $__finalCompiled;
});