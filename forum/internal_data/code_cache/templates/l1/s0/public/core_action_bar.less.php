<?php
// FROM HASH: 206342f035284ffba71d6ca6a0d07134
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '.actionBar
{
	.m-clearFix();
}

.actionBar-set
{
	&.actionBar-set--internal
	{
		float: left;
		margin-left: -3px;

		> .actionBar-action:first-child
		{
			margin-left: 0;
		}
	}

	&.actionBar-set--external
	{
		float: right;
		margin-right: -3px;

		> .actionBar-action:last-child
		{
			margin-right: 0;
		}
	}
}

.actionBar-action
{
	display: inline-block;
	padding: 3px;
	border: 1px solid transparent;
	border-radius: @xf-borderRadiusMedium;
	margin-left: 5px;

	&:before
	{
		.m-faBase();
		font-size: 90%;
	}

	&.actionBar-action--menuTrigger
	{
		display: none;

		&:after
		{
			.m-menuGadget(true);
		}

		&.is-menuOpen
		{
			// get rid of text decoration when the menu opens
			text-decoration: none;
		}
	}

	&.actionBar-action--inlineMod
	{
		label
		{
			color: @xf-linkColor;
			font-size: 120%;
			vertical-align: -2px;
		}

		input
		{
			.m-checkboxAligner();
		}
	}

	&.actionBar-action--mq
	{
		&:before { .m-faContent("@{fa-var-plus}\\20"); }

		&.is-selected
		{
			background-color: @xf-contentHighlightBg;
			border-color: @xf-borderColorHighlight;

			&:before { .m-faContent("@{fa-var-minus}\\20"); }
		}
	}

	&.actionBar-action--postLink
	{
		text-decoration: inherit !important;
		color: inherit !important;
	}

	&.actionBar-action--reply:before { .m-faContent("@{fa-var-reply}\\20"); }
	&.actionBar-action--like:before { .m-faContent("@{fa-var-thumbs-up}\\20"); }

	&.actionBar-action--reaction:not(.has-reaction) .reaction-text
	{
		color: inherit;
	}
}

@media (max-width: @xf-responsiveNarrow)
{
	.actionBar-action
	{
		&.actionBar-action--menuItem
		{
			display: none !important;
		}

		&.actionBar-action--menuTrigger
		{
			display: inline;
		}
	}
}';
	return $__finalCompiled;
});