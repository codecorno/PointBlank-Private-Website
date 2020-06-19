<?php
// FROM HASH: 321cd1ecad625c8b6375c1cd26c63968
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '/* Mixin */

.m-pageWidth(@min-width: @xf-pageEdgeSpacer)
{
	max-width: @xf-pageWidthMax;
	width: 100%;
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

/* Place */

.message-userExtras {
	.xf-nlMessageUserExtrasGroup();
	
	.pairs.pairs--justified > dt {
		.xf-nlMessageUserExtrasLabel();
	}
}

// Normalizes with block tab headers
.tabs--standalone .tabs-tab {
	line-height: normal;
}

// New table adjustment
.dataList-table {
    border-collapse: collapse;
}
.dataList-row.dataList-row--header .dataList-cell {
    border: none;
}

/* Hover transitions */

.hoverTransitions {
	
	a {
		transition: ' . $__templater->func('property', array('nlHoverTransitionSpeed', ), true) . 's color;
	}
	
	.iconic > input[type=checkbox] + i:before,
	.iconic > input[type=checkbox] + i:after {
		transition: ' . $__templater->func('property', array('nlHoverTransitionSpeed', ), true) . 's opacity;
	}

	button,
	.button,
	input[type="submit"],
	.p-staffBar-link,	
	.menu-row,
	a.menu-linkRow,
	.blockLink,
	.categoryList-itemRow,
	.block-row.block-row--separated,
	.pageNav-page,
	.p-breadcrumbs--xf1 li,
	.p-breadcrumbs--xf1 .arrow,
	.p-breadcrumbs--xf1 .arrow span,
	.tabs-tab,
	.messageButtonLinks .actionBar-set--external .actionBar-action,
	.inputGroup-text
	{
		transition: ' . $__templater->func('property', array('nlHoverTransitionSpeed', ), true) . 's all;
	}
	// correct for a hovers
	.pageNav-page a,
	.p-breadcrumbs--xf1 > li a {
		transition: none;
	}
	
	input[type="search"],
	input[type="email"],
	input[type="password"]
	{
		transition: ' . $__templater->func('property', array('nlHoverTransitionSpeed', ), true) . 's background, ' . $__templater->func('property', array('nlHoverTransitionSpeed', ), true) . 's color, ' . $__templater->func('property', array('nlHoverTransitionSpeed', ), true) . 's box-shadow;
	}

	.hover-fx {
		position: relative;
		overflow: hidden;
		
		&.h-float-down, &.h-float-up {
			transition: ' . $__templater->func('property', array('nlHoverTransitionSpeed', ), true) . 's margin;
		}
		&.h-float-up:hover {
			margin-top: -10px;
			margin-bottom: 10px;
		}
		&.h-float-down:hover {
			margin-bottom: -10px;
			margin-top: 10px;
		}
		a& {
			display: block;
		}
		> img {
			display: block;
		}
	}
	
}

/* Template overrides */

/* message.less */

.message-cell
{
	&.message-cell--main
	{
		.xf-nlMessageContent();
	}
	&.message-cell--user .message-userExtras {
		.xf-nlMessageUserInfo();
	}
}

@media (max-width: @xf-messageSingleColumnWidth)
{
	.message:not(.message--forceColumns)
	{

		.message-cell
		{

			&.message-cell--user
			{
				.xf-nlMessageUserBlockResponsive();
			}
		}
	}
}

.message-avatar-wrapper
{
	.message-avatar-online
	{
		.xf-nlMessageAvatarOnline();
	}
}

.message-name
{
	font-weight: inherit;
	.xf-nlMessageUsername();
	
	a {
		color: inherit;
	}
}

@media (max-width: @xf-messageSingleColumnWidth)
{
	.message:not(.message--forceColumns)
	{

		.message-name
		{
			.xf-nlMessageUsernameResponsive();
			
			a {
				color: inherit;
			}
		}
	}
}

/* node_list.less */

.node + .node {
    border-top: inherit;
}
.node-body {
	min-height: 70px;
	.xf-nlNodeRow();
}
.alternateNodes .block-body > .node:nth-of-type(even) .node-body {
	.xf-nlNodeRowAlternate();
}
.hoverNodes:not(.nodeImgBehind) {
	.node:not(.th_nodes) .node-body:hover,
	.block-body > .node:nth-of-type(even):not(.th_nodes) .node-body:hover {
		.xf-nlNodeRowHover();
	}
}
.node-body > div,
.node-body > span {
	.xf-nlNodeCell();
}

.node-icon	
{
	padding: @xf-nlNodePaddingV 0 @xf-nlNodePaddingV @xf-nlNodePaddingH;
	width: @xf-nlNodeIconBlockWidth;
	.xf-nlNodeIconBlock();

	i
	{
		line-height: 1;
		// .xf-nlNodeIconReadWrapper();

		&:before
		{
			display: inline-block;
			font-weight: @xf-nlNodeIconStyle;
			color: @xf-nodeIconReadColor;
			text-shadow: 1px 1px 0.5px fade(@xf-nlBoxShadowColor, (@xf-nlBoxShadowAlpha * 100));
			.xf-nlNodeIcon();
			
			.node--unread & {
				opacity: 1;
				color: @xf-nodeIconUnreadColor;
				text-shadow: 1px 1px 0.5px fade(@xf-nlBoxShadowColor, (@xf-nlBoxShadowAlpha * 100));
				.xf-nlNodeIconUnread();
			}
		}
		.node--forum &:before,
		.node--category &:before
		{

		}
		.node--page &:before
		{

		}
		.node--link &:before
		{

		}
	}
}

.node-main
{
	padding: @xf-nlNodePaddingV @xf-nlNodePaddingH;
	width: 100%;
	.xf-nlNodeMainBlock(); 
}
.node-stats
{
	padding: @xf-nlNodePaddingV 0;
	.xf-nlNodeStatsBlock();
	
	> dl.pairs.pairs--rows
	{
		.xf-nlNodeStatsLabelPair();
	}
	&.node-stats--iconify > dl.pairs dt {
		display: inline-block;
		width: 1em;
		padding-right: .3em;
		text-decoration: none;
		color: inherit;
		color: @xf-nlStatsSubforumsIconColor;
		/* text-shadow: 1px 1px 0 fade(@xf-nlBoxShadowColor, (@xf-nlBoxShadowAlpha * 100)); */
	}
}
.node-extra a:hover,
.node-extra-user a.username:hover,
.structItem-title a:hover {
	color: @xf-linkHoverColor;
}
.node-extra
{
	width: @xf-nlNodelastPostBlockWidth;
	padding: @xf-nlNodePaddingV @xf-nlNodePaddingH;
	.xf-nlNodeLastPost();
	.m-clearFix();
	
	.node-extra-inner {
		.xf-nlNodeLastPostInner();
	}
}
.node-extra-row .node-extra-date {
	color: inherit;
}
.node-extra-date {
}
.node-extra .node-extra-row .listInline--bullet {
    .m-overflowEllipsis();
}

a.node-extra-title {
	color: @xf-nlNodeLastPostTitleColor;
	.xf-nlNodeLastPostTitle();
}
.node-extra-user a.username {
	color: @xf-nlNodeLastPostAuthorColor;
	.xf-nlNodeLastPostAuthor();
}

h3.node-title
{
	';
	if ($__templater->func('property', array('nlNodeTitleOverflow', ), false) == 'ellipsis') {
		$__finalCompiled .= '
	.m-overflowEllipsis();
	';
	}
	$__finalCompiled .= '
	color: @xf-nlNodeTitleColor;
	.xf-nlNodeTitle();
	
	a {
		color: inherit;
	}
	a:hover {
		.xf-nlNodeTitleHover();
	}
	.node--unread &
	{
		/* font-weight: @xf-fontWeightHeavy; */
	}
}

.node-statsMeta
{
	i {
		color: @xf-nlStatsSubforumsIconColor;
	}
}

@media (max-width: @xf-responsiveMedium)
{
.node-extra
	{
		.xf-nlNodeLastPostResponsive();
	}
}

.subNodeLink
{
	&:before
	{
		color: @xf-nlStatsSubforumsIconColor;
		/* text-shadow: 1px 1px 0 @xf-nlBoxShadowColor; */
	}
	&.subNodeLink--unread
	{
		&:before
		{
			/* text-shadow: 1px 1px 0 @xf-nlBoxShadowColor; */
		}
	}
	&.subNodeLink--forum:before,
	&.subNodeLink--category:before
	{
		.m-faContent(@fa-var-folder);
	}
}


/* block_message.less */

.block--messages
{
	.block-container
	{
		.m-stripElement();
	}

	.message,
	.block-row
	{
		.xf-nlMessageWrapper();
		
		&.is-mod-selected {
			.xf-nlMessageWrapperModSelected();
		}
		+ .message,
		+ .block-row
		{
			margin-top: @xf-nlMessageSpacer;
		}
	}
}

/* lightslider.less */

.lSSlideOuter .lSPager.lSpg > li a {
	.xf-nlDotControl();
	.xf-nlNoticeDotControl();
}
.lSSlideOuter .lSPager.lSpg > li:hover a,
.lSSlideOuter .lSPager.lSpg > li.active a {
	.xf-nlDotControlHover();
	.xf-nlNoticeDotControlHover();
}


/* Place */

.structItem-parts > li:nth-child(even) {
	color: inherit;
}
.structItem-cell--latest .structItem-cell--inner > a:first-of-type {
    color: inherit;
	
	&:hover {
		color: @xf-linkHoverColor;
	}
}

.categoryList-item
{
	.categoryList
	{
		padding-left: 0;
	}
}
.categoryList-itemRow
{
	flex-grow: 1;
	.m-lineHeightNormal();
	.xf-blockLink();
	.xf-blockLinkExtended();

	&:hover
	{
		.xf-blockLinkSelected();
		.xf-blockLinkSelectedExtended();
	}
	
	a {
		color: inherit;
		transition: none;
	}
}


/* Temp */

.structItem.is-private {
	background: @xf-contentHighlightBg !important;
}

/* Gradients */

';
	if (($__templater->func('property', array('gradientTop', ), false) != null) AND ($__templater->func('property', array('gradientBottom', ), false) != null)) {
		$__finalCompiled .= '
.m-primaryGradient(@direction: to bottom; @startColor: @xf-gradientTop; @stopColor: @xf-gradientBottom;) {
	background: linear-gradient(@direction, @startColor, @stopColor);
}
';
	}
	$__finalCompiled .= '
';
	if (($__templater->func('property', array('secondaryGradientTop', ), false) != null) AND ($__templater->func('property', array('secondaryGradientBottom', ), false) != null)) {
		$__finalCompiled .= '
.m-secondaryGradient(@direction: to bottom; @startColor: @xf-secondaryGradientTop; @stopColor: @xf-secondaryGradientBottom;) {
	background: linear-gradient(@direction, @startColor, @stopColor);
}
';
	}
	$__finalCompiled .= '
';
	if (($__templater->func('property', array('lightGradientTop', ), false) != null) AND ($__templater->func('property', array('lightGradientBottom', ), false) != null)) {
		$__finalCompiled .= '
.m-lightGradient(@direction: to bottom; @startColor: @xf-lightGradientTop; @stopColor: @xf-lightGradientBottom;) {
	background: linear-gradient(@direction, @startColor, @stopColor);
}
';
	}
	$__finalCompiled .= '
';
	if (($__templater->func('property', array('darkGradientTop', ), false) != null) AND ($__templater->func('property', array('darkGradientBottom', ), false) != null)) {
		$__finalCompiled .= '
.m-darkGradient(@direction: to bottom; @startColor: @xf-darkGradientTop; @stopColor: @xf-darkGradientBottom;) {
	background: linear-gradient(@direction, @startColor, @stopColor);
}
';
	}
	$__finalCompiled .= '

/* Global */

.p-pageWrapper {
  z-index: 1;
}
h1, h2, h3, h4, h5 {
	/* line-height: 1.4; */
	line-height: normal;
	// .xf-nlHeadings();
	
	&.inline {
		margin: 0 !important;
		line-height: normal;
	}
	> i:not(.hScroller-action) {
		margin-right: 8px;
	}
}
b, strong {
	/* color: @xf-nlStrongFontColor; */
}

.page_top, .page_bot {
    width: 100%;
}
.p-body {
	.p-body-header.block-container {
		.xf-nlPageTitleBoxed();
	}
	.block-container .p-title {
		margin-bottom: 0;
	}
	.p-body-header.block-container .p-title-value {
		margin: 0 auto 0 0;
	}
	.p-body-header.block-container .p-title-pageAction {
		margin-bottom: 0;
	}
}
.p-title-pageAction {
    text-align: right;
}
/* General */

.p-title-value {
	line-height: normal;
}
.p-title-pageAction {
    text-align: right;
}
.p-description {
	.xf-nlPageDescription();
	
	i {
		.xf-nlPageDescriptionIcon();
	}
}
/*
.p-body-main p {

}
*/
.p-topBar-left {
float: left;
}
.p-topBar-right {
float: right;
}
#p-topBar:after {
    clear: both;
    display: block;
    width: 100%;
    content: " ";
}
.nlFeature_anchor {
	display: none;
}
.anchorOffset {
    display: block;
    margin-top: -60px;
    z-index: 99;
    height: 60px;
    clear: both;
}
/*
label:not(.iconic--hiddenLabel) > i {
	margin-right: @xf-paddingSmall;
}
*/

/* Buttons */

@fa-var-cart: "\\f07a";

.button,
a.button // needed for specificity over a:link
{
	&.button--icon {
		&--cart         { .m-buttonIcon(@fa-var-cart, 1em); }
		&--comment      { .m-buttonIcon(@fa-var-comment, 1em); }
		&--email	    { .m-buttonIcon(@fa-var-envelope, 1em); }
		&--heart	    { .m-buttonIcon(@fa-var-heart, 1em); }
		&--support	    { .m-buttonIcon(@fa-var-life-ring, 1em); }
		&--help	        { .m-buttonIcon(@fa-var-life-ring, 1em); }
	}
}
a.button.button--icon--rotateHover:hover i {
    animation: fa-spin 2s infinite linear;
}
.p-body-sideNav .block a.button.button--fullWidth + a.button--fullWidth {
    margin-top: 10px;
}

.buttonGroup.buttonGroup--aligned .button--small + .button--small {
    margin-left: 4px;
}

/* Staff bar */

.p-staffBar {
	
	.p-staffBar-link {
		.xf-nlPublicStaffBarLink();

		&:hover {
			.xf-nlPublicStaffBarLinkHover();
		}
	}
	.badge.badge--highlighted,
	.badgeContainer.badgeContainer--highlighted:after {
		.xf-nlPublicStaffBarBadge();
	}
}

/* Header */

@media (min-width: @xf-publicNavCollapseWidth)
{
	.headerFixed {

		.p-topBarController,
		.p-header,
		.p-navController,
		.p-sectionLinks,
		.p-staffBar,
		.p-page-header {
			max-width: @xf-pageWidthMax;
			width: 100%;
			margin: 0 auto;
		}
		.p-topBar-inner,
		&.compactHeader .p-nav-inner {
			// .m-pageInset();
		}
	}

	.headerStretch.headerFixedInner {
		.p-staffBar-inner,
		.p-topBar-inner,
		.p-page-header-inner,
		&.stretchNavigation .p-nav-inner {
			max-width: @xf-pageWidthMax;
			width: 100%;
			margin: auto;
		}
	}
	.headerStretch:not(.stretchNavigation) {
		.p-navController,
		.p-sectionLinks {
			max-width: @xf-pageWidthMax;
			width: 100%;
			margin: auto;
		}
	}
	.headerStretch:not(.stretchNavigation) {
		.p-page-header {
			max-width: @xf-pageWidthMax;
			width: 100%;
			margin: auto;
		}
	}
	.headerStretch.boxedContent .p-body {
		margin: @xf-elementSpacer auto;
	}
}

.p-topBar-inner {
	.m-clearFix();
}

/* Navigation */

.p-nav-smallLogo {
	position: relative;
	margin-right: @xf-paddingMedium;
}
a.p-navEl-link[data-nav-id="home"] {
	font-size: 0;
}
a.p-navEl-link[data-nav-id="home"]:before {
	content: "\\f015";
	.m-faBase();
	font-size: @xf-fontSizeNormal;
}
.p-navEl-splitTrigger:before {
	display: none;
}
a.p-navEl-link:before,
.p-navgroup-link.p-navgroup-link--iconic i:after {
	font-size: @xf-nlNavigationIconSize;
}
.p-navgroup.p-discovery {
	margin: 0;
	
	a:last-of-type {
		margin-right: 0;
	}
}

.p-navSticky
{
	&.is-sticky
	{
		.xf-nlPublicNavSticky();
	}
}

/* Force mobile nav */

.nav--forceMobileTrigger {
	.p-nav-scroller {
		display: none;
	}
	.p-nav-menuTrigger {
		display: inline-block;
		margin-left: 0;
	}
}

/* Off canvas menu */

.offCanvasMenu--nav
{
	.offCanvasMenu-content
	{
		padding: 0;
		height: auto;
		.xf-nlSlideNavMenu();
	}
	.offCanvasMenu-header
	{
		.xf-nlSlideMenuHeader();
		
		img {
			max-width: 60%;
			vertical-align: middle;
		}
	}
	.offCanvasMenu-linkHolder {
		.xf-nlSlideMenuItem();
	} 	
	.offCanvasMenu-linkHolder.is-selected {
		.xf-nlSlideMenuItemSelected();
	} 
}

/* Visitor tabs */

.p-navgroup-link--search .p-navgroup-linkText {
	display: none;
}
[data-logged-in="true"].hide-loggedVtabLabels {
	.p-navgroup-linkText {
		display: none;
	}
}
[data-logged-in="false"].hide-loggedOutVtabLabels {
	.p-navgroup-linkText {
		display: none;
	}
}

/* Page navigation */

.pageNav-main {
	.xf-nlPageNavContainer();
}

/* Images */

img.img--center {
    display: block;
    margin: auto;
}

img.img--fullWidth {
    max-width: 100%;
    width: 100%;
}
.img-grow img {
    max-width: 100%;
    width: 100%;
}
.img--strip img, .img--strip br {
    margin: 0;
    padding: 0;
    white-space: pre-line;
    display: inline-block;
    vertical-align: top;
}

/* Advertising */

.ad-wrapper {
    margin: auto;
    text-align: center;
}

/* Headings */

h1 {
	font-size: @xf-nlH1FontSize;
}
h2 {
	font-size: @xf-nlH2FontSize;
}
h3 {
	font-size: @xf-nlH3FontSize;
}
h4 {
	font-size: @xf-nlH4FontSize;
}
h5 {
	font-size: @xf-nlH5FontSize;
}

/* Content Shadows */

.contentShadows {
	.block-container,
	.blockMessage:not(.blockMessage--none),
	.noticeScrollContainer .lSSlideWrapper,
	.p-breadcrumbs.xf1,
	.p-breadcrumbs--xf1,
	.gridNodes.separateNodes:not(.imgNodes) .node-body,	
	.gridNodes.separateNodes.imgNodes.nodeImgBehind .node-body,
	.gridNodes.separateNodes.imgNodes.nodeImgAbove .node-wrapper,
	.gridNodes.separateNodes .node-category-header,
	.thNodes__nodeList .block-container .node-body,
	.block--messages article.message,
	.p-body-content .block-container--grid .productList-product-grid.node,
	.p-body-content .block-container--full-grid .productList-product-grid.node,
	&.media-itemDesc-below [data-type="xfmg_media"] .itemList-item
	{
		.m-dropShadow();
	}
}
.block-container .blockMessage {
    box-shadow: none;
}

/* Blocks */

.block-body {
	.m-clearFix();
}

/* Flat style blocks */
.blockStyle--flat .p-body {
	
	// Borders
	.block-container,
	.productList-product-grid {
		border: none;
	}
	// Padding
	.block-header,
	.block-minorHeader,
	.block-row,
	.porta-article-item .message-cell,
	.productList-product-grid.node .node-main,
	.productList-product-grid.node .productList-product-grid--clearfix,
	.productList-product-grid.node .productList-product-grid--updateInfo {
		padding-left: 0;
		padding-right: 0;
	}
}
.blockStyle--flat.blockHeadStyle--blockMinorHeader .overlay {
	.block-header {
		padding-left: @xf-blockHeadPaddingH;
		padding-right: @xf-blockHeadPaddingH;
	}
}

/* Check and consider. Inlines h tags to use padding only */
.block-header, .block-minorHeader {
    line-height: normal;
	
	a {
		color: inherit;
	}
}
.block-row .block-textHeader {
	margin: 0;
}
.inline-blocks {
	.block-container, .block {
		background: transparent;
		border: none;
		padding: 0;
		box-shadow: none !important;
	}
	.block-row {
		padding: 0;
	}
	h2.block-minorHeader, h3.block-minorHeader,
	h2.block-header, h3.block-header {
		display: none;
	}
}
.block-footer {
	line-height: 1;
	
	a:not(.button), a.button.button--plain {
		font-size: inherit;
		font-weight: inherit;
		text-transform: inherit;
		.xf-nlBlockFooterLink();
		
		&:hover {
			.xf-nlBlockFooterLinkHover();
		}
	}
}

.headerProxy {
	position: absolute;
	width: 100%;
	z-index: -1;
	.xf-nlHeaderWrapper();
}

/* Hide sidebar */

.hideSidebar .p-body-main--withSidebar .p-body-content {
	padding: 0 !important;
}

/* Page title */

.p-description {
	.m-clearFix();
}

/* Navigation */

.p-sectionLinks-list .p-navEl.is-menuOpen .p-navEl-link {
	color: @xf-publicSubNavElMenuOpen--color;
}

/* Notices */

.noticeScrollContainer {
	.lSSlideWrapper {
		border: none;
		background: @xf-contentBg;
	}
}
.lSSlideOuter .lSPager.lSpg {
    margin: 10px 0 0 !important;
}
.notices {
	h1, h2, h3, h4, h5 {
		margin: 0;
		padding: 0;
	}
}

/* Sidebar */

.p-body-sidebar,
.p-body-sideNav,
.resourceBody-sidebar,
.columnContainer-sidebar {
	
	/* Normalize line height */
	line-height: 1.4;
	
	.block-container {
		.xf-nlSidebarBlock();
	}
	
	iframe {
		max-width: 100%;
		width: auto;
		min-height: (@xf-sidebarWidth * .75);
		@media (min-width: @xf-responsiveNarrow)
		{
			height: auto;
		}
		@media (max-width: @xf-responsiveNarrow)
		{
			width: 100%;
		}
	}
	
	.block-row {
		.xf-nlSidebarBlockRow();
		
		&:first-of-type:not(:only-of-type) {
			padding-bottom: @xf-blockPaddingV / 2;
		}
		& + .block-row:not(:last-of-type) {
			padding-top: @xf-blockPaddingV / 2;
			padding-bottom: @xf-blockPaddingV / 2;
		}
		& + .block-row:last-of-type {
			padding-top: @xf-blockPaddingV / 2;
		}
		//&:first-of-type:not(:only-of-type) {
		//	padding-top: @xf-blockPaddingV;
		//}
		//&:last-of-type:not(:only-of-type) {
		//	padding-bottom: @xf-blockPaddingV;
		//}
	}
	
	/* Enforce link types */
	.listInline a:not(.u-concealed):first-of-type, .contentRow a:not(.u-concealed):first-of-type {
		font-size: inherit;
		.xf-nlSidebarLink();
	}
	.pairs > dt {
		.xf-nlPairedListLabel();
	}
	.contentRow h3 {
		font-family: inherit;
	}
	.sidebarAltRows & .block-body > *:nth-child(even) {
		background: @xf-nlSidebarAltRowColor;
	}
}
.p-body-sidebar,
.p-body-sideNav,
.resourceBody-sidebar,
.columnContainer-sidebar,
.flex_columns {
	
	.block-minorHeader i {
		text-indent: 0;
	}
	
	/* Truncate single line links */
	.truncateSidebarLinks & {
		.contentRow-main--close a:first-of-type {
			.m-overflowEllipsis();
		}
	}
	.block .contentRow-lesser {
		.m-overflowEllipsis();
	}
}
.categoryList-label label {
	margin: 0;
}

/* Menus */

.menu-separator {
	display: none;
}
.menu-row, .menu-footer {
	.input {
		.xf-nlMenuInput();
	}
	.input:focus {
		.xf-nlMenuInputFocus();
	}
	.inputGroup.inputGroup--joined .input + .input {
		border-left: @xf-nlMenuInput--border-color;
	}
}
.menu-footer {
	a:not(.button) {
		.xf-nlMenuFooterLink();
	}
	a:not(.button):hover {
		.xf-nlMenuFooterLinkHover();
	}
}
.menu--structural .menu-content .menu-row {
	.m-lineHeightDefault();
	
	a {
		.xf-nlMenuRowLink();
	}
	a:hover {
		.xf-nlMenuRowLinkHover();
	}
}
.menu--structural form .menu-row:hover {
	background: @xf-nlMenuRow--background-color;
}

/* Styles account menu elements */
.menu--account {
	.tabPanes li:first-of-type > .menu-row.menu-row--alt {
		.xf-nlAccountMenuHeader();
	}
	.menu-linkRow {
		.xf-nlAccountMenuLink();
		
		&:hover {
			.xf-nlAccountMenuLinkHover();
		}
	}
	.menu-content .menu-row a.username {
		.xf-nlAccountMenuHeaderUsername();
	}
	.menu-content .menu-row .contentRow-minor {
		.xf-nlAccountMenuHeaderStats();
	}
}

/* Popup menus */

/* Overlays */

.overlay {
	.xf-nlOverlayPanel();
}
.overlay .block-container {
	box-shadow: none !important;
}
.overlay-title {
	line-height: 1;
}
.overlay-titleCloser {
	.xf-nlOverlayCloser();
}

/* Attachments */

.attachment-icon.attachment-icon--img {
	img {
		.xf-nlAttachmentThumb();
	}
	img:hover {
		.xf-nlAttachmentThumbHover();
	}
}

/* Temp patches */

/* Node list */

.nodeList .block-body > .node:last-of-type .node-body {
    margin-bottom: 0;
}
.nodeList {
	width: 100%;
	.xf-nlNodeListWrapper();
	
	.block.block--nodes {
		.xf-nlNodeGroupContainer();
	}
	
	.block.block--nodes .block-container {
		.xf-nlNodeCategoryContainer();
	}
	.block.block--nodes .block-body {
		.xf-nlNodeCategoryListContainer();
		
		& > .node:last-of-type .node-body {
			margin-bottom: 0;
		}
	}
	.block.block--nodes h2.block-header {
		margin: 0;
		padding: 0;
		.xf-nlCategoryStrip();

		a {
			.xf-nlCategoryStripTitle();
		}
		a:hover {
			.xf-nlCategoryStripTitleHover();
		}
		.block-desc {
			.xf-nlCategoryStripDescription();
		}
		+ .block-desc {
			.xf-nlCategoryDescSeparate();
		}
	}
}
.node-description {
	margin: (@xf-paddingSmall / 2) 0;
	.xf-nlNodeDescription();
}
/* Hard set line height, gives tighter appearance. Reconsider for later */
.node-description, .node-extra-row, .node-title, .structItem-cell {
    .m-lineHeightDefault();
}
.subNodeLink {
	color: inherit;
}

/* Replicate popup menus */
.menu .subNodeMenu
{
	.subNodeLink
	{
		.xf-menuLinkRow();
		.xf-menuLinkRowExtended();
		
		&:hover {
			.xf-menuLinkRowSelected();
		}
	}
}

.has-menuFollowIcons .menu-linkRow:after {
	.xf-nlMenuLinkRowAfterIcon();
}
.has-blockTitle .p-body .p-title {
	.xf-contentBase();
	.xf-blockBorder();
	border-radius: @xf-blockBorderRadius;
}
.p-title-value {
	.xf-nlPageTitle();
}

/* Structured lists */

.structItemContainer-group--sticky {
	.structItem {
		.xf-nlStructItemSticky();
	}
}
.structItem-cell--main .structItem-title {
	.m-lineHeightDefault();
	/* margin-right: 26px; */
	.m-clearFix();
	
	.label.label--primary:last-child {
		/* float: right; */
	}
}
.structItem-cell.structItem-cell--meta {
	.structItem-cell--inner {
		@media (min-width: @xf-responsiveMedium)
		{
			.xf-nlStructItemCellMetaInner();
		}
		.structItem-minor {
			color: inherit;
			font-size: inherit;
		}
		.pairs.pairs--justified > dt {
			.xf-nlStructItemCellMetaLabel();
		}
		.pairs.pairs--justified > dd {
			.xf-nlStructItemCellMetaAmount();
		}
	}
}
.structItem-cell.structItem-cell--latest {
	.structItem-cell--inner {
		@media (min-width: @xf-responsiveMedium)
		{
			width: 100%;
			.xf-nlStructItemLatestInner();
		}
	}
}
@media (max-width: @xf-responsiveMedium)
{
	.structItem-cell--inner {
		display: inline-block;
	}
}

.structItem-cell.structItem-cell--icon.structItem-cell--iconEnd {
	text-align: right;
}

/* Forum view */

.filterBar-menuTrigger {
	.xf-nlBlockMenuTrigger();
}
.hideIcon .node-icon {
	display: none !important;
}
.hideStatsBlock .node-stats {
	display: none !important;
}
.hideLastPost .node-extra {
	display: none !important;
}
.node-iconStats .pairs.pairs--inline {
	margin-right: 10px;
	
	> dt:after {
		display: none;
	}
}
.node-main .node-stats {
	
	font-size: @xf-fontSizeSmall;
	color: @xf-textColorDimmed;
	
	padding: 6px 0;
	flex: none;
	text-align:  left;
	background: transparent none;
	display: block;
	width: 100%;
	
	dl.pairs > *,
	dl.pairs {
		display: inline-block !important;
		margin-right: 2px;
	}
	&.node-stats--iconify .pairs > dt:after {
		content: "";
	}
	&.node-stats--iconify .pairs > dd {
		display: inline-block;
	}
}
.node-body .node-stats.node-stats--iconify {
	
	> dl.pairs dt {
		width: auto;
	}
	.pairs.pairs--rows > dd {
		display: inline;
	}
}
.nodeStats-belowDesc .node-statsMeta {
    display: inline-block;
}

/* Inline moderation bar */

.inlineModBar {
	button,
	.button.button--primary {
		.xf-nlInlineModBarButton();
	}
	button:hover,
	.button.button--primary:hover {
		.xf-nlInlineModBarButtonHover();
	}
}
/* Better input and button consistency */
.inlineModBar .input,
.inlineModBar .button {
    padding-top: 8px;
    padding-bottom: 8px;
    line-height: 1;
    vertical-align: middle;
    border-radius: @xf-borderRadiusSmall;
}
.inlineModBar .button.button--primary span {
    line-height: 1;
}

/* Message view */

.message-content a {
	.xf-nlMessageLink();
}
.message-content a:hover {
	.xf-nlMessageLinkHover();
}
.messageButtonLinks .actionBar-set--external .actionBar-action {
	.xf-buttonBase();
	';
	if ($__templater->func('property', array('nlMessageButtonLinksStyle', ), false) == 'buttonPrimary') {
		$__finalCompiled .= '
	.xf-buttonPrimary();
	&:hover {
		.xf-nlButtonPrimaryHover();
	}
	';
	} else if ($__templater->func('property', array('nlMessageButtonLinksStyle', ), false) == 'buttonCta') {
		$__finalCompiled .= '
	.xf-buttonCta();
	&:hover {
		.xf-nlButtonCtaHover();
	}
	';
	} else if ($__templater->func('property', array('nlMessageButtonLinksStyle', ), false) == 'buttonLink') {
		$__finalCompiled .= '
	.xf-nlButtonLink();
	&:hover {
		.xf-nlButtonLinkHover();
	}
	';
	}
	$__finalCompiled .= '
	.xf-nlButtonSmall();
	
	&.has-reaction .reaction-text {
		color: inherit !important;
	}
}
.message-userDetails {
	.xf-nlMessageUserDetails();
	
	.userTitle {
		.xf-nlUserTitle();
	}
}

.actionBar-action {
	.xf-nlMessageActionLinks();
}
.actionBar-action.actionBar-action--inlineMod label.iconic {
    color: inherit;
}

/* Message banners and buttons */

.message-cell--user {
	a.cxf-button.button--link,
	.offline, .online,
	.userBanner {
		display: block;
		/* width: 100%; */
		margin-top: 4px;
	}
}

/* Member profiles */

.memberHeader-main .memberHeader-avatar .avatar {
	.xf-memberHeaderAvatar();
}
.memberHeader-blurb dt {
    .xf-memberHeaderLabels();
}
.memberHeader-main + .memberHeader-content {
    .xf-memberSecondaryHeader();
}
.memberTooltip-headerInfo .memberTooltip-blurb {
	font-size: inherit;
}
.memberTooltip-info {
	.xf-memberTooltipSecondaryHeader();
}

.p-body-content .message--simple .message-attribution .listInline {
	
	&.listInline--bullet > li:before {
		content: " ";
	}
	> li:not(.message-attribution-user) {
		position: relative;
		margin-left: 8px;
		.xf-nlMessageAttributionItem();
		
		.ratingStars {
			vertical-align: middle;
			top: auto;
		}
		
		&:before,
		&:after {
			content: "";
			position: absolute;
			top: 23%;
		}
		&:before {
			left: -7px;
			.m-triangleLeft(@xf-nlMessageAttributionItem--border-color, 7px);
		}
		&:after {
			left: -6px;
			.m-triangleLeft(@xf-nlMessageAttributionItem--background-color, 7px);
		}
	}
}

/* Forms */

.formRow label.formRow-label {
	.xf-nlFormLabel();
}
.form-inline label {
    margin: @xf-paddingSmall 0;
    display: inline-block;
}
.form-inline .formSubmitRow-bar {
    background: none;
    border: none;
}
.form-inline .formSubmitRow-controls {
    padding: 0;
    margin: @xf-elementSpacer 0 0;
}
input[type="search"] {
    color: @xf-inputTextColor;
}
select.input.hide {
    display: none !important;
}
.inputGroup.inputGroup--joined .input:focus + .inputGroup-text {
    border-color: @xf-inputFocusBorderColor;
}

/* Footer */

.footerFixed footer.p-footer {
	.m-pageWidth();
	padding: 0;
}
.p-footer-row-main {
	.p-footer-linkList {
		
		/* Set chooser links */
		& > li a {
			.xf-nlFooterLinksBarChooserLink();
		}
		& > li a:hover {
			.xf-nlFooterLinksBarChooserLinkHover();
		}
	}
}

.p-footer {

	/* Bold widget links within columns */
	.contentRow-main a {
		font-weight: @xf-fontWeightHeavy;
		display: block;
		.m-overflowEllipsis();
	}
	.label.label--primary {
		
	}
	.contentRow-muted
	{
		color: @xf-footerTextColorMuted;
	}
	.contentRow-minor
	{
		color: @xf-footerTextColorMuted;
	}
	.pairs > dt {
		color: @xf-footerTextColorMuted;
	}
	textarea, .input {
		.xf-nlFooterInput();
	}
}
.p-footer-row {
	position: relative;
}
.p-footer-row.p-footer-copyright {
	a {
		.xf-nlFooterCopyrightLink();
	}
	a:hover {
		.xf-nlFooterCopyrightLinkHover();
	}
}
/* Deprecate or change
.p-footer-copyright > * {
    display: inline-block;
}
*/

/* Social Links */

/* Tabs */

.tabs-tab {
	position: relative;
}
.tab-markers-default {
	
}
.tab-markers-arrow {
	.tabs-tab {
		border-bottom: none !important;
	}
	@_tabs-arrowSize: 8px;
	
	.tabs-tab:after {
		content: "";
		position: absolute;
		bottom: 0;
		left: 50%;
		margin-left: -(@_tabs-arrowSize / 2);
		.m-triangleUp(transparent, @_tabs-arrowSize);
	}
	.block-tabHeader {
		.tabs-tab:after {
			bottom: -@xf-blockTabHeader--border-bottom-width;
		}
		.tabs-tab.is-active:after {
			border-bottom-color: @xf-blockTabHeaderSelected--border-color;
		}
	}
	.block-minorTabHeader {
		.tabs-tab.is-active:after {
			border-bottom-color: @xf-blockMinorTabHeaderSelected--border-color;
		}
	}
	.tabs--standalone {
		.tabs-tab.is-active:after {
			border-bottom-color: @xf-standaloneTabSelected--border-color;
		}
	}
	.menu-tabHeader {
		.tabs-tab.is-active:after {
			border-bottom-color: @xf-menuTabHeaderSelected--border-color;
		}
	}
}

/* Misc */

.float-left {
	float: left;
}
.float-right {
	float: right;
}

.button.button--scroll,
a.button.button--scroll {
	.xf-nlPageScrollButton();
}
.u-scrollButtons {
	bottom: @xf-nlScrollToTopMarginV;
}
.lSAction > a:after {
	font-weight: 900;
}
.actionBar-action.actionBar-action--inlineMod label.iconic,
label.iconic i {
    font-weight: normal;
}

/* Full width theme */

@media (min-width: @xf-nlFullWidthTrigger)
{
	.fullWidth {
		.p-staffBar,
		.p-topBarController,
		.p-navController,
		.p-sectionLinks,
		.p-page-header,
		.p-header,
		.p-page-header-inner,
		.p-body-inner,
		.p-row-inner,
		.p-footer,
		.p-footer-wrxapper,
		.p-footer-inner {
			.xf-nlPageWidthFluid();
		}
		.p-staffBar-inner,
		.p-topBar-inner,
		.p-nav-inner,
		.p-sectionLinks-inner,
		.p-header-inner,
		.top-row .top-row-item {
			width: auto !important;
			max-width: none !important;
			.m-pageInset();
		}
		&.headerStretch #header .p-topBar-inner,
		&.headerStretch .p-sectionLinks-inner {
			max-width: none;
		}
	}
}

/* Setup responsive widths */

.p-staffBar-inner,
.p-topBar-inner,
.p-header-inner,
.p-nav-inner,
.p-sectionLinks-inner,
.p-page-header-inner,
.p-body-inner,
.p-row-inner,
.footerFixed footer .p-footer-wraxpper,
.p-footer-inner,
.footerStretch .p-footer-inner {
	.m-pageInset();
}

@media (max-width: @xf-publicNavCollapseWidth)
{
	/*
	.has-js .p-nav {
		.xf-nlPublicNavResponsive();
	}
	*/
	.p-nav .p-nav-menuTrigger {
		margin: 0 @xf-paddingMedium 0 0;
	}
	.has-js .p-nav-opposite {
		margin-right: 0;
	}
	.has-js .p-nav-opposite .p-navgroup-link {
		.xf-nlPublicNavResponsiveVtabs();
	}
}
@media (min-width: @xf-publicNavCollapseWidth)
{
	/* Deprecating
	.p-nav-inner,
	.p-sectionLinks-inner {
		padding: @xf-nlPNavInnerAdjuster;
	}
	*/
}

@media (min-width: @xf-pageWidthMax)
{
	.headerStretch.headerStretchInner .p-staffBar-inner,
	.headerStretch.headerStretchInner .p-topBar-inner,
	.headerStretch.headerStretchInner .p-header-inner,
	.headerStretch.headerStretchInner .p-page-header-inner,
	.headerStretch.headerStretchInner:not(.has-paddedNav) .p-nav-inner,
	.headerStretch.headerStretchInner .p-sectionLinks-inner {
		width: auto;
		max-width: none;
	}
	.fixedWidth.headerStretch.headerFixedInner .p-staffBar-inner,
	.fixedWidth.headerStretch.headerFixedInner .p-topBar-inner,
	.fixedWidth.headerStretch.headerFixedInner .p-header-inner,
	.fixedWidth.headerStretch.headerFixedInner .p-page-header-inner,
	.fixedWidth.headerStretch.headerFixedInner:not(.has-paddedNav) .p-nav-inner,
	.fixedWidth.headerStretch.headerFixedInner .p-sectionLinks-inner,
	.fixedWidth.footerStretch .p-footer-inner {
		.m-pageInset(0);
	}
	.boxedContent.fixedWidth {
		//&.headerStretch.headerFixedInner .p-staffBar-inner,
		//&.headerStretch.headerFixedInner .p-topBar-inner,
		//&.headerStretch.headerFixedInner .p-header-inner,
		//&.headerStretch.headerFixedInner .p-page-header-inner,
		//&.headerStretch.headerFixedInner .p-nav-inner,
		&.headerFixed:not(.has-paddedNav):not(.compactHeader) .p-nav-inner,
		&.headerFixed .p-sectionxLinks-inner,
		&.defaultHeader .p-nav-inner {
			.m-pageInset(0);
		}
	}
	.floatingContent.fixedWidth {
		//.p-staffBar-inner,
		//.p-topBar-inner,
		//.p-header-inner,
		//&.compactHeader .p-nav-inner,
		// &.stretchNavigation .p-nav-inner,
		//.boxedContent .p-nav-inner,
		//.p-sectionLinks-inner,
		//.p-page-header-inner,
		//.footerFixed footer .p-footer-wrapper,
		//.p-footer-inner,
		//.footerStretch .p-footer-inner,
		.p-body-inner,
		.p-row-inner {
			.m-pageInset(0);
		}
	}
}
@media (max-width: @xf-pageWidthMax)
{
	.p-staffBar-inner,
	.p-topBar-inner,
	.p-header-inner,
	.p-nav-inner,
	.p-sectionLinks-inner,
	.p-page-header-inner,
	.p-body-inner,
	.boxedContent .p-body-inner,
	.p-row-inner,
	//.footerFixed footer .p-footer-wrapper,
	.p-footer-inner,
	.footerStretch .p-footer-inner {
		.m-pageInset(); /* adding wide padding */
	}
}
@media (max-width: @xf-responsiveMedium)
{
	.p-staffBar-inner,
	.p-topBar-inner,
	.p-header-inner,
	.p-nav-inner,
	.p-sectionLinks-inner,
	.p-page-header-inner,
	.p-body-inner,
	.boxedContent .p-body-inner,
	.p-row-inner,
	//.footerFixed footer .p-footer-wrapper,
	.p-footer-inner,
	.footerStretch .p-footer-inner {
		.m-pageInset(@xf-pageEdgeSpacerMedium); /* adding medium padding */
	}
}
@media (max-width: @xf-responsiveNarrow)
{
	.p-staffBar-inner,
	.p-topBar-inner,
	.p-header-inner,
	.p-nav-inner,
	.p-sectionLinks-inner,
	.p-page-header-inner,
	.p-body-inner,
	.boxedContent .p-body-inner,
	.p-row-inner,
	//.footerFixed footer .p-footer-wrapper,
	.p-footer-inner,
	.footerStretch .p-footer-inner {
		.m-pageInset(@xf-pageEdgeSpacerNarrow);
	}
}

/* Internet Explorer 10+, Microsoft Edge Browser */

_:-ms-lang(x), .p-navSticky.is-sticky { left:0; right:0; }';
	return $__finalCompiled;
});