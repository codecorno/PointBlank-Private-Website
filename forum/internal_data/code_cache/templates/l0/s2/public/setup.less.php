<?php
// FROM HASH: b100971074584982d812601e3d4f7ff0
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '// Note that this file should never output any CSS directly. It should contain variables and mixins only.
// Output should go into core.less.

// ################################ VARIABLES ###############################

// BLOCKS
@block-borderRadius-inner: max(@xf-blockBorderRadius - 1px, 0px);
@block-noStripSel: ~\'.block-container:not(.block-container--noStripRadius)\';

// HELPERS
@zIndex-1: 1 * (@xf-zIndexMultiplier);
@zIndex-2: 2 * (@xf-zIndexMultiplier);
@zIndex-3: 3 * (@xf-zIndexMultiplier);
@zIndex-4: 4 * (@xf-zIndexMultiplier);
@zIndex-5: 5 * (@xf-zIndexMultiplier);
@zIndex-6: 6 * (@xf-zIndexMultiplier);
@zIndex-7: 7 * (@xf-zIndexMultiplier);
@zIndex-8: 8 * (@xf-zIndexMultiplier);
@zIndex-9: 9 * (@xf-zIndexMultiplier);

// AVATARS
@_avatarBaseSize: 96px;
@avatar-xxs: (@_avatarBaseSize) / 4;
@avatar-xs:  (@_avatarBaseSize) / 3;
@avatar-s:   (@_avatarBaseSize) / 2;
@avatar-m:   (@_avatarBaseSize);
@avatar-l:   (@_avatarBaseSize) * 2;
@avatar-o:   (@_avatarBaseSize) * 4;

@rtl: xf-isRtl;
@ltr: xf-isLtr;

// DEFAULT FONTAWESOME TYPE
@faType: \'Pro\';

// FONTAWESOME WEIGHT VALUES
@faWeight-light: 300;
@faWeight-regular: 400;
@faWeight-solid: 900;

' . $__templater->includeTemplate('setup_fa.less', $__vars) . '

// TABLES
@tablePadding: @xf-paddingSmall;

// ####################################### MIXINS ##################################

.m-clearFix()
{
	&:before,
	&:after
	{
		content: " ";
		display: table;
	}
	&:after
	{
		clear: both;
	}
}

.m-hideText()
{
	text-indent: 100%;
	overflow: hidden;
	white-space: nowrap;
	word-wrap: normal;
}

.m-overflowEllipsis()
{
	overflow: hidden;
	white-space: nowrap;
	word-wrap: normal;
	text-overflow: ellipsis;
}

.m-appendColon()
{
	&:after
	{
		content: "' . $__templater->escape($__vars['xf']['language']['label_separator']) . '";
	}

	&.is-sentence:after
	{
		content: "";
	}
}

.m-tooltipArrow(@color, @size, @variation: ~\'\', @fillColor: false)
{
	@{variation}.tooltip--top &
	{
		bottom: 0;
		left: 50%;
		margin-left: -@size;
		.m-triangleDown(@color, @size);
	}
	@{variation}.tooltip--bottom &
	{
		top: 0;
		left: 50%;
		margin-left: -@size;
		.m-triangleUp(@color, @size);
	}
	@{variation}.tooltip--right &
	{
		top: 50%;
		left: 0;
		margin-top: -@size;
		.m-triangleLeft(@color, @size);
	}
	@{variation}.tooltip--left &
	{
		top: 50%;
		right: 0;
		margin-top: -@size;
		.m-triangleRight(@color, @size);
	}

	& when (iscolor(@fillColor))
	{
		@{variation} &:after
		{
			position: absolute;
			content: \'\';
		}

		@{variation}.tooltip--top &:after
		{
			bottom: 1px;
			left: -@size + 1px;
			.m-triangleDown(@fillColor, @size - 1px);
		}

		@{variation}.tooltip--bottom &:after
		{
			top: 1px;
			left: -@size + 1px;
			.m-triangleUp(@fillColor, @size - 1px);
		}

		@{variation}.tooltip--right &:after
		{
			left: 1px;
			bottom: -@size + 1px;
			.m-triangleLeft(@fillColor, @size - 1px);
		}

		@{variation}.tooltip--left &:after
		{
			right: 1px;
			bottom: -@size + 1px;
			.m-triangleRight(@fillColor, @size - 1px);
		}
	}
}

.m-triangleUp(@color; @size)
{
	border: @size solid transparent;
	border-top-width: 0;
	border-bottom-color: @color;
}

.m-triangleDown(@color; @size)
{
	border: @size solid transparent;
	border-bottom-width: 0;
	border-top-color: @color;
}

.m-triangleLeft(@color; @size)
{
	border: @size solid transparent;
	border-left-width: 0;
	border-right-color: @color;
}

.m-triangleRight(@color; @size)
{
	border: @size solid transparent;
	border-right-width: 0;
	border-left-color: @color;
}

.m-dropShadow(@x: 1px; @y: 2px; @blur: 2px; @spread: 0; @alpha: 0.25)
{
	box-shadow: @x @y @blur @spread rgba(0, 0, 0, @alpha);
}

.m-textOutline(@fillColor: white; @strokeColor: black; @width: 1px;)
{
	-webkit-text-fill-color: @fillColor;
	-webkit-text-stroke-color: @strokeColor;
	-webkit-text-stroke-width: @width;
}

.m-gradient(@startColor; @stopColor; @fallback; @startPos: 0%; @endPos: 100%)
{
	background: @fallback;
	background: linear-gradient(to bottom, @startColor @startPos, @stopColor @endPos);
}

.m-gradientHorizontal(@startColor; @stopColor; @fallback; @startPos: 0%; @endPos: 100%)
{
	background: @fallback;

	& when(@ltr)
	{
		background: linear-gradient(to right, @startColor @startPos, @stopColor @endPos);
	}
	& when(@rtl)
	{
		background: linear-gradient(to left, @startColor @startPos, @stopColor @endPos);
	}
}

.m-tabSize(@size: 4)
{
	-moz-tab-size: @size;
	tab-size: @size;
}

.m-transition(@props: all; @duration: @xf-animationSpeed; @timing: ease; @delay: 0; @raw-input: false)
{
	// adapted from https://stackoverflow.com/a/20810461 @ScottS

	.output() when (@raw-input = false)
	{
		@propsLength: length(@props);
		@durationLength: length(@duration);
		@timingLength: length(@timing);
		@delayLength: length(@delay);

		.buildString(@i, @s: ~\'\') when (@i <= @propsLength)
		{
			@prop: extract(@props, @i);

			.setDuration() when (@i <= @durationLength) { @dur: extract(@duration, @i); }
			.setDuration() when (@i >  @durationLength) { @dur: extract(@duration, @durationLength); }
			.setDuration();

			.setEasing() when (@i <= @timingLength) { @time: extract(@timing, @i); }
			.setEasing() when (@i >  @timingLength) { @time: extract(@timing, @timingLength); }
			.setEasing();

			.setDelay() when (@i <= @delayLength) { @del: extract(@delay, @i); }
			.setDelay() when (@i >  @delayLength) { @del: extract(@delay, @delayLength); }
			.setDelay();

			.setDivider() when (@i > 1) { @divider: ~\'@{s},\'; }
			.setDivider() when (@i = 1) { @divider: ~\'\'; }
			.setDivider();

			.buildString((@i + 1), @divider @prop @dur @time);
		}

		.buildString(1);

		.buildString(@i, @s: ~\'\') when (@i > @propsLength)
		{
			.compact(@s);
		}
	}

	.output() when not (@raw-input = false)
	{
		.compact(@raw-input);
	}

	.compact(@string)
	{
		-webkit-transition: @string;
		//-moz-transition: @string;
		//-ms-transition: @string;
		//-o-transition: @string;
		transition: @string;
	}

	.output();
}

.m-transitionProperty(@props)
{
	-webkit-transition-property: @props;
	transition-property: @props;
}

.m-animation(@props)
{
	-webkit-animation: @props;
	animation: @props;
}

.m-transform(@props)
{
	-webkit-transform: @props;
	-ms-transform: @props;
	transform: @props;
}

.m-keyframes(@name, @rules)
{
	@-webkit-keyframes @name { @rules(); }
	@keyframes @name { @rules(); }
}

.m-columns(@count; @breakWidth: 0; @gap: 1em)
{
	-moz-column-count: @count;
	-webkit-column-count: @count;
	column-count: @count;

	-moz-column-gap: @gap;
	-webkit-column-gap: @gap;
	column-gap: @gap;

	& when(@breakWidth > 0)
	{
		@media (max-width: @breakWidth)
		{
			-moz-column-count: 1;
			-webkit-column-count: 1;
			column-count: 1;
		}
	}
}

.m-columnBreakAvoid()
{
	-webkit-column-break-inside: avoid;
	break-inside: avoid-column;
	page-break-inside: avoid;
}

.m-borderLeftRadius(@radius)
{
	border-top-left-radius: @radius;
	border-bottom-left-radius: @radius;
}

.m-borderRightRadius(@radius)
{
	border-top-right-radius: @radius;
	border-bottom-right-radius: @radius;
}

.m-borderTopRadius(@radius)
{
	border-top-left-radius: @radius;
	border-top-right-radius: @radius;
}

.m-borderBottomRadius(@radius)
{
	border-bottom-left-radius: @radius;
	border-bottom-right-radius: @radius;
}

.m-hiddenLinks()
{
	a
	{
		color: inherit;
		text-decoration: none;

		&:hover
		{
			text-decoration: underline;
		}
	}
}

.m-textColoredLinks()
{
	a
	{
		color: inherit;
		text-decoration: underline;
	}
}

.m-placeholder(@rules)
{
	&::-webkit-input-placeholder { @rules(); }
	&::-moz-placeholder { @rules(); }
	&:-moz-placeholder { @rules(); }
	&:-ms-input-placeholder { @rules(); }
}

.m-autoCompleteList(@wide: false)
{
	.m-listPlain();
	cursor: default;

	.xf-menu();
	min-width: 180px;
	& when(@wide = true)
	{
		min-width: 250px;
	}
	max-width: 95%;

	border: @xf-borderSize solid @xf-borderColor;
	.m-dropShadow(0, 3px, 5px, 0, .3);

	li
	{
		padding: @xf-paddingMedium;
		line-height: 24px;

		.m-clearFix();

		&.is-selected
		{
			background: @xf-contentHighlightBg;
		}

		.autoCompleteList-icon
		{
			float: left;
			margin-right: @xf-paddingMedium;
			width: 24px;
			height: 24px;
		}
	}
}

.m-listPlain()
{
	list-style: none;
	margin: 0;
	padding: 0;
}

// makes direct children display: inline-block, removing white-space between elements (mostly used on ul > li)
.m-inlineBlocks(@fontSize: @xf-fontSizeNormal)
{
	font-size: 0;

	> *
	{
		display: inline-block;
		font-size: @fontSize;
	}
}

.m-tabsTogether(@fontSize: @xf-fontSizeNormal)
{
	font-size: 0;

	.tabs-tab,
	.tabs-extra,
	.hScroller-action
	{
		font-size: @fontSize;
	}
}

.m-hiddenEl(@transition: true)
{
	display: none;

	&.is-active
	{
		display: block;
	}

	& when(@transition = true)
	{
		.m-transitionFadeDown();
	}
}

.m-transitionFade(@speed: @xf-animationSpeed)
{
	display: none;
	opacity: 0;

	.m-transition(all, -xf-opacity; @speed);

	&.is-active
	{
		display: block;
		opacity: 1;
	}

	&.is-transitioning
	{
		display: block;
	}
}

.m-transitionFadeDown(@speed: @xf-animationSpeed)
{
	.m-transitionFade(@speed);

	overflow-y: hidden;
	height: 0;

	.m-transitionProperty(all, -xf-height;);

	&.is-active
	{
		height: auto;
		overflow-y: visible;
	}

	&.is-transitioning
	{
		overflow-y: hidden;
	}
}

.m-visuallyHidden()
{
	position: absolute;
	height: 1px;
	width: 1px;
	margin: -1px;
	padding: 0;
	border: 0;
	clip: rect(0 0 0 0);
	overflow: hidden;
}

.m-faBase(@type: @faType; @weight: @xf-fontAwesomeWeight)
{
	.m-defaultFaWeight() when (@type = \'Brands\')
	{
		@weight: @faWeight-regular;
	}
	// else use given @weight

	.m-defaultFaWeight();

	font-family: \'Font Awesome 5 @{type}\';
	font-size: inherit;
	font-style: normal;
	font-weight: @weight;
	text-rendering: auto;
	-webkit-font-smoothing: antialiased;
	-moz-osx-font-smoothing: grayscale;
}

.m-faContent(@content, @width: false, @direction: false)
{
	& when (@direction = false)
	{
		content: @content;
	}
	& when (@direction = ltr)
	{
		-ltr-content: @content;
	}
	& when (@direction = rtl)
	{
		-rtl-content: @content;
	}
	& when (isnumber(@width))
	{
		display: inline-block;
		width: @width;
	}
}

.m-faBefore(@icon, @width: false)
{
	&:before
	{
		.m-faContent(@icon, @width);
	}
}

.m-faAfter(@icon, @width: false)
{
	&:after
	{
		.m-faContent(@icon, @width);
	}
}

.m-menuGadget(@separate: false, @faWidth: false, @faWeight: @faWeight-solid)
{
	.m-faBase();
	.m-faContent("@{fa-var-caret-down}", @faWidth);
	font-weight: @faWeight;

	& when (@separate = true)
	{
		margin-left: .2em;
	}
	& when (isnumber(@separate))
	{
		margin-left: @separate;
	}

	unicode-bidi: isolate; // this is needed to ensure correct positioning in RTL with LTR text
}

.m-content(@content, @direction: false, @separateDirection: false, @separateWidth: false)
{
	& when (@direction = false)
	{
		content: @content;
	}
	& when (@direction = ltr)
	{
		-ltr-content: @content;
	}
	& when (@direction = rtl)
	{
		-rtl-content: @content;
	}
	& when (@separateDirection = left)
	{
		& when (isnumber(@separateWidth))
		{
			margin-right: @separateWidth;
		}
		& when not(isnumber(@separateWidth))
		{
			margin-right: .2em;
		}
	}
	& when (@separateDirection = right)
	{
		& when (isnumber(@separateWidth))
		{
			margin-left: @separateWidth;
		}
		& when not(isnumber(@separateWidth))
		{
			margin-left: .2em;
		}
	}
}

.m-buttonIcon(@icon, @width: false)
{
	& > .button-text
	{
		.m-faBefore(@icon, @width);
	}
}

.m-avatarSize(@avatarSize)
{
	width: @avatarSize;
	height: @avatarSize;
	font-size: round((@avatarSize) * (@xf-avatarDynamicTextPercent / 100));
}

.m-buttonBase()
{
	display: inline-block; // maintain this just in case for old browsers

	display: inline-flex;
	align-items: center;
	justify-content: center;
	text-decoration: none;
	cursor: pointer;
	border: @xf-borderSize solid transparent;
	white-space: nowrap;
	.m-transition(background-color);
	.xf-buttonBase();
}

.m-buttonColorVariation(@bgColor; @textColor)
{
	color: @textColor;
	background-color: @bgColor;
	.m-buttonBorderColorVariation(@bgColor);

	&:hover,
	&:active,
	&:focus
	{
		background-color: saturate(xf-intensify(@bgColor, 4%), 12%);
	}
}

.m-buttonBlockColorVariationSimple(@color)
{
	.m-buttonBorderColorVariation(@color);

	&:not(.button--splitTrigger),
	&.button--splitTrigger > .button-text,
	&.button--splitTrigger > .button-menu
	{
		&:hover,
		&:focus,
		&:active
		{
			background-color: saturate(xf-intensify(@color, 4%), 12%);
		}
	}
}

.m-buttonBorderColorVariation(@borderColor)
{
	border-color: xf-diminish(@borderColor, 5%) xf-intensify(@borderColor, 5%) xf-intensify(@borderColor, 5%) xf-diminish(@borderColor, 5%);

	&.button--splitTrigger
	{
		> .button-text { border-right-color: xf-intensify(@borderColor, 5%); }
		> .button-menu { border-left-color: xf-diminish(@borderColor, 5%); }
	}
}

// this will be replaced by the CSS renderer with the correct background-image rule
.m-selectGadgetColor(@color: black)
{
	-xf-select-gadget: @color;
}

.m-hScrollerActionColorVariation(@background, @text, @textHover)
{
	color: @text;

	&:hover
	{
		color: @textHover;
	}

	&.hScroller-action--start
	{
		.m-gradientHorizontal(@background, fade(@background, 0%), @background, 66%, 100%);
	}

	&.hScroller-action--end
	{
		.m-gradientHorizontal(fade(@background, 0%), @background, @background, 0%, 33%);
	}
}

// Fix for iOS zoom on input focus.
.m-inputZoomFix()
{
	@media (max-width: 568px)
	{
		font-size: 16px;
	}
}

.m-checkboxAligner()
{
	vertical-align: -2px;
}

.m-highResolution(@rules)
{
	@media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 144dpi)
	{
		@rules();
	}
}

.m-fixedWidthFlex(@width)
{
	flex: 0 0 @width;
}

.m-labelVariation(@color; @bg; @border: false)
{
	color: @color;
	background: @bg;
	border-color: xf-intensify(@bg, 10%);

	& when (iscolor(@border))
	{
		border-color: @border;
	}

	a&:hover,
	a:hover &
	{
		background: xf-intensify(@bg, 4%);
		border-color: xf-intensify(@bg, 14%);

		& when (iscolor(@border))
		{
			border-color: xf-intensify(@border, 4%);
		}
	}
}

.m-tableBase()
{
	> table
	{
		border: none;
		border-collapse: collapse;
		empty-cells: show;
		max-width: 100%;

		> thead, > tbody
		{
			> tr
			{
				> th
				{
					background: @xf-paletteColor1;
					border: @xf-borderSize solid @xf-borderColor;
					border-bottom-color: @xf-borderColorFeature;
					border-bottom-width: @xf-borderSizeMinorFeature;
					padding: @tablePadding;
				}

				> td
				{
					background: @xf-contentBg;
					border: @xf-borderSize solid @xf-borderColor;
					padding: @tablePadding;
				}
			}
		}
	}
}

// STICKY HEADER STUFF
// Note that to access the height and offset, you need to call the mixin and use the variables locally.

@header-navHeight: 2 * (@xf-publicNavPaddingV)
	+ (xf-default(@xf-publicNav--font-size, @xf-fontSizeNormal) * (@xf-lineHeightDefault));
@header-subNavHeight: 2 * (@xf-publicSubNavPaddingV)
	+ (xf-default(@xf-publicSubNav--font-size, @xf-fontSizeNormal) * (@xf-lineHeightDefault))
	+ xf-default(@xf-publicSubNav--border-top-width, 0)
	+ xf-default(@xf-publicSubNav--border-bottom-width, 0);

.m-stickyHeaderConfig(@type)
{
	@_stickyHeader-offset: 10px;
	@_stickyHeader-height: 0;
}
.m-stickyHeaderConfig(@type) when(@type = primary)
{
	@_stickyHeader-height: @header-navHeight;
}
.m-stickyHeaderConfig(@type) when(@type = all)
{
	@_stickyHeader-height: @header-navHeight + @header-subNavHeight;
}

' . $__templater->includeTemplate('setup_custom.less', $__vars);
	return $__finalCompiled;
});