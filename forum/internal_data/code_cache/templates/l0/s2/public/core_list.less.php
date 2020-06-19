<?php
// FROM HASH: c51b1dc591bee23c68340da675acbd7e
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '// ##################################### LISTS ##################

.pairs
{
	padding: 0;
	margin: 0;
	overflow: hidden;

	&.pairs--plainLabel
	{
		> dt
		{
			color: inherit;
		}
	}

	&.pairs--spaced + .pairs
	{
		margin-top: @xf-paddingMedium;
	}

	&.pairs--noColon
	{
		> dt
		{
			&:after
			{
				content: "";
			}
		}
	}

	> dt
	{
		padding: 0;
		margin: 0;
		color: @xf-textColorMuted;

		.m-appendColon();
	}

	> dd
	{
		padding: 0;
		margin: 0;
	}

	&.pairs--inline
	{
		display: inline;

		> dt,
		> dd
		{
			display: inline;
		}
	}

	&.pairs--columns
	{
		display: table;
		table-layout: fixed;
		width: 100%;

		> dt,
		> dd
		{
			display: table-cell;
		}

		> dt
		{
			width: 50%;
			padding-right: @xf-paddingMedium;
		}

		&.pairs--fixedSmall > dt
		{
			width: 200px;
		}
		&.pairs--fluidSmall > dt
		{
			width: 25%;
		}
		&.pairs--fluidHuge > dt
		{
			width: 70%;
		}

		@media (max-width: 500px)
		{
			display: block;

			> dt,
			> dd
			{
				display: block;
			}

			&.pairs > dt // the .pairs repetition is to increase specificity to override all fixed/fluid options
			{
				width: auto;
				padding-right: 0;
			}

			& + .pairs
			{
				margin-top: @xf-paddingMedium;
			}
		}
	}

	&.pairs--justified
	{
		.m-clearFix();

		> dt
		{
			float: left;
			max-width: 100%;
			margin-right: @xf-paddingMedium;
		}

		> dd
		{
			float: right;
			text-align: right;
			max-width: 100%;
		}
	}

	&.pairs--rows
	{
		display: inline-table;
		margin-right: @xf-paddingMedium;

		&.pairs--rows--centered
		{
			> dt,
			> dd
			{
				text-align: center;
			}
		}

		&:last-of-type
		{
			margin-right: 0;
		}

		> dt
		{
			display: table-row;
			font-size: 80%;

			&:after
			{
				content: \'\';
				display: none;
			}
		}

		> dd
		{
			display: table-row;
		}
	}
}

.pairWrapper
{
	&.pairWrapper--spaced
	{
		.pairs
		{
			margin-top: @xf-paddingMedium;

			&:first-child
			{
				margin-top: 0;
			}
		}
	}
}

.pairJustifier
{
	display: flex;
	flex-wrap: wrap;
	justify-content: space-between;

	margin-right: -5px;
	margin-bottom: -5px;

	.pairs.pairs--rows
	{
		margin-right: 5px;
		margin-bottom: 5px;
	}
}

.listPlain
{
	.m-listPlain();
}

.listInline
{
	.m-listPlain();

	&.listInline--selfInline
	{
		display: inline;
	}

	> li
	{
		display: inline;
		margin: 0;
		padding: 0;
	}

	&.listInline--block
	{
		> li
		{
			display: inline-block;
		}
	}

	&.listInline--comma
	{
		> li:after
		{
			content: "' . $__templater->escape($__vars['xf']['language']['comma_separator']) . '";
		}

		> li:last-child:after
		{
			content: "";
			display: none;
		}
	}

	&.listInline--bullet
	{
		> li:before
		{
			content: "\\00B7\\20";
		}

		> li:first-child:before
		{
			content: "";
			display: none;
		}
	}
}

.listHeap
{
	.m-listPlain();
	margin-top: -4px;

	> li
	{
		margin: 0;
		padding: 0;
		display: inline-block;
		margin-right: -1px;
		margin-top: 4px;

		&:last-child
		{
			margin-right: 0;
		}
	}
}

.listColumns
{
	.m-columns(2, @xf-responsiveNarrow);

	> li
	{
		.m-columnBreakAvoid();
		position: relative; // workaround Chrome quirk with hover triggers in visitor menu
	}

	&.listColumns--spaced
	{
		> li
		{
			margin-bottom: .5em;
		}
	}

	&.listColumns--narrow
	{
		.m-columns(2);
	}

	&.listColumns--together
	{
		-moz-column-gap: 0;
		-webkit-column-gap: 0;
		column-gap: 0;
	}

	&.listColumns--collapsed
	{
		display: inline-block;
	}

	&.listColumns--3
	{
		.m-columns(3);

		@media (max-width: @xf-responsiveWide)
		{
			.m-columns(2, @xf-responsiveNarrow);
		}
	}

	&.listColumns--4
	{
		.m-columns(4);

		@media (max-width: @xf-responsiveWide)
		{
			.m-columns(2, @xf-responsiveNarrow);
		}
	}
}

.textHighlight
{
	font-style: normal;
	font-weight: @xf-fontWeightHeavy;

	&.textHighlight--attention
	{
		color: @xf-textColorAttention;
	}
}';
	return $__finalCompiled;
});