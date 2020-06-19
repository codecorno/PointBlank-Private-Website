<?php
// FROM HASH: e73afda64c126446b037fea5ffaef6c3
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '// ############################ BUTTONS #################

.button,
a.button // needed for specificity over a:link
{
	.m-buttonBase();

	a
	{
		color: inherit;
		text-decoration: none;
	}

	.xf-buttonDefault();
	.m-buttonBlockColorVariationSimple(xf-default(@xf-buttonDefault--background-color, transparent));

	&.button--primary
	{
		.xf-buttonPrimary();
		.m-buttonBlockColorVariationSimple(xf-default(@xf-buttonPrimary--background-color, transparent));
	}

	&.button--cta
	{
		.xf-buttonCta();
		.m-buttonBlockColorVariationSimple(xf-default(@xf-buttonCta--background-color, transparent));
	}

	&.button--link
	{
		// block colors
		background: @xf-contentBg;
		color: @xf-linkColor;
		.m-buttonBorderColorVariation(@xf-borderColor);

		&:hover,
		&:active,
		&:focus
		{
			text-decoration: none;
			background: @xf-contentHighlightBg;
		}
	}

	&.button--plain
	{
		background: none;
		color: @xf-linkColor;
		border: none;

		&:hover,
		&:active,
		&:focus
		{
			text-decoration: none;
			background: none;
		}
	}

	&.button--alt
	{
		// block colors
		background-color: @xf-paletteColor1;
		color: @xf-linkColor;
		.m-buttonBorderColorVariation(@xf-paletteColor2);

		&:hover,
		&:active,
		&:focus
		{
			background-color: @xf-paletteColor1;
			color: @xf-linkColor;
		}
	}

	&.button--longText
	{
		.m-overflowEllipsis();
		max-width: 100%;
		display: inline-block;
	}

	&.is-disabled
	{
		.xf-buttonDisabled();
		.m-buttonBorderColorVariation(xf-default(@xf-buttonDisabled--background-color, transparent));

		&:hover,
		&:active,
		&:focus
		{
			background: xf-default(@xf-buttonDisabled--background-color, transparent) !important;
		}
	}

	&.button--scroll
	{
		background: fade(xf-default(@xf-buttonDefault--background-color, transparent), 75%);
		padding: 5px 8px;

		.m-dropShadow();
	}

	&.button--normal
	{
		font-size: @xf-fontSizeNormal;
	}

	&.button--small
	{
		font-size: @xf-fontSizeSmall;
		padding: 3px 6px;
	}

	&.button--smaller
	{
		font-size: @xf-fontSizeSmaller;
		padding: 2px 5px;
	}

	&.button--fullWidth
	{
		display: block;
		width: 100%;
	}

	&.button--icon
	{
		> .button-text:before,
		.button-icon
		{
			.m-faBase();
		}

		> .button-text:before,
		> .fa--xf:before,
		.button-icon
		{
			font-size: 120%;
			vertical-align: -.1em;
			display: inline-block;
			margin: -.255em 6px -.255em 0;
		}

		> .fa--xf
		{
			// helps fix a button alignment issue (Chrome only)
			line-height: inherit;
		}

		.button-icon
		{
			height: 1em;
			vertical-align: 0;
		}

		&.button--iconOnly
		{
			> .button-text:before,
			> i.fa--xf:before,
			.button-icon
			{
				margin-left: 0;
				margin-right: 0;
			}
		}

		&.button--padded
		{
			> .button-text:before,
			> i.fa--xf:before,
			.button-icon
			{
				margin-top: 0;
				margin-bottom: 0;
			}
		}

		&--add          { .m-buttonIcon(@fa-var-plus-square, .79em); }
		&--confirm      { .m-buttonIcon(@fa-var-check, 1em); }
		&--write	    { .m-buttonIcon(@fa-var-edit, 1em); }
		&--import  	    { .m-buttonIcon(@fa-var-upload, .93em); }
		&--export  	    { .m-buttonIcon(@fa-var-download,  1.125em); }
		&--download	    { .m-buttonIcon(@fa-var-download, 1.125em); }
		&--redirect	    { .m-buttonIcon(@fa-var-external-link, 1.125em); }
		&--disable      { .m-buttonIcon(@fa-var-power-off); }
		&--edit         { .m-buttonIcon(@fa-var-edit, .86em); }
		&--save         { .m-buttonIcon(@fa-var-save, .86em); }
		&--reply	    { .m-buttonIcon(@fa-var-reply, 1em); }
		&--quote	    { .m-buttonIcon(@fa-var-quote-left, .93em); }
		&--purchase	    { .m-buttonIcon(@fa-var-credit-card, 1.11em); }
		&--payment	    { .m-buttonIcon(@fa-var-credit-card, 1.08em); }
		&--convert	    { .m-buttonIcon(@fa-var-bolt, .5em); }
		&--search	    { .m-buttonIcon(@fa-var-search, .93em); }
		&--sort         { .m-buttonIcon(@fa-var-sort, .58em); }
		&--upload	    { .m-buttonIcon(@fa-var-upload, .93em); }
		&--attach	    { .m-buttonIcon(@fa-var-paperclip, .79em); }
		&--login {
			.m-buttonIcon(@fa-var-lock, .90em);
		}
		&--rate         { .m-buttonIcon(@fa-var-star, .93em); }
		&--config       { .m-buttonIcon(@fa-var-cog, .86em); }
		&--refresh      { .m-buttonIcon(@fa-var-sync-alt, .86em); }
		&--translate    { .m-buttonIcon(@fa-var-globe, .86em); }
		&--vote         { .m-buttonIcon(@fa-var-check-circle, .86em); }
		&--result       { .m-buttonIcon(@fa-var-chart-bar, 1.15em); }
		&--history	    { .m-buttonIcon(@fa-var-history, .86em); }
		&--cancel       { .m-buttonIcon(@fa-var-ban, .86em); }
		&--close        { .m-buttonIcon(@fa-var-times, .79em); }
		&--preview      { .m-buttonIcon(@fa-var-eye, 1em); }
		&--conversation { .m-buttonIcon(@fa-var-comments, 1em); }
		&--bolt         { .m-buttonIcon(@fa-var-bolt, .5em); }
		&--list         { .m-buttonIcon(@fa-var-list, .86em); }
		&--prev			{ .m-buttonIcon(@fa-var-chevron-left, .71em); }
		&--next			{ .m-buttonIcon(@fa-var-chevron-right, .71em); }
		&--markRead     { .m-buttonIcon(@fa-var-check-square, .93em); }
		&--user         { .m-buttonIcon(@fa-var-user, .72em); }
		&--userCircle   { .m-buttonIcon(@fa-var-user-circle, 1em); }

		&--notificationsOn  { .m-buttonIcon(@fa-var-bell, 1em); }
		&--notificationsOff { .m-buttonIcon(@fa-var-bell-slash, 1.15em); }

		&--show			{ .m-buttonIcon(@fa-var-eye) }
		&--hide			{ .m-buttonIcon(@fa-var-eye-slash) }

		// for inline mod confirmations
		&--merge { .m-buttonIcon(@fa-var-compress, .86em); }
		&--move { .m-buttonIcon(@fa-var-share, 1em); }
		&--copy { .m-buttonIcon(@fa-var-copy, 1em); }
		&--approve, &--unapprove { .m-buttonIcon(@fa-var-shield, .72em); }
		&--delete, &--undelete { .m-buttonIcon(@fa-var-trash-alt, .79em); }
		&--stick, &--unstick { .m-buttonIcon(@fa-var-thumbtack, .65em); }
		&--lock { .m-buttonIcon(@fa-var-lock, .65em); }
		&--unlock { .m-buttonIcon(@fa-var-unlock, .93em); }

		&--bookmark
		{
			.m-buttonIcon(@fa-var-bookmark);

			&.is-bookmarked .button-text:before
			{
				font-weight: @faWeight-solid;
				color: @xf-textColorAttention;
			}
		}
	}

	&.button--provider
	{
		> .button-text:before,
		.button-icon
		{
			.m-faBase(\'Brands\');
			font-size: 120%;
			vertical-align: middle;
			display: inline-block;
			margin: -4px 6px -4px 0;
		}

		.button-icon
		{
			height: 1em;
			vertical-align: 0;
		}

		&--facebook
		{
			.m-buttonColorVariation(#3B5998, white);
			.m-buttonIcon(@fa-var-facebook, .58em);
		}

		&--twitter
		{
			.m-buttonColorVariation(#1DA1F3, white);
			.m-buttonIcon(@fa-var-twitter, .93em);
		}

		&--google
		{
			.m-buttonColorVariation(white, #444);
			border-color: #e9e9e9;

			> .button-text:before
			{
				display: none;
			}
		}

		&--github
		{
			.m-buttonColorVariation(#666666, white);
			.m-buttonIcon(@fa-var-github, .86em);
		}

		&--linkedin
		{
			.m-buttonColorVariation(#0077b5, white);
			.m-buttonIcon(@fa-var-linkedin, .86em);
		}

		&--microsoft
		{
			.m-buttonColorVariation(#00bcf2, white);
			.m-buttonIcon(@fa-var-windows, .93em);
		}

		&--yahoo
		{
			.m-buttonColorVariation(#410093, white);
			.m-buttonIcon(@fa-var-yahoo, .86em);
		}
	}

	&.button--splitTrigger
	{
		// button-text and button-menu are always children of button--splitTrigger
		// but are defined here for reasons of specificity, as these border colors
		// are overwritten by .m-buttonBorderColorVariation()
		> .button-text { border-right: @xf-borderSize solid transparent; }
		> .button-menu { border-left: @xf-borderSize solid transparent; }

		.m-clearFix();
		padding: 0;
		font-size: 0;
		display: inline-block;

		button.button-text
		{
			background: transparent;
			border: none;
			border-right: @xf-borderSize solid transparent;
			color: inherit;
		}

		> .button-text,
		> .button-menu
		{
			.xf-buttonBase();
			display: inline-block;

			&:hover
			{
				&:after
				{
					opacity: 1;
				}
			}
		}

		> .button-text
		{
			.m-borderRightRadius(0);
		}

		> .button-menu
		{
			.m-borderLeftRadius(0);
			padding-right: xf-default(@xf-buttonBase--padding-right, 0);// * (2/3);
			padding-left: xf-default(@xf-buttonBase--padding-left, 0);// * (2/3);

			&:after
			{
				.m-menuGadget(); // .58em
				opacity: .5;
			}
		}
	}
}

.buttonGroup
{
	display: inline-block;
	vertical-align: top;
	.m-clearFix();

	&.buttonGroup--aligned
	{
		vertical-align: middle;
	}

	> .button
	{
		float: left;

		&:not(:first-child)
		{
			border-left: none;
		}

		&:not(:first-child):not(:last-child)
		{
			border-radius: 0;
		}

		&:first-child:not(:last-child)
		{
			.m-borderRightRadius(0);
		}

		&:last-child:not(:first-child)
		{
			.m-borderLeftRadius(0);
		}
	}

	> .buttonGroup-buttonWrapper
	{
		float: left;

		&:not(:first-child) > .button
		{
			border-left: none;
		}

		&:not(:first-child):not(:last-child) > .button
		{
			border-radius: 0;
		}

		&:first-child:not(:last-child) > .button
		{
			.m-borderRightRadius(0);
		}

		&:last-child:not(:first-child) > .button
		{
			.m-borderLeftRadius(0);
		}
	}
}

.toggleButton
{
	> input
	{
		display: none;
	}

	> span
	{
		.xf-buttonDisabled();
		.m-buttonBorderColorVariation(xf-default(@xf-buttonDisabled--background-color, transparent));
	}

	&.toggleButton--small > span
	{
		font-size: @xf-fontSizeSmaller;
		padding: @xf-paddingSmall;
	}

	> input:checked + span
	{
		.xf-buttonDefault();
		.m-buttonBlockColorVariationSimple(xf-default(@xf-buttonDefault--background-color, transparent));
	}
}

.u-scrollButtons
{
	position: fixed;
	bottom: 30px;
	right: (@xf-pageEdgeSpacer) / 2;

	.has-hiddenscroll &
	{
		right: 20px;
	}

	z-index: @zIndex-9;

	.m-transition(opacity; @xf-animationSpeed);
	opacity: 0;
	display: none;

	&.is-transitioning
	{
		display: block;
	}

	&.is-active
	{
		display: block;
		opacity: 1;
	}

	.button
	{
		display: block;

		+ .button
		{
			margin-top: (@xf-pageEdgeSpacer) / 2;
		}
	}
}
';
	return $__finalCompiled;
});