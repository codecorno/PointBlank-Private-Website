<?php
// FROM HASH: d82ede860cd82d7a033b1b7dbf5103a4
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('dbtech_ecommerce_store');
	$__templater->pageParams['pageNumber'] = $__vars['page'];
	$__finalCompiled .= '

' . $__templater->callMacro('metadata_macros', 'canonical_url', array(
		'canonicalUrl' => $__templater->func('link', array('canonical:dbtech-ecommerce', null, array('page' => $__vars['page'], ), ), false),
	), $__vars) . '

';
	$__templater->setPageParam('searchConstraints', array('dbtech_ecommerce_products' => array('search_type' => 'dbtech_ecommerce_product', ), ));
	$__finalCompiled .= '

';
	if ($__templater->method($__vars['xf']['visitor'], 'canAddDbtechEcommerceProduct', array())) {
		$__compilerTemp1 = '';
		$__compilerTemp2 = $__templater->method($__templater->method($__vars['xf']['app']['em'], 'getRepository', array('DBTech\\eCommerce:Product', )), 'getProductTypeHandlers', array());
		if ($__templater->isTraversable($__compilerTemp2)) {
			foreach ($__compilerTemp2 AS $__vars['productType'] => $__vars['handler']) {
				$__compilerTemp1 .= '
				<a href="' . $__templater->func('link', array('dbtech-ecommerce/add', null, array('product_type' => $__vars['productType'], ), ), true) . '" data-xf-click="overlay" class="menu-linkRow">' . $__templater->escape($__templater->method($__vars['handler'], 'getProductTypePhrase', array())) . '</a>
			';
			}
		}
		$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('Gerenciar' . $__vars['xf']['language']['ellipsis'], array(
			'class' => 'menuTrigger',
			'data-xf-click' => 'menu',
			'aria-expanded' => 'false',
			'aria-haspopup' => 'true',
		), '', array(
		)) . '
	<div class="menu" data-menu="menu" aria-hidden="true">
		<div class="menu-content">
			<h3 class="menu-header">' . 'dbtech_ecommerce_add_product' . '</h3>

			' . $__compilerTemp1 . '
		</div>
	</div>
');
	}
	$__finalCompiled .= '

';
	if ($__vars['canInlineMod']) {
		$__finalCompiled .= '
	';
		$__templater->includeJs(array(
			'src' => 'xf/inline_mod.js',
			'min' => '1',
		));
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	if ($__templater->func('property', array('dbtechEcommerceEnableInfiniteScroll', ), false) AND (($__vars['page'] == 1) AND ($__vars['total'] > $__vars['perPage']))) {
		$__finalCompiled .= '
	';
		$__vars['infiniteScroll'] = true;
		$__finalCompiled .= '

	';
		$__templater->includeJs(array(
			'src' => 'DBTech/eCommerce/metafizzy/infinite-scroll/infinite-scroll.pkgd.js',
			'min' => '1',
			'addon' => 'DBTech/eCommerce',
		));
		$__finalCompiled .= '
	';
		$__templater->includeJs(array(
			'src' => 'DBTech/eCommerce/product_list.js',
			'min' => '1',
			'addon' => 'DBTech/eCommerce',
		));
		$__finalCompiled .= '

	';
		$__templater->includeCss('dbtech_ecommerce_infinite_scroll.less');
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

<div class="block" data-xf-init="' . ($__vars['canInlineMod'] ? 'inline-mod' : '') . '" data-type="dbtech_ecommerce_product" data-href="' . $__templater->func('link', array('inline-mod', ), true) . '">
	<div class="block-outer">';
	$__compilerTemp3 = '';
	$__compilerTemp4 = '';
	$__compilerTemp4 .= '
					';
	if ($__vars['canInlineMod']) {
		$__compilerTemp4 .= '
						' . $__templater->callMacro('inline_mod_macros', 'button', array(), $__vars) . '
					';
	}
	$__compilerTemp4 .= '
				';
	if (strlen(trim($__compilerTemp4)) > 0) {
		$__compilerTemp3 .= '
			<div class="block-outer-opposite">
				<div class="buttonGroup">
				' . $__compilerTemp4 . '
				</div>
			</div>
		';
	}
	$__finalCompiled .= trim('

		' . $__compilerTemp3 . '

	') . '</div>

	<div class="block-container ' . (($__templater->func('property', array('dbtechEcommerceProductListStyle', ), false) == 'grid') ? 'block-container--grid' : 'block-container--list') . '">
		' . $__templater->callMacro('dbtech_ecommerce_overview_macros', 'list_filter_bar', array(
		'filters' => $__vars['filters'],
		'baseLinkPath' => 'dbtech-ecommerce',
		'ownerFilter' => $__vars['ownerFilter'],
		'platformFilter' => $__vars['platformFilter'],
		'productFieldFilter' => $__vars['productFieldFilter'],
	), $__vars) . '

		<div class="block-body">
			';
	if (!$__templater->test($__vars['products'], 'empty', array())) {
		$__finalCompiled .= '
				';
		if ($__templater->func('property', array('dbtechEcommerceProductListStyle', ), false) == 'grid') {
			$__finalCompiled .= '
					';
			$__templater->includeCss('dbtech_ecommerce_product_grid.less');
			$__finalCompiled .= '

					<div class="productList-grid grid-' . $__templater->func('property', array('nlDBTech_ecommGridItemsPerRow', ), true) . '"
						 data-xf-init="' . ($__vars['infiniteScroll'] ? 'dbtech-ecommerce-infinite-scroll' : '') . '"
						 data-infinite-scroll-click="' . $__templater->func('property', array('dbtechEcommerceInfiniteScrollClick', ), true) . '"
						 data-infinite-scroll-after="' . $__templater->func('property', array('dbtechEcommerceInfiniteScrollAfter', ), true) . '"
						 data-infinite-scroll-history="' . $__templater->func('property', array('dbtechEcommerceInfiniteScrollHistory', ), true) . '"
					>
						';
			if ($__templater->isTraversable($__vars['products'])) {
				foreach ($__vars['products'] AS $__vars['product']) {
					$__finalCompiled .= '
							' . $__templater->callMacro('dbtech_ecommerce_product_list_macros', 'product_grid', array(
						'filters' => $__vars['filters'],
						'baseLinkPath' => 'dbtech-ecommerce',
						'product' => $__vars['product'],
						'showOwner' => ($__templater->func('property', array('dbtechEcommerceShowOwnerOverview', ), false) ? true : false),
						'allowInlineMod' => $__vars['canInlineMod'],
					), $__vars) . '
						';
				}
			}
			$__finalCompiled .= '
					</div>
				';
		} else {
			$__finalCompiled .= '
					<div class="structItemContainer"
						 data-xf-init="' . ($__vars['infiniteScroll'] ? 'dbtech-ecommerce-infinite-scroll' : '') . '"
						 data-infinite-scroll-click="' . $__templater->func('property', array('dbtechEcommerceInfiniteScrollClick', ), true) . '"
						 data-infinite-scroll-after="' . $__templater->func('property', array('dbtechEcommerceInfiniteScrollAfter', ), true) . '"
						 data-infinite-scroll-history="' . $__templater->func('property', array('dbtechEcommerceInfiniteScrollHistory', ), true) . '"
					>
						';
			if ($__templater->isTraversable($__vars['products'])) {
				foreach ($__vars['products'] AS $__vars['product']) {
					$__finalCompiled .= '
							' . $__templater->callMacro('dbtech_ecommerce_product_list_macros', 'product', array(
						'filters' => $__vars['filters'],
						'baseLinkPath' => 'dbtech-ecommerce',
						'product' => $__vars['product'],
						'showOwner' => ($__templater->func('property', array('dbtechEcommerceShowOwnerOverview', ), false) ? true : false),
						'allowInlineMod' => $__vars['canInlineMod'],
					), $__vars) . '
						';
				}
			}
			$__finalCompiled .= '
					</div>
				';
		}
		$__finalCompiled .= '
			';
	} else if ($__vars['filters']) {
		$__finalCompiled .= '
				<div class="block-row">' . 'dbtech_ecommerce_there_no_products_matching_your_filters' . '</div>
			';
	} else {
		$__finalCompiled .= '
				<div class="block-row">' . 'dbtech_ecommerce_no_products_have_been_created_yet' . '</div>
			';
	}
	$__finalCompiled .= '
		</div>
	</div>

	';
	if ($__vars['infiniteScroll']) {
		$__finalCompiled .= '
		<div class="block-row product-status">
			<div class="product-ellipsis infinite-scroll-request">
				<span class="product-ellipsis--dot"></span>
				<span class="product-ellipsis--dot"></span>
				<span class="product-ellipsis--dot"></span>
				<span class="product-ellipsis--dot"></span>
			</div>
		</div>

		<div class="block-row product-loader">
			' . $__templater->button('dbtech_ecommerce_load_more...', array(
			'class' => 'product-button button--cta',
		), '', array(
		)) . '
		</div>
	';
	}
	$__finalCompiled .= '

	<div class="block-outer block-outer--after block-outer--pagination">
		' . $__templater->func('page_nav', array(array(
		'page' => $__vars['page'],
		'total' => $__vars['total'],
		'link' => 'dbtech-ecommerce',
		'params' => $__vars['filters'],
		'wrapperclass' => 'block-outer-main',
		'perPage' => $__vars['perPage'],
	))) . '
		' . $__templater->func('show_ignored', array(array(
		'wrapperclass' => 'block-outer-opposite',
	))) . '
	</div>
</div>

';
	$__templater->setPageParam('sideNavTitle', 'Categorias');
	$__finalCompiled .= '
';
	$__templater->modifySideNavHtml(null, '
	' . $__templater->callMacro('dbtech_ecommerce_category_list_macros', 'simple_list_block', array(
		'categoryTree' => $__vars['categoryTree'],
		'categoryExtras' => $__vars['categoryExtras'],
	), $__vars) . '
', 'replace');
	$__finalCompiled .= '

';
	$__templater->modifySideNavHtml('_xfWidgetPositionSideNavDbtechEcommerceOverviewSidenav', $__templater->widgetPosition('dbtech_ecommerce_overview_sidenav', array()), 'replace');
	return $__finalCompiled;
});