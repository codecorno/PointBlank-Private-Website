<?php
// FROM HASH: bc56c8d7f84e05e18c412be96317c524
return array('macros' => array('overview_block' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'memberStat' => '!',
		'results' => '!',
		'showTitle' => true,
		'showFooter' => true,
	), $__arguments, $__vars);
	$__finalCompiled .= '

	';
	$__compilerTemp1 = '';
	$__compilerTemp1 .= '
					';
	if ($__templater->isTraversable($__vars['results'])) {
		foreach ($__vars['results'] AS $__vars['userId'] => $__vars['data']) {
			$__compilerTemp1 .= '
						<li>
							' . $__templater->callMacro(null, 'overview_row', array(
				'data' => $__vars['data'],
			), $__vars) . '
						</li>
					';
		}
	}
	$__compilerTemp1 .= '
				';
	if (strlen(trim($__compilerTemp1)) > 0) {
		$__finalCompiled .= '
		<li class="memberOverviewBlock">
			';
		if ($__vars['showTitle']) {
			$__finalCompiled .= '
				<h3 class="block-textHeader">
					<a href="' . $__templater->func('link', array('members', null, array('key' => $__vars['memberStat']['member_stat_key'], ), ), true) . '"
						class="memberOverViewBlock-title">' . $__templater->escape($__vars['memberStat']['title']) . '</a>
				</h3>
			';
		}
		$__finalCompiled .= '
			<ol class="memberOverviewBlock-list">
				' . $__compilerTemp1 . '
			</ol>
			';
		if ($__vars['showFooter']) {
			$__finalCompiled .= '
				<div class="memberOverviewBlock-seeMore">
					<a href="' . $__templater->func('link', array('members', null, array('key' => $__vars['memberStat']['member_stat_key'], ), ), true) . '">' . 'See more' . $__vars['xf']['language']['ellipsis'] . '</a>
				</div>
			';
		}
		$__finalCompiled .= '
		</li>
	';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},
'overview_row' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'data' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	<div class="contentRow contentRow--alignMiddle">
		<div class="contentRow-figure">
			' . $__templater->func('avatar', array($__vars['data']['user'], 'xs', false, array(
	))) . '
		</div>
		<div class="contentRow-main">
			';
	if ($__vars['data']['value']) {
		$__finalCompiled .= '
				<div class="contentRow-extra contentRow-extra--large">' . $__templater->escape($__vars['data']['value']) . '</div>
			';
	}
	$__finalCompiled .= '
			<h3 class="contentRow-title">' . $__templater->func('username_link', array($__vars['data']['user'], true, array(
	))) . '</h3>
		</div>
	</div>
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped(($__vars['active'] ? $__templater->escape($__vars['active']['title']) : 'Notable members'));
	$__finalCompiled .= '

';
	$__compilerTemp1 = $__vars;
	$__compilerTemp1['pageSelected'] = ($__vars['active'] ? $__vars['active']['member_stat_key'] : 'overview');
	$__templater->wrapTemplate('member_wrapper', $__compilerTemp1);
	$__finalCompiled .= '

' . $__templater->callMacro('metadata_macros', 'canonical_url', array(
		'canonicalUrl' => $__templater->func('link', array('canonical:members', ), false),
	), $__vars) . '

';
	$__templater->includeCss('member.less');
	$__finalCompiled .= '

';
	if ($__vars['userNotFound']) {
		$__finalCompiled .= '
	<div class="blockMessage blockMessage--error blockMessage--iconic">' . 'The specified member cannot be found. Please enter a member\'s entire name.' . '</div>
';
	}
	$__finalCompiled .= '

';
	if ($__templater->test($__vars['memberStats'], 'empty', array())) {
		$__finalCompiled .= '
	<div class="blockMessage">' . 'No notable members can currently be shown.' . '</div>
';
	} else {
		$__finalCompiled .= '
	<section class="block">
		<div class="block-container">
			';
		if ($__vars['active']) {
			$__finalCompiled .= '
				<ol class="block-body">
					';
			$__compilerTemp2 = true;
			if ($__templater->isTraversable($__vars['resultsData'][$__vars['active']['member_stat_key']])) {
				foreach ($__vars['resultsData'][$__vars['active']['member_stat_key']] AS $__vars['userId'] => $__vars['data']) {
					$__compilerTemp2 = false;
					$__finalCompiled .= '
						<li class="block-row block-row--separated">
							' . $__templater->callMacro('member_list_macros', 'item', array(
						'user' => $__vars['data']['user'],
						'extraData' => $__vars['data']['value'],
						'extraDataBig' => true,
					), $__vars) . '
						</li>
					';
				}
			}
			if ($__compilerTemp2) {
				$__finalCompiled .= '
						<li class="block-row">' . 'No users match the specified criteria.' . '</li>
					';
			}
			$__finalCompiled .= '
				</ol>
			';
		} else {
			$__finalCompiled .= '
				<div class="block-body">
					<ol class="memberOverviewBlocks">
						';
			if ($__templater->isTraversable($__vars['memberStats'])) {
				foreach ($__vars['memberStats'] AS $__vars['key'] => $__vars['memberStat']) {
					$__finalCompiled .= '
							' . $__templater->callMacro(null, 'overview_block', array(
						'memberStat' => $__vars['memberStat'],
						'results' => $__vars['resultsData'][$__vars['key']],
					), $__vars) . '
						';
				}
			}
			$__finalCompiled .= '
					</ol>
				</div>
			';
		}
		$__finalCompiled .= '
		</div>
	</section>
';
	}
	$__finalCompiled .= '

' . '

';
	return $__finalCompiled;
});