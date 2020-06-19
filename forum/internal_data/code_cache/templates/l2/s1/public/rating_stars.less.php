<?php
// FROM HASH: 56902ec6b551b0dbda901ab0c26437a9
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '.ratingStars
{
	display: inline-block;
	line-height: 1;
	font-size: 120%;
	vertical-align: sub;
	position: relative;

	&.ratingStars--larger
	{
		font-size: 140%;
		vertical-align: bottom;
	}

	&.ratingStars--smaller
	{
		font-size: 100%;
		vertical-align: top;
		top: .2em;
	}
}

.ratingStars-star
{
	float: left;
	position: relative;
	margin-left: 2px;

	&:first-child
	{
		margin-left: 0;
	}

	&:before
	{
		.m-faBase(@faType; @faWeight-solid);
		.m-faContent(@fa-var-star);
		color: @xf-starEmptyColor;
	}

	&.ratingStars-star--full
	{
		&:before
		{
			color: @xf-starFullColor;
		}
	}

	&.ratingStars-star--half
	{
		&:after
		{
			position: absolute;
			top: 1px;
			left: 0;
			.m-faBase(@faType; @faWeight-solid);
			.m-faContent(@fa-var-star-half);
			color: @xf-starFullColor;

			& when(@rtl)
			{
				.m-transform(scaleX(-1));
			}
		}
	}
}

.ratingStarsRow
{
	&.ratingStarsRow--justified
	{
		display: flex;

		.ratingStarsRow-text
		{
			margin-left: auto;
		}
	}

	&.ratingStarsRow--textBlock
	{
		display: block;

		.ratingStarsRow-text
		{
			display: block;
		}
	}
}

/** XF: override */
.br-select
{
	display: none;
}

.br-theme-fontawesome-stars
{
	.br-widget
	{
		&.br-widget--withSelected
		{
			height: 40px;

			.br-current-rating
			{
				display: block;
				font-size: 80%;
			}
		}
	}
}

/** Vendor: variables.less */
@star-default: @xf-starEmptyColor;
@star-active: @xf-starFullColor;
@star-selected: @xf-starFullColor;

/** Vendor: fontawesome-stars.less */
.br-theme-fontawesome-stars {

	.br-widget {
		height: 28px;
		white-space: nowrap;

		a {
			.m-faBase(@faType; @faWeight-solid);
			font-size: 120%;
			text-decoration: none;
			margin-right: 2px;
		}

		a:after {
			.m-faContent(@fa-var-star);
			color: @star-default;
		}

		a.br-active:after {
			color: @star-active;
		}

		a.br-selected:after {
			color: @star-selected;
		}

		.br-current-rating {
			display: none;
		}
	}

	.br-readonly {
		a {
			cursor: default;
		}
	}

}

@media print {
	.br-theme-fontawesome-stars {

		.br-widget {
			a:after {
				.m-faContent(@fa-var-star);
				color: black;
			}

			a.br-active:after,
			a.br-selected:after {
				.m-faContent(@fa-var-star);
				color: black;
			}
		}

	}
}';
	return $__finalCompiled;
});