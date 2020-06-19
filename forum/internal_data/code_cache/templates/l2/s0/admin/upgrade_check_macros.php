<?php
// FROM HASH: 843456ed5497913edfc9d3189c27d1fa
return array('macros' => array('serious_errors' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'upgradeCheck' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	$__compilerTemp1 = '';
	$__compilerTemp1 .= '
				';
	if ($__vars['upgradeCheck']['error_code']) {
		$__compilerTemp1 .= '
					<li>' . $__templater->escape($__vars['upgradeCheck']['error_message']) . '</li>
				';
	} else {
		$__compilerTemp1 .= '
					';
		if (!$__vars['upgradeCheck']['branding_valid']) {
			$__compilerTemp1 .= '
						<li>' . 'The default copyright notice/branding has been removed and your license does not permit you to do that.' . '</li>
					';
		}
		$__compilerTemp1 .= '
					';
		if ($__templater->isTraversable($__vars['upgradeCheck']['invalid_add_ons'])) {
			foreach ($__vars['upgradeCheck']['invalid_add_ons'] AS $__vars['addOnId'] => $__vars['reason']) {
				$__compilerTemp1 .= '
						';
				if ($__vars['reason'] == 'using_newer') {
					$__compilerTemp1 .= '
							<li>' . 'You are running a version of ' . $__templater->escape($__vars['upgradeCheck']['RelevantAddOns'][$__vars['addOnId']]['title']) . ' which is newer than your license permits.' . '
							</li>
						';
				} else if ($__vars['reason'] == 'no_license') {
					$__compilerTemp1 .= '
							<li>' . 'You are running ' . $__templater->escape($__vars['upgradeCheck']['RelevantAddOns'][$__vars['addOnId']]['title']) . ' which your license does not permit you to.' . '
							</li>
						';
				}
				$__compilerTemp1 .= '
					';
			}
		}
		$__compilerTemp1 .= '
				';
	}
	$__compilerTemp1 .= '
			';
	if (strlen(trim($__compilerTemp1)) > 0) {
		$__finalCompiled .= '
		<div class="blockMessage blockMessage--error blockMessage--iconic">
			' . 'The following issues must be resolved as soon as possible. Once they have been resolved, <a href="' . $__templater->func('link', array('tools/upgrade-check', ), true) . '">check for upgrades again</a> to confirm.' . '
			<ul>
			' . $__compilerTemp1 . '
			</ul>
		</div>
	';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},
'warnings' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'upgradeCheck' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	if (!$__templater->method($__vars['upgradeCheck'], 'hasLicenseErrors', array())) {
		$__finalCompiled .= '
		';
		$__compilerTemp1 = '';
		$__compilerTemp1 .= '
					';
		if (!$__vars['upgradeCheck']['board_url_valid']) {
			$__compilerTemp1 .= '
						<li>' . 'The <a href="' . ($__templater->func('link', array('options/groups/basicBoard/', ), true) . '#boardUrl') . '">Board URL option</a> does not match the site URL configured for your license.' . '
						</li>
					';
		}
		$__compilerTemp1 .= '
					';
		if ($__vars['upgradeCheck']['license_expired']) {
			$__compilerTemp1 .= '
						<li>' . 'Your license has now expired. You will not have access to new versions or support. To continue to get the latest updates and receive support, you should <a href="https://xenforo.com/customers" target="_blank">renew now</a>.' . '</li>
					';
		}
		$__compilerTemp1 .= '
				';
		if (strlen(trim($__compilerTemp1)) > 0) {
			$__finalCompiled .= '
			<div class="blockMessage blockMessage--warning blockMessage--iconic">
				' . 'The following issues should be resolved. Once they have been resolved, <a href="' . $__templater->func('link', array('tools/upgrade-check', ), true) . '">check for upgrades again</a> to confirm.' . '
				<ul>
				' . $__compilerTemp1 . '
				</ul>
			</div>
		';
		}
		$__finalCompiled .= '
	';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},
'updates' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'upgradeCheck' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	if ($__templater->method($__vars['xf']['visitor'], 'hasAdminPermission', array('upgradeXenForo', )) AND ($__vars['upgradeCheck']['available_updates'] AND (!$__templater->method($__vars['upgradeCheck'], 'hasLicenseErrors', array())))) {
		$__finalCompiled .= '
		';
		$__compilerTemp1 = '';
		$__compilerTemp1 .= '
					';
		if ($__templater->method($__vars['upgradeCheck'], 'hasAvailableUpdate', array('XF', ))) {
			$__compilerTemp1 .= '
						' . 'XenForo ' . $__templater->escape($__vars['upgradeCheck']['available_updates']['XF']['version_string']) . ' is now available.' . '
						<div style="margin-top: .5em">
							' . $__templater->button('Upgrade now' . '
							', array(
				'href' => $__templater->func('link', array('tools/upgrade-xf', ), false),
				'class' => 'button--link',
			), '', array(
			)) . '
						</div>
					';
		} else if ($__templater->method($__vars['upgradeCheck'], 'hasAvailableAddOnUpdate', array())) {
			$__compilerTemp1 .= '
						' . 'The following updates to official add-ons are now available:' . '
						<ul class="listInline listInline--comma listInline--selfInline">
							';
			if ($__templater->isTraversable($__vars['upgradeCheck']['available_updates'])) {
				foreach ($__vars['upgradeCheck']['available_updates'] AS $__vars['addOnId'] => $__vars['update']) {
					if ($__templater->method($__vars['upgradeCheck'], 'hasAvailableUpdate', array($__vars['addOnId'], ))) {
						$__compilerTemp1 .= '
								<li>' . $__templater->escape($__vars['upgradeCheck']['RelevantAddOns'][$__vars['addOnId']]['title']) . ' ' . $__templater->escape($__vars['update']['version_string']) . '</li>
							';
					}
				}
			}
			$__compilerTemp1 .= '
						</ul>
						<div style="margin-top: .5em">
							' . $__templater->button('
								' . 'Upgrade now' . '
							', array(
				'href' => $__templater->func('link', array('tools/upgrade-xf-add-on', ), false),
				'class' => 'button--link',
			), '', array(
			)) . '
						</div>
					';
		}
		$__compilerTemp1 .= '
				';
		if (strlen(trim($__compilerTemp1)) > 0) {
			$__finalCompiled .= '
			<div class="blockMessage blockMessage--highlight blockMessage--iconic">
				' . $__compilerTemp1 . '
			</div>
		';
		}
		$__finalCompiled .= '
	';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},
'full_status' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'upgradeCheck' => '!',
		'showSeriousErrors' => false,
		'showWarnings' => true,
		'showUpdates' => true,
		'showSuccess' => false,
	), $__arguments, $__vars);
	$__finalCompiled .= '

	';
	if ($__vars['showSeriousErrors']) {
		$__finalCompiled .= '
		' . $__templater->callMacro(null, 'serious_errors', array(
			'upgradeCheck' => $__vars['upgradeCheck'],
		), $__vars) . '
	';
	}
	$__finalCompiled .= '
	';
	if ($__vars['showWarnings']) {
		$__finalCompiled .= '
		' . $__templater->callMacro(null, 'warnings', array(
			'upgradeCheck' => $__vars['upgradeCheck'],
		), $__vars) . '
	';
	}
	$__finalCompiled .= '
	';
	if ($__vars['showUpdates']) {
		$__finalCompiled .= '
		' . $__templater->callMacro(null, 'updates', array(
			'upgradeCheck' => $__vars['upgradeCheck'],
		), $__vars) . '
	';
	}
	$__finalCompiled .= '

	';
	if ($__vars['showSuccess'] AND (!$__templater->method($__vars['upgradeCheck'], 'hasNotice', array()))) {
		$__finalCompiled .= '
		<div class="blockMessage blockMessage--success">
			' . 'Your version of XenForo and official add-ons are fully up-to-date. No issues were identified with your license.' . '
		</div>
	';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '

' . '

' . '

';
	return $__finalCompiled;
});