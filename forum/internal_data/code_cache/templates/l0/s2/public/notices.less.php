<?php
// FROM HASH: 65bcb92c79728705f905e7688f5ccf7a
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->func('property', array('styleType', ), false) == 'light') {
		$__finalCompiled .= '
@_notice-darkBg: xf-intensify(@xf-contentBg, 10%);
@_notice-lightBg: xf-diminish(@xf-contentBg, 10%);
';
	} else {
		$__finalCompiled .= '
@_notice-darkBg: xf-diminish(@xf-contentBg, 10%);
@_notice-lightBg: xf-intensify(@xf-contentBg, 10%);
';
	}
	$__finalCompiled .= '
@_notice-floatingFade: 80%;
@_notice-imageSize: 48px;
@_notice-paddingV: @xf-nlNoticePaddingV;
@_notice-paddingH: @xf-nlNoticePaddingH;

.notices
{
	.m-listPlain();

	&.notices--block
	{
		.notice
		{
			margin-bottom: ((@xf-elementSpacer) / 2);
		}
	}

	&.notices--floating
	{
		// assumed to be within u-bottomFixer
		margin: 0 20px 0 auto;
		width: 300px;
		max-width: 100%;
		z-index: @zIndex-8;

		@media (max-width: 340px)
		{
			margin-right: 10px;
		}

		.notice
		{
			margin-bottom: 20px;
		}
	}

	&.notices--scrolling
	{
		display: flex;
		align-items: stretch;
		overflow: hidden;
		.xf-blockBorder();
		margin-bottom: ((@xf-elementSpacer) / 2);

		&.notices--isMulti
		{
			margin-bottom: ((@xf-elementSpacer) / 2) + 20px;
		}

		.notice
		{
			width: 100%;
			flex-grow: 0;
			flex-shrink: 0;
			/* border: none; */
		}
	}
}

.noticeScrollContainer
{
	margin-bottom: ((@xf-elementSpacer) / 2);

	.lSSlideWrapper
	{
		// .xf-blockBorder();
		background: @xf-contentBg;
	}

	.notices.notices--scrolling
	{
		border: none;
		margin-bottom: 0;
	}
}

.notice
{
	.m-clearFix();
	position: relative;

	.xf-blockBorder();
	.xf-nlNoticeContent();

	&.notice--primary
	{
		.xf-contentHighlightBase();
		.xf-nlNoticePrimary();

		a:not(.button):not(.button--notice) {
			.xf-nlNoticePrimaryLink();
		}
		a:not(.button):not(.button--notice):hover {
			.xf-nlNoticePrimaryLinkHover();
		}
	}

	&.notice--accent
	{
		.xf-contentAccentBase();
		.xf-nlNoticeAccent();
		
		a:not(.button):not(.button--notice) {
			.xf-nlNoticeAccentLink();
		}
		a:not(.button):not(.button--notice):hover {
			.xf-nlNoticeAccentLinkHover();
		}
	}

	&.notice--dark
	{
		background: @_notice-darkBg;
		.xf-nlNoticeDark();
		
		a:not(.button):not(.button--notice) {
			.xf-nlNoticeDarkLink();
		}
		a:not(.button):not(.button--notice):hover {
			.xf-nlNoticeDarkLinkHover();
		}
	}

	&.notice--light
	{
		background: @_notice-lightBg;
		.xf-nlNoticeLight();
		
		a:not(.button):not(.button--notice) {
			.xf-nlNoticeLightLink();
		}
		a:not(.button):not(.button--notice):hover {
			.xf-nlNoticeLightLinkHover();
		}
	}

	&.notice--enablePush
	{
		display: none;

		@media (max-width: @xf-responsiveWide)
		{
			padding: @xf-paddingSmall @xf-paddingSmall @xf-paddingLarge;
			font-size: @xf-fontSizeSmall;
		}
	}

	&.notice--cookie
	{
		@media (max-width: @xf-responsiveWide)
		{
			.notice-content
			{
				padding: @xf-paddingSmall @xf-paddingSmall @xf-paddingLarge;
				font-size: @xf-fontSizeSmaller;

				.button--notice
				{
					font-size: @xf-fontSizeSmaller;
					padding: @xf-paddingSmall @xf-paddingMedium;

					.button-text
					{
						font-size: @xf-fontSizeSmaller;
					}
				}
			}
		}
	}

	.notices--block &
	{
		font-size: @xf-fontSizeNormal;
		border-radius: @xf-blockBorderRadius;
	}

	.notices--floating &
	{
		font-size: @xf-fontSizeSmallest;
		border-radius: @xf-borderRadiusMedium;
		box-shadow: 1px 1px 3px rgba(0,0,0, 0.25);

		&.notice--primary
		{
			// background-color: fade(@xf-contentHighlightBase--background-color, @_notice-floatingFade);
		}

		&.notice--accent
		{
			// background-color: fade(@xf-contentAccentBase--background-color, @_notice-floatingFade);
		}

		&.notice--dark
		{
			// background-color: fade(@_notice-darkBg, @_notice-floatingFade);
		}

		&.notice--light
		{
			// background-color: fade(@_notice-lightBg, @_notice-floatingFade);
		}

		.has-js &
		{
			display: none;
		}
	}

	&.notice--hasImage
	{
		.notice-content
		{
			margin-left: ((@_notice-imageSize) + (@_notice-paddingH));
			min-height: ((@_notice-imageSize) + (@_notice-paddingV) * 2);
		}
	}

	// note: visibility hidden is used by the JS to detect when responsiveness is hiding a notice

	@media (max-width: @xf-responsiveWide)
	{
		&.notice--hidewide:not(.is-vis-processed)
		{
			display: none;
			visibility: hidden;
		}
	}
	@media (max-width: @xf-responsiveMedium)
	{
		&.notice--hidemedium:not(.is-vis-processed)
		{
			display: none;
			visibility: hidden;
		}
	}
	@media (max-width: @xf-responsiveNarrow)
	{
		&.notice--hidenarrow:not(.is-vis-processed)
		{
			display: none;
			visibility: hidden;
		}
	}
}

.notice-image
{
	float: left;
	padding: @_notice-paddingV 0 @_notice-paddingH @_notice-paddingV;

	img
	{
		max-width: @_notice-imageSize;
		max-height: @_notice-imageSize;
	}
}

.notice-content
{
	padding: @_notice-paddingV @_notice-paddingH;

	a.notice-dismiss
	{
		&:before
		{
			.m-faBase();

			.m-faContent(@fa-var-times, .69em);
		}

		float: right;

		color: inherit;
		font-size: 16px;
		line-height: 1;
		height: 1em;
		box-sizing: content-box;
		padding: 0 0 5px 5px;

		opacity: .5;
		.m-transition(opacity);

		cursor: pointer;

		&:hover
		{
			text-decoration: none;
			opacity: 1;
		}

		.notices--floating &
		{
			font-size: 14px;
		}
	}
}';
	return $__finalCompiled;
});