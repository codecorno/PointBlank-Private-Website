<?php
// FROM HASH: f09843c1239f310d448152385cb9e733
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->func('property', array('nlThemeClass', ), false) != null) {
		$__finalCompiled .= ' ' . $__templater->func('property', array('nlThemeClass', ), true);
	}
	$__finalCompiled .= '
';
	if ($__templater->func('property', array('nlEnableFullWidth', ), false) == true) {
		$__finalCompiled .= ' fullWidth';
	} else {
		$__finalCompiled .= ' fixedWidth';
	}
	$__finalCompiled .= '
';
	if ($__templater->func('property', array('nlUseContentBoxShadows', ), false) == true) {
		$__finalCompiled .= ' contentShadows';
	}
	$__finalCompiled .= '
';
	if ($__templater->func('property', array('nlUseHoverTransitions', ), false) == true) {
		$__finalCompiled .= ' hoverTransitions';
	}
	$__finalCompiled .= '
';
	if ($__templater->func('property', array('nlUseBlockTitle', ), false) == true) {
		$__finalCompiled .= ' has-blockTitle';
	}
	$__finalCompiled .= '
 blockStyle--' . $__templater->func('property', array('nlBlockPaddingStyle', ), true) . '
';
	if ($__templater->func('property', array('nlContentLayout', ), false) == 'floating') {
		$__finalCompiled .= ' 
 floatingContent
	';
		if ($__templater->func('property', array('nlHeaderLayout', ), false) == true) {
			$__finalCompiled .= ' headerStretch';
		} else {
			$__finalCompiled .= ' headerFixed';
		}
		$__finalCompiled .= '
	';
		if ($__templater->func('property', array('nlStretchHeaderInnerContents', ), false) == true) {
			$__finalCompiled .= ' headerStretchInner';
		} else {
			$__finalCompiled .= ' headerFixedInner';
		}
		$__finalCompiled .= '
	';
		if ($__templater->func('property', array('nlStretchNavigation', ), false) == true) {
			$__finalCompiled .= ' stretchNavigation';
		} else {
			$__finalCompiled .= ' fixedNavigation';
		}
		$__finalCompiled .= '
	';
		if ($__templater->func('property', array('nlFooterLayout', ), false) == 'stretch') {
			$__finalCompiled .= ' footerStretch';
		} else if ($__templater->func('property', array('nlFooterLayout', ), false) == 'fixed') {
			$__finalCompiled .= ' footerFixed';
		}
		$__finalCompiled .= '

';
	} else if ($__templater->func('property', array('nlContentLayout', ), false) == 'boxed') {
		$__finalCompiled .= '
 boxedContent
	';
		if (($__templater->func('property', array('nlHeaderLayout', ), false) == true) AND ($__templater->func('property', array('nlForceHeaderFooterBoxedWidth', ), false) == false)) {
			$__finalCompiled .= ' headerStretch';
		} else {
			$__finalCompiled .= ' headerFixed';
		}
		$__finalCompiled .= '
	';
		if (($__templater->func('property', array('nlStretchHeaderInnerContents', ), false) == true) AND ($__templater->func('property', array('nlForceHeaderFooterBoxedWidth', ), false) == false)) {
			$__finalCompiled .= ' headerStretchInner';
		} else {
			$__finalCompiled .= ' headerFixedInner';
		}
		$__finalCompiled .= '
	';
		if (($__templater->func('property', array('nlStretchNavigation', ), false) == true) AND ($__templater->func('property', array('nlForceHeaderFooterBoxedWidth', ), false) == false)) {
			$__finalCompiled .= ' stretchNavigation';
		}
		$__finalCompiled .= '
	';
		if (($__templater->func('property', array('nlFooterLayout', ), false) == 'stretch') AND ($__templater->func('property', array('nlForceHeaderFooterBoxedWidth', ), false) == false)) {
			$__finalCompiled .= ' footerStretch';
		} else if (($__templater->func('property', array('nlFooterLayout', ), false) == 'fixed') OR ($__templater->func('property', array('nlForceHeaderFooterBoxedWidth', ), false) == true)) {
			$__finalCompiled .= ' footerFixed';
		}
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	if ($__templater->func('property', array('nlPNavPadded', ), false) == true) {
		$__finalCompiled .= ' has-paddedNav';
	}
	$__finalCompiled .= '
';
	if ($__templater->func('property', array('nlSidebarUseAltRows', ), false) == true) {
		$__finalCompiled .= ' sidebarAltRows';
	}
	$__finalCompiled .= '
';
	if ($__templater->func('property', array('nlEnableThemeEffects', ), false) == true) {
		$__finalCompiled .= ' enableThemeEffects';
	}
	$__finalCompiled .= '
';
	if ($__templater->func('property', array('nlShowMenuHeaders', ), false) == true) {
		$__finalCompiled .= ' has-menuHeaders';
	}
	$__finalCompiled .= '
';
	if ($__templater->func('property', array('nlMenuLinkFollowIcons', ), false) == true) {
		$__finalCompiled .= ' has-menuFollowIcons';
	}
	$__finalCompiled .= '

';
	if ($__vars['page']['advanced_mode']) {
		$__finalCompiled .= ' pageAdvanced';
	}
	$__finalCompiled .= '
';
	if ($__templater->func('property', array('nlDataListUseAlternatingRows', ), false) == true) {
		$__finalCompiled .= ' dataListAltRows';
	}
	$__finalCompiled .= '
 tab-markers-' . $__templater->func('property', array('nlTabMarkerStyle', ), true) . '
' . $__templater->callMacro('bodytag_macros', 'page_class_output', array(
		'pageMode' => $__vars['pageMode'],
		'showTitle' => $__vars['showTitle'],
		'showBreadcrumb' => $__vars['showBreadcrumb'],
		'showSidebar' => $__vars['showSidebar'],
		'showSidenav' => $__vars['showSidenav'],
		'showShare' => $__vars['showShare'],
		'pagePadding' => $__vars['pagePadding'],
	), $__vars);
	return $__finalCompiled;
});