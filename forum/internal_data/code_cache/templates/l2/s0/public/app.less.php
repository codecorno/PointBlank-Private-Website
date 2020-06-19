<?php
// FROM HASH: 7aa8864a11192f5dfc4e1186da50fffb
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '@_nav-elTransitionSpeed: @xf-animationSpeed;
@_navAccount-hPadding: @xf-paddingLarge;

.m-pageWidth(@min-width: @xf-pageEdgeSpacer)
{
	max-width: @xf-pageWidthMax;
	padding: 0 @xf-pageEdgeSpacer;
	margin: 0 auto;
}

.m-pageInset(@defaultPadding: @xf-pageEdgeSpacer)
{
	padding-left: @defaultPadding;
	padding-right: @defaultPadding;

	// iPhone X/Xr/Xs support
	@supports(padding: max(0px))
	{
		&
		{
			padding-left: ~"max(@{defaultPadding}, env(safe-area-inset-left))";
			padding-right: ~"max(@{defaultPadding}, env(safe-area-inset-right))";
		}
	}
}

.u-anchorTarget
{
	.m-stickyHeaderConfig(@xf-publicNavSticky);
	height: (@_stickyHeader-height + @_stickyHeader-offset);
	margin-top: -(@_stickyHeader-height + @_stickyHeader-offset);
}

.p-pageWrapper
{
	position: relative;
	display: flex;
	flex-direction: column;
	min-height: 100vh;
	.xf-pageBackground();

	.is-modalOverlayOpen &
	{
		& when (unit(xf-default(@xf-overlayMaskBlur, 0)) > 0)
		{
			filter: blur(@xf-overlayMaskBlur);
		}
	}
}

// RESPONSIVE HEADER

.p-offCanvasAccountLink
{
	display: none;
}

@media (max-width: 359px)
{
	.p-offCanvasAccountLink
	{
		display: block;
	}
}

@media (max-width: 359px)
{
	.p-offCanvasRegisterLink
	{
		display: block;
	}
}

' . $__templater->includeTemplate('app_staffbar.less', $__vars) . '
' . $__templater->includeTemplate('app_header.less', $__vars) . '
' . $__templater->includeTemplate('app_stickynav.less', $__vars) . '
' . $__templater->includeTemplate('app_nav.less', $__vars) . '
' . $__templater->includeTemplate('app_sectionlinks.less', $__vars) . '
' . $__templater->includeTemplate('app_body.less', $__vars) . '
' . $__templater->includeTemplate('app_breadcrumbs.less', $__vars) . '
' . $__templater->includeTemplate('app_title.less', $__vars) . '
' . $__templater->includeTemplate('app_footer.less', $__vars) . '
' . $__templater->includeTemplate('app_inlinemod.less', $__vars) . '
' . $__templater->includeTemplate('app_ignored.less', $__vars) . '
' . $__templater->includeTemplate('app_username_styles.less', $__vars) . '
' . $__templater->includeTemplate('app_user_banners.less', $__vars);
	return $__finalCompiled;
});