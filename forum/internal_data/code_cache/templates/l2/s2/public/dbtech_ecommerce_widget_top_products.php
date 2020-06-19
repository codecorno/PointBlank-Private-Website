<?php
// FROM HASH: cedf4a16ba65346ba0d5e4ea2007a7dc
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if (!$__templater->test($__vars['products'], 'empty', array())) {
		$__finalCompiled .= '
	<div class="block"' . $__templater->func('widget_data', array($__vars['widget'], ), true) . '>
		<div class="block-container block-container--' . $__templater->escape($__vars['style']) . '">
			';
		if ($__vars['style'] == 'full') {
			$__finalCompiled .= '
				<h3 class="block-header">' . ($__templater->escape($__vars['title']) ?: 'dbtech_ecommerce_top_products') . '</h3>
				<div class="block-body">
					';
			if ($__templater->func('property', array('dbtechEcommerceProductListStyle', ), false) == 'grid') {
				$__finalCompiled .= '
						';
				$__templater->includeCss('dbtech_ecommerce_product_grid.less');
				$__finalCompiled .= '

						<div class="productList-grid grid-' . $__templater->escape($__vars['options']['limit']) . '">
							';
				if ($__templater->isTraversable($__vars['products'])) {
					foreach ($__vars['products'] AS $__vars['product']) {
						$__finalCompiled .= '
								' . $__templater->callMacro('dbtech_ecommerce_product_list_macros', 'product_grid', array(
							'filters' => array(),
							'baseLinkPath' => 'dbtech-ecommerce',
							'product' => $__vars['product'],
							'showOwner' => ($__templater->func('property', array('dbtechEcommerceShowOwnerOverview', ), false) ? true : false),
							'allowInlineMod' => false,
						), $__vars) . '
							';
					}
				}
				$__finalCompiled .= '
						</div>
					';
			} else {
				$__finalCompiled .= '
						<div class="structItemContainer">
							';
				if ($__templater->isTraversable($__vars['products'])) {
					foreach ($__vars['products'] AS $__vars['product']) {
						$__finalCompiled .= '
								' . $__templater->callMacro('dbtech_ecommerce_product_list_macros', 'product', array(
							'allowInlineMod' => false,
							'product' => $__vars['product'],
						), $__vars) . '
							';
					}
				}
				$__finalCompiled .= '
						</div>
					';
			}
			$__finalCompiled .= '
				</div>
			';
		} else if ($__vars['style'] == 'full-grid') {
			$__finalCompiled .= '
				';
			$__templater->includeCss('dbtech_ecommerce_product_grid.less');
			$__finalCompiled .= '

				<h3 class="block-header">' . ($__templater->escape($__vars['title']) ?: 'dbtech_ecommerce_top_products') . '</h3>
				<div class="block-body">
					<div class="productList-grid grid-' . $__templater->escape($__vars['options']['limit']) . '">
						';
			if ($__templater->isTraversable($__vars['products'])) {
				foreach ($__vars['products'] AS $__vars['product']) {
					$__finalCompiled .= '
							' . $__templater->callMacro('dbtech_ecommerce_product_list_macros', 'product_grid', array(
						'filters' => array(),
						'baseLinkPath' => 'dbtech-ecommerce',
						'product' => $__vars['product'],
						'showOwner' => ($__templater->func('property', array('dbtechEcommerceShowOwnerOverview', ), false) ? true : false),
						'allowInlineMod' => false,
					), $__vars) . '
						';
				}
			}
			$__finalCompiled .= '
					</div>
				</div>
			';
		} else if ($__vars['style'] == 'full-list') {
			$__finalCompiled .= '
				<h3 class="block-header">' . ($__templater->escape($__vars['title']) ?: 'dbtech_ecommerce_top_products') . '</h3>
				<div class="block-body">
					<div class="structItemContainer">
						';
			if ($__templater->isTraversable($__vars['products'])) {
				foreach ($__vars['products'] AS $__vars['product']) {
					$__finalCompiled .= '
							' . $__templater->callMacro('dbtech_ecommerce_product_list_macros', 'product', array(
						'allowInlineMod' => false,
						'product' => $__vars['product'],
					), $__vars) . '
						';
				}
			}
			$__finalCompiled .= '
					</div>
				</div>
			';
		} else {
			$__finalCompiled .= '
				<h3 class="block-minorHeader">' . ($__templater->escape($__vars['title']) ?: 'dbtech_ecommerce_top_products') . '</h3>
				<ul class="block-body">
					';
			if ($__templater->isTraversable($__vars['products'])) {
				foreach ($__vars['products'] AS $__vars['product']) {
					$__finalCompiled .= '
						<li class="block-row">
							' . $__templater->callMacro('dbtech_ecommerce_product_list_macros', 'product_simple', array(
						'product' => $__vars['product'],
						'showOwner' => ($__templater->func('property', array('dbtechEcommerceShowOwnerOverview', ), false) ? true : false),
						'withRating' => true,
					), $__vars) . '
						</li>
					';
				}
			}
			$__finalCompiled .= '
				</ul>
			';
		}
		$__finalCompiled .= '
		</div>
	</div>
';
	}
	return $__finalCompiled;
});