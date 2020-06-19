<?php
// FROM HASH: 6cd84c723673910dc4a389e245a03008
return array('macros' => array('page_class_output' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'pageMode' => $__vars['pageMode'],
		'showTitle' => $__vars['showTitle'],
		'showNotices' => $__vars['notices'],
		'showBreadcrumb' => $__vars['showBreadcrumb'],
		'showSidebar' => $__vars['showSidebar'],
		'showSidenav' => $__vars['showSidenav'],
		'showShare' => $__vars['showShare'],
		'pagePadding' => $__vars['pagePadding'],
		'bgImage' => $__vars['bgImage'],
		'bgColor' => $__vars['bgColor'],
	), $__arguments, $__vars);
	$__finalCompiled .= '
	
	';
	if ($__vars['pageMode']) {
		$__finalCompiled .= 'page-' . $__templater->escape($__vars['pageMode']);
	}
	$__finalCompiled .= '
	';
	if ($__vars['pageMode']) {
		$__finalCompiled .= 'pagetitle-' . $__templater->escape($__vars['showTitle']);
	}
	$__finalCompiled .= '
	';
	if ($__vars['pageMode']) {
		$__finalCompiled .= 'notices-' . $__templater->escape($__vars['showTitle']);
	}
	$__finalCompiled .= '
	';
	if ($__vars['showBreadcrumb']) {
		$__finalCompiled .= 'breadcrumb-' . $__templater->escape($__vars['showBreadcrumb']);
	}
	$__finalCompiled .= '
	';
	if ($__vars['showSidebar']) {
		$__finalCompiled .= 'sidebar-' . $__templater->escape($__vars['showSidebar']);
	}
	$__finalCompiled .= '
	';
	if ($__vars['showSidenav']) {
		$__finalCompiled .= 'sidenav-' . $__templater->escape($__vars['showSidenav']);
	}
	$__finalCompiled .= '
	';
	if ($__vars['showShare']) {
		$__finalCompiled .= 'share-' . $__templater->escape($__vars['showShare']);
	}
	$__finalCompiled .= '
	';
	if ($__vars['pagePadding']) {
		$__finalCompiled .= 'padding-' . $__templater->escape($__vars['pagePadding']);
	}
	$__finalCompiled .= '
	
';
	return $__finalCompiled;
},
'page_inline_styles' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'bgImage' => $__vars['bgImage'],
		'bgColor' => $__vars['bgColor'],
	), $__arguments, $__vars);
	$__finalCompiled .= '
	
	<style>
		html .p-pageWrapper,
		html.has-single-customBg .p-pageWrapper{
			background-color: ' . $__templater->escape($__vars['bgColor']) . ';
			background-image: url(' . $__templater->escape($__vars['bgImage']) . ');
			background-size: cover;
			background-attachment: fixed;
		}
	</style>

';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '

';
	return $__finalCompiled;
});