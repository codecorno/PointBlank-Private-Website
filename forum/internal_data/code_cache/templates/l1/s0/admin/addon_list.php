<?php
// FROM HASH: b7ff84e4a4e018a4d9fa19c6b7448389
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Add-ons');
	$__finalCompiled .= '

';
	$__templater->includeCss('addon_list.less');
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	if ($__vars['disabled']) {
		$__compilerTemp1 .= '
		' . $__templater->button('
			' . 'Enable' . '
		', array(
			'href' => $__templater->func('link', array('add-ons/mass-toggle', null, array('enable' => 1, ), ), false),
			'overlay' => 'true',
			'data-cache' => '0',
		), '', array(
		)) . '
	';
	}
	$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('
		' . 'Install/upgrade from archive' . '
	', array(
		'href' => $__templater->func('link', array('add-ons/install-from-archive', ), false),
		'overlay' => 'true',
		'icon' => 'add',
	), '', array(
	)) . '
	' . $__compilerTemp1 . '
	' . $__templater->button('
		' . 'Disable all' . '
	', array(
		'href' => $__templater->func('link', array('add-ons/mass-toggle', null, array('enable' => 0, ), ), false),
		'overlay' => 'true',
		'data-cache' => '0',
	), '', array(
	)) . '
');
	$__finalCompiled .= '

';
	if ($__vars['hasProcessing']) {
		$__finalCompiled .= '
	<div class="blockMessage blockMessage--error blockMessage--iconic">
		' . 'One or more add-ons currently have actions pending and may be in an inconsistent state. Because of this, some errors may be suppressed and unexpected behavior may occur. If this does not change shortly, please contact the add-on author for guidance.' . '
	</div>
';
	}
	$__finalCompiled .= '

' . $__templater->callMacro('addon_list_macros', 'addon_list_filter', array(), $__vars) . '

';
	if ($__vars['total']) {
		$__finalCompiled .= '
	<div class="addOnList">
		' . '
		' . $__templater->callMacro('addon_list_macros', 'addon_list_block', array(
			'addOns' => $__vars['upgradeable'],
			'heading' => 'Upgradeable add-ons',
		), $__vars) . '
		' . '
		' . $__templater->callMacro('addon_list_macros', 'addon_list_block', array(
			'addOns' => $__vars['installable'],
			'heading' => 'Installable add-ons',
		), $__vars) . '
		' . '
		' . $__templater->callMacro('addon_list_macros', 'addon_list_block', array(
			'addOns' => $__vars['installed'],
			'heading' => 'Installed add-ons',
		), $__vars) . '
		' . '
		' . $__templater->callMacro('addon_list_macros', 'addon_list_block', array(
			'addOns' => $__vars['legacy'],
			'heading' => 'Legacy add-ons',
			'desc' => 'Legacy add-ons are only compatible with older versions of XenForo. They are in a disabled state and will not be re-enabled until they are upgraded. If you choose to uninstall a legacy add-on, any database alterations will remain. You may wish to consult the add-on developer for advice.',
		), $__vars) . '
		' . '
	</div>
';
	} else {
		$__finalCompiled .= '
	<div class="blockMessage">' . 'No add-ons are installed or available to be installed.' . '</div>
';
	}
	return $__finalCompiled;
});