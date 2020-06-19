<?php
// FROM HASH: 91db4cd81f80294b1959c4dc89c41b07
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '/* setup_fa.less */

@fa-var-login: "\\f090";
@fa-var-login-alt: "\\f2f6";
@fa-var-register: "\\f234";

/* app_body.less */

.p-body
{
	flex-direction: column;
	width: 100%;
	.xf-nlPageBody(); 
}

/* Floating content mode */
.p-body-inner {
	padding-top: @xf-elementSpacer;
	padding-bottom: @xf-elementSpacer;
	margin: 0 auto;
	
	.boxedContent & {
		.xf-nlPageBodyInner();
	}
	.floatingContent & {
		.xf-nlPageBodyInnerFloating();
	}
}

@media (max-width: @xf-responsiveWide)
{
	.p-body-main--withSideNav,
	.p-body-main--withSidebar,
	.sidebarLeft .p-body-main--withSideNav,
	.sidebarLeft .p-body-main--withSidebar
	{
		.p-body-content { padding: 0; }
		.p-body-content { padding: 0 !important; }
	}
}

/* app_footer.less */

.p-footer
{
	a:hover
	{
		.xf-nlPublicFooterLinkHover();
	}
}

/* app_nav.less */

.p-nav
{
	.p-navSticky.is-sticky &
	{
		.p-nav-list .p-navEl.is-selected,
		.p-account
		{
		}
	}
}

.p-nav .p-nav-menuTrigger
{
	margin-left: auto;
	padding: 0;
}

.p-nav-smallLogo
{
	max-width: @xf-nlNavigationSmallLogoMaxWidth;

	img
	{
		display: block;
		max-height: none;
	}
}

.p-nav-list
{
	&:before,
	&:after
	{
		width: 0;
	}

	.p-navEl
	{
		transition: none;

		&.is-selected
		{
			.p-navEl-link {
				
			}
			.has-sectionLinks & .p-navEl-link
			{
			}
			.has-sectionLinks & .p-navEl-splitTrigger
			{
			}
		}

		&:not(.is-selected):not(.is-menuOpen)
		{
			&:hover,
			.p-navEl-link:hover,
			.p-navEl-splitTrigger:hover
			{
			}
		}

		&.is-menuOpen
		{
		}
	}

	.p-navEl-link,
	.p-navEl-splitTrigger
	{
		padding-top: 0;
		padding-bottom: 0;
		height: @xf-nlNavTabHeight;
		line-height: @xf-nlNavTabHeight;
	}
	.hide-sectionLinks & {
		.p-navEl.is-selected .p-navEl-splitTrigger {
			display: block;
		}
		
		.p-navEl-link.p-navEl-link--splitMenu {
			padding-right: ((@xf-publicNavPaddingH) / 4);
		}
	}
}


@media (max-width: @xf-publicNavCollapseWidth)
{
	.has-js
	{
		.p-nav {
			.xf-nlPublicNavResponsive();
		}

		.p-nav .p-nav-menuTrigger
		{
			.xf-nlPublicNavResponsiveMenuTrigger();
		}

		.p-nav-smallLogo
		{
			
			&.enhance img {
				image-rendering: pixelated;
			}
		}

		.p-nav-opposite .p-navgroup-link {
			color: @xf-nlPublicNavResponsiveMenuTrigger--color;
		}
	}
}

// ACCOUNT/VISITOR/SEARCH SECTION

.p-nav-opposite
{
	margin-right: 0
}

.p-navgroup
{
	float: left;
	.m-clearFix();
	background: transparent none;
	border: 0;
}

.p-navgroup-link
{
	border-left: none;
	.xf-publicNavTab();
	height: @xf-nlNavTabHeight;
	line-height: @xf-nlNavTabHeight;
	padding: 0 ((@xf-publicNavPaddingH) / 2);


	&:hover
	{
		text-decoration: none;
		background: transparent none;
		.xf-publicNavTabHover();
	}

	&.p-navgroup-link--user
	{

		.avatar
		{
			vertical-align: middle;
		}
	}

	&.badgeContainer
	{

		&:after
		{
			.xf-nlVisitorTabBadge();
		}

	}

	&.p-navgroup-link--logIn i:after
	{
		.m-faContent(@fa-var-login, 1em);
	}
	
	&.p-navgroup-link--register i:after
	{
		.m-faContent(@fa-var-register, 1em);
	}
}

.p-navgroup--guest .p-navgroup-link--iconic i:after {
    margin-right: 4px;
}

/* app_header.less */

.p-header {
	position: relative;
}
.p-header-content
{
	.xf-nlPublicHeaderContent();
}
.p-header-logo
{
	&.p-header-logo--center
	{
		margin: 0 auto;
		text-align: center;
	}
	&.p-header-logo--image
	{
		img
		{
			display: block;
		}
	}
}

/* app_staffbar.less */

.p-staffBar-inner
{

}

/* app_user_banners.less */

.userBanner
{
	.xf-nlUserBannerBase();

	&.userBanner--staff,
	&.userBanner--primary
	{
		.xf-nlStaffBanner();
	}
}

/* app_sectionlinks.less */

.p-sectionLinks
{
	overflow: hidden;

	&.p-sectionLinks--empty
	{
		display: none;
	}
	.hide-sectionLinks & {
		display: none;
	}
}

.p-sectionLinks-list
{
	.p-navEl
	{

		&:hover
		{
			a
			{
				color: inherit;
			}
		}
	}
	.p-navEl-link,
	.p-navEl-splitTrigger
	{
		padding-top: 0;
		padding-bottom: 0;
	}
	.p-navEl-link
	{
		.xf-nlSubNavigationLink();
	}
	.p-navEl-splitTrigger
	{
		padding: 0;
		height: @xf-nlSubNavLinkHeight;
		line-height: @xf-nlSubNavLinkHeight;
	}
}';
	return $__finalCompiled;
});