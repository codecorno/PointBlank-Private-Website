<?php
// FROM HASH: 8149b98bd4d05c4155963c09c3ffb40b
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '/* XFMG Fix */

.has-touchevents .itemList-itemOverlay,
.itemList-item:hover .itemList-itemOverlay {
    height: auto;
}

/* XFRM */

.resourceSidebarGroup-title {
	.xf-nlRMSidebarGroupTitle();
}
/* Siropu Shout */

.nodeList .siropuShoutbox .block-body {
	padding: 10px !important;
	background: inherit;
}
/* Siropu chat */

#siropuChatOptions {
	margin-top: 0;
}

/* [XD] Featured Threads Sliders */

.FTSlider_BlockBody {
	padding: @xf-blockPaddingV @xf-blockPaddingH;
}

/* DBTech Ecommerce */

/* From dbtech_ecommerce_product_grid.less */

.productList-product-grid {
	.xf-blockBorder();
	background: @xf-contentBg;
}
';
	if ($__templater->func('property', array('nlDBTechEcommWidgetInnerPadding', ), false) != null) {
		$__finalCompiled .= '
.productList-product-grid.node .node-main {
	padding: @xf-nlDBTechEcommWidgetInnerPadding;
}
.productList-product-grid.node .productList-product-grid--clearfix {
	padding: 0 @xf-nlDBTechEcommWidgetInnerPadding @xf-nlDBTechEcommWidgetInnerPadding;
}
';
	} else {
		$__finalCompiled .= '
.productList-product-grid.node .productList-product-grid--clearfix {
	padding: 0 @xf-nlNodePaddingH @xf-nlNodePaddingV;
}
';
	}
	$__finalCompiled .= '
.productList-product-grid.node .productList-product-grid--clearfix:after {
	margin: 0;
}

/* end overrides */

.productList-product-grid.node .node-title {
	margin-bottom: @xf-paddingSmall;
}

@_dbtech_ecomm_itemWidth: (100 / @xf-nlDBTech_ecommGridItemsPerRow);

.p-body .block-container--grid,
.p-body .block-container--full-grid {
	.m-stripElement();
	
	.productList-grid {
		display: flex;
		flex-flow: row wrap;
		justify-content: flex-start;
		margin: 0 -(@xf-elementSpacer / 2);
	}
	.productList-product-grid.node {
		padding: 0;
		margin: (@xf-elementSpacer / 2);
		flex: 0 0 ~"calc(33.33% - @xf-elementSpacer)";
		width: auto;
		max-width: none;
		min-width: 0;
		
		.productList-product-grid--updateInfo {
			.xf-blockFooter();
		}
	}
	/* Standard price in widgets */
	.price {
		.xf-nlDBTechEcommWidgetPrice();
	}
	/* Set strikethrough price styles */
	.productList-product-grid--clearfix .price,
	.productList-product-grid--priceCartInfo .price {
		span {
			line-height: normal;
		}
		.old-price {
			text-decoration: line-through;
			color: @xf-textColorMuted;
		}
		.sale-price {
			
		}
	}
	.productList-product-grid--clearfix .price {
		span {
			display: block;
		}
	}
	
	.grid-2 {
		.productList-product-grid.node {
			flex: 0 0 ~"calc(50% - @xf-elementSpacer)";
		}
	}
	.grid-3 {
		.productList-product-grid.node {
			flex: 0 0 ~"calc(33.33% - @xf-elementSpacer)";
		}
	}
	.grid-4 {
		.productList-product-grid.node {
			flex: 0 0 ~"calc(25% - @xf-elementSpacer)";
		}
	}
	.grid-5 {
		.productList-product-grid.node {
			flex: 0 0 ~"calc(20% - @xf-elementSpacer)";
		}
	}
	.block-footer {
		.m-stripElement();
	}
	@media (max-width: 800px)
	{
		.productList-product-grid.node {
			flex: 0 0 ~"calc(33.33% - @xf-elementSpacer)" !important;
		}
	}
	@media (max-width: @xf-responsiveMedium)
	{
		.productList-product-grid.node {
			flex: 0 0 ~"calc(50% - @xf-elementSpacer)" !important;
		}
	}
	@media (max-width: @xf-responsiveNarrow)
	{
		.productList-product-grid.node {
			flex: 1 1 100% !important;
		}
	}
}

.productList-product-grid--priceCartInfo {
	display: flex;
	
	.addToCart, .costs {
		padding: @xf-paddingSmall;
		flex: 1;
		line-height: normal;
	}
	.addToCart {
		flex: 0 0 auto;
	}
	.costs {
		text-align: right;
	}
}



/* Product icons */

.productList-product-grid.node .productList-product-grid--icon {
	.xf-nlDBTechEcommThumbWrapper();
}

.productList-product-grid--icon a.avatar {
    width: auto;
    height: auto;
}

/* Custom responsive product icons - deprecate if added by author */

';
	if ($__vars['xf']['options']['dbtechEcommerceProductIconMaxDimensions']['width'] OR $__vars['xf']['options']['dbtechEcommerceProductIconMaxDimensions']['height']) {
		$__finalCompiled .= '
.flex-box .avatar
{
    &.avatar--productIconDefault,
    &.avatar--productIcon
    {
		width: auto;
		height: auto;
		
		&.avatar--xxs
		{
			';
		if ($__vars['xf']['options']['dbtechEcommerceProductIconMaxDimensions']['width']) {
			$__finalCompiled .= '
				max-width: ' . ($__vars['xf']['options']['dbtechEcommerceProductIconMaxDimensions']['width'] * 0.125) . 'px;
			';
		}
		$__finalCompiled .= '
		}

		&.avatar--xs
		{
			';
		if ($__vars['xf']['options']['dbtechEcommerceProductIconMaxDimensions']['width']) {
			$__finalCompiled .= '
				max-width: ' . ($__vars['xf']['options']['dbtechEcommerceProductIconMaxDimensions']['width'] * 0.17) . 'px;
			';
		}
		$__finalCompiled .= '
		}
		
		&.avatar--s
		{
			';
		if ($__vars['xf']['options']['dbtechEcommerceProductIconMaxDimensions']['width']) {
			$__finalCompiled .= '
				max-width: ' . ($__vars['xf']['options']['dbtechEcommerceProductIconMaxDimensions']['width'] * 0.25) . 'px;
			';
		}
		$__finalCompiled .= '
		}
		
		&.avatar--m
		{
			';
		if ($__vars['xf']['options']['dbtechEcommerceProductIconMaxDimensions']['width']) {
			$__finalCompiled .= '
				max-width: ' . ($__vars['xf']['options']['dbtechEcommerceProductIconMaxDimensions']['width'] * 0.5) . 'px;
			';
		}
		$__finalCompiled .= '
		}

		&.avatar--l
		{
			';
		if ($__vars['xf']['options']['dbtechEcommerceProductIconMaxDimensions']['width']) {
			$__finalCompiled .= '
				max-width: ' . $__templater->escape($__vars['xf']['options']['dbtechEcommerceProductIconMaxDimensions']['width']) . 'px;
			';
		}
		$__finalCompiled .= '
		}
    }
}
';
	}
	$__finalCompiled .= '

/* Product pages */

.product-feature-img {
	position: relative;
}
.productBody .productBody--attachments {
	margin: 0;
}
@media (min-width: @xf-responsiveWide)
{
	.template-dbtech_ecommerce_product_view .p-body-sidebar {
		width: @xf-nlDBTechEcommSidebarWidth;
	}
}
.productContainer {
	.product-feature-img {

	}
	.product-feature-img img {
		width: 100%;
		display: block;
	}
	.productBody .productBody--main {
		padding: 0 !important;
	}
	.attachment.slick-slide img {
		max-height: 100px;
	}
}
@media (min-width: @xf-responsiveNarrow)
{
	.productContainer--attachments-side {
		.productAttachments-aside {
			max-width: 180px;
			float: left;
		}
		.slick-container {
			margin: 0;
		}
		.product-feature-img {
			margin-left: 200px;
		}
	}
}
.productContainer--attachments-below {
	
	> .block-body > .block-row:nth-of-type(2) {
		padding-top: 0;
	}
	.slick-container {
		margin: 0;
		
		.slick-prev {
			left: 0;
		}
		.slick-next {
			right: 0;
		}
		.productBody--attachments {
			margin: 0;
		}
		.slick-list {
			padding: 0;
		}
		.slick-slide.attachment {
			background: rgba(0,0,0,0.05);
		}
	}
}
.block-row--pricingInfo .pairs--price > dd {
    .xf-nlDBTechEcommPrice();
}

/* ITD Page scroll progress bar */

.scrollindicator {
	background: @xf-nlITDScrollbarTrack;
}

.scrollprogress {
	background: @xf-nlITDScrollbarActive;
}

/* TH Holidays */

.node--category .node-icon i, .node--forum .node-icon i {
    background-position: center center;
}

/* XenPorta */

@media (max-width: @xf-responsiveMedium)
{
	.block.porta-features {
		margin-left: auto;
		margin-right: auto;
	}
	.porta-masonry {
		margin: 0 auto;
	}
}
@media (max-width: @xf-responsiveNarrow)
{
	.block.porta-features {
		margin-left: auto;
		margin-right: auto;
	}
	.porta-masonry {
		margin: 0 auto;
	}
}

/* Collapsable categories */

.collapsible-nodes .block-header--left {
    line-height: normal;
	
	a {
    	display: block;
	}
}
.block--category .collapseTrigger {
    line-height: normal;
}

/* TH Node grid */

.nodeSeparateTitleDesc .thNodes__nodeList .block-container {
	.block-header {
		margin-bottom: 0;
	}
	.block-body {
		margin-top: @xf-th_nodeGutter;
	}
}
.thNodes__nodeList {
    background: none;
    border: none;
    box-shadow: none;
	
	.siropuChatAboveForumList .block-container {
		.xf-contentBase();
		.xf-blockBorder();
		border-radius: @xf-blockBorderRadius;
	} 
	
	> .block {
        padding: @xf-th_nodeGutter !important;
    }
	.node .node-wrapper {
		display: flex;
		flex-grow: 1;
	}
	.node-icon i {
		.xf-nlImgNodeIconWrapper();
	}
}

.thNodes__nodeHeader .node-icon i:nth-of-type(2) {
    display: none;
}
.th_nodes .node-extra {
    width: 100%;
}

.thNodes__nodeList > .block.block--category:last-of-type {
    margin-bottom: 0;
}';
	return $__finalCompiled;
});