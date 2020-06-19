<?php
// FROM HASH: ca30e0ed0d6f45c943a46e6e044d8aa8
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__compilerTemp1 = '';
	$__compilerTemp1 .= '
		';
	$__compilerTemp2 = '';
	$__compilerTemp2 .= '
				';
	if ($__vars['hasOptions']) {
		$__compilerTemp2 .= '
					<a href="' . (($__vars['hasOptions'] === true) ? $__templater->func('link', array('add-ons/options', $__vars['addOn'], ), true) : $__templater->func('link', array($__vars['hasOptions'], ), true)) . '" class="menu-linkRow">' . 'Options' . '</a>
				';
	}
	$__compilerTemp2 .= '
				';
	if ($__vars['hasPublicTemplates']) {
		$__compilerTemp2 .= '
					<a href="' . $__templater->func('link', array('styles/templates', $__vars['style'], array('addon_id' => $__vars['addOn']['addon_id'], 'type' => 'public', ), ), true) . '" class="menu-linkRow">' . 'Public templates' . '</a>
				';
	}
	$__compilerTemp2 .= '
				';
	if ($__vars['hasEmailTemplates']) {
		$__compilerTemp2 .= '
					<a href="' . $__templater->func('link', array('styles/templates', $__vars['style'], array('addon_id' => $__vars['addOn']['addon_id'], 'type' => 'email', ), ), true) . '" class="menu-linkRow">' . 'Email templates' . '</a>
				';
	}
	$__compilerTemp2 .= '
				';
	if ($__vars['hasAdminTemplates']) {
		$__compilerTemp2 .= '
					<a href="' . $__templater->func('link', array('styles/templates', $__vars['masterStyle'], array('addon_id' => $__vars['addOn']['addon_id'], 'type' => 'admin', ), ), true) . '" class="menu-linkRow">' . 'Admin templates' . '</a>
				';
	}
	$__compilerTemp2 .= '
				';
	if ($__vars['hasPhrases']) {
		$__compilerTemp2 .= '
					<a href="' . $__templater->func('link', array('languages/phrases', $__vars['language'], array('addon_id' => $__vars['addOn']['addon_id'], ), ), true) . '" class="menu-linkRow">' . 'Phrases' . '</a>
				';
	}
	$__compilerTemp2 .= '
			';
	if (strlen(trim($__compilerTemp2)) > 0) {
		$__compilerTemp1 .= '
			' . $__compilerTemp2 . '

			<hr class="menu-separator" />
		';
	}
	$__compilerTemp1 .= '

		';
	$__compilerTemp3 = '';
	$__compilerTemp3 .= '
				';
	if (($__templater->method($__vars['addOn'], 'isInstalled', array()) OR $__templater->method($__vars['addOn'], 'canUpgrade', array())) AND (!$__templater->method($__vars['addOn'], 'isLegacy', array()))) {
		$__compilerTemp3 .= '
					<a href="' . $__templater->func('link', array('add-ons/toggle', $__vars['addOn'], array('t' => $__templater->func('csrf_token', array(), false), ), ), true) . '" class="menu-linkRow">' . ($__vars['addOn']['active'] ? 'Disable' : 'Enable') . '</a>
				';
	}
	$__compilerTemp3 .= '
				';
	if ($__templater->method($__vars['addOn'], 'hasPendingChanges', array()) AND $__vars['xf']['development']) {
		$__compilerTemp3 .= '
					<a href="' . $__templater->func('link', array('add-ons/sync-changes', $__vars['addOn'], array('t' => $__templater->func('csrf_token', array(), false), ), ), true) . '" class="menu-linkRow">' . 'Sync changes' . '</a>
				';
	}
	$__compilerTemp3 .= '
				';
	if ($__templater->method($__vars['addOn'], 'isFileVersionValid', array()) AND $__templater->method($__vars['addOn'], 'canRebuild', array())) {
		$__compilerTemp3 .= '
					<a href="' . $__templater->func('link', array('add-ons/rebuild', $__vars['addOn'], ), true) . '" class="menu-linkRow" data-xf-click="overlay">' . 'Rebuild' . '</a>
				';
	}
	$__compilerTemp3 .= '
				';
	if ($__templater->method($__vars['addOn'], 'isInstalled', array()) AND (!$__templater->method($__vars['addOn'], 'canUpgrade', array()))) {
		$__compilerTemp3 .= '
					<a href="' . $__templater->func('link', array('add-ons/uninstall', $__vars['addOn'], ), true) . '" class="menu-linkRow" data-xf-click="overlay">' . 'Uninstall' . '</a>
				';
	}
	$__compilerTemp3 .= '
			';
	if (strlen(trim($__compilerTemp3)) > 0) {
		$__compilerTemp1 .= '
			' . $__compilerTemp3 . '
		';
	}
	$__compilerTemp1 .= '
	';
	if (strlen(trim($__compilerTemp1)) > 0) {
		$__finalCompiled .= '
	' . $__compilerTemp1 . '
';
	} else {
		$__finalCompiled .= '
	<div class="menu-row">' . 'No items to display' . '</div>
';
	}
	return $__finalCompiled;
});