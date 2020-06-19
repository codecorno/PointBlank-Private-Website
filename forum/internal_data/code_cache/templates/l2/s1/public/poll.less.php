<?php
// FROM HASH: 8a5566e775e1eb3b41109f4cb86109b0
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '.pollResult
{
	display: table;
	table-layout: fixed;
	width: 100%;
	margin: 0;
	padding: @xf-blockPaddingV @xf-blockPaddingH;

	&.pollResult--showVoters
	{
		cursor: pointer;

		&:hover
		{
			background: @xf-contentHighlightBg;
		}
	}
}

.pollResult-response
{
	display: table-cell;
	margin: 0;
	padding: 0;
	vertical-align: middle;
	font-size: @xf-fontSizeNormal;
	font-weight: @xf-fontWeightNormal;

	.pollResult--voted &
	{
		font-weight: @xf-fontWeightHeavy;

		&:before
		{
			.m-faBase();
			.m-faContent("@{fa-var-check-circle}\\20");
			color: @xf-textColorAttention;
			unicode-bidi: isolate;
		}
	}
}

.pollResult-votes
{
	display: table-cell;
	width: 6.5em;
	vertical-align: middle;
	text-align: right;
}

.pollResult-percentage
{
	display: table-cell;
	width: 4.35em;
	vertical-align: middle;
	text-align: right;
}

.pollResult-graph
{
	display: table-cell;
	width: 30%;
	padding-left: @xf-paddingMedium;
	vertical-align: middle;
}

.pollResult-bar
{
	display: block;
	position: relative;
	height: .8em;

	&:empty
	{
		display: none;
	}

	> i
	{
		position: absolute;
		left: 0;
		top: 0;
		bottom: 0;
		background: @xf-progressBarColor;
		border-radius: @xf-borderRadiusMedium;
	}
}

.pollResult-voters
{
	padding: @xf-blockPaddingV @xf-blockPaddingH;
	.xf-contentHighlightBase();
}

.pollResult--simple
{
	display: block;
	.m-narrowPollResults();
}

@media (max-width: @xf-responsiveMedium)
{
	.pollResult
	{
		display: block;
		.m-narrowPollResults();
	}
}

.m-narrowPollResults()
{
	.pollResult-response
	{
		display: block;
	}

	.pollResult-votes
	{
		display: inline;
	}

	.pollResult-percentage
	{
		display: inline;
		padding-left: 1em;
	}

	.pollResult-graph
	{
		display: block;
		width: 100%;
		padding-left: 0;
	}
}';
	return $__finalCompiled;
});