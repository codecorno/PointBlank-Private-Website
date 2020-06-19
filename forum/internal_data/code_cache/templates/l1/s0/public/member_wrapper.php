<?php
// FROM HASH: 5e5632a2beb07582566a673fab7f7243
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__compilerTemp1 = '';
	if ($__templater->isTraversable($__vars['memberStats'])) {
		foreach ($__vars['memberStats'] AS $__vars['key'] => $__vars['memberStat']) {
			$__compilerTemp1 .= '
					<a class="blockLink ' . (($__vars['pageSelected'] == $__vars['key']) ? 'is-selected' : '') . '" href="' . $__templater->func('link', array('members', null, array('key' => $__vars['memberStat']['member_stat_key'], ), ), true) . '">' . $__templater->escape($__vars['memberStat']['title']) . '</a>
				';
		}
	}
	$__compilerTemp2 = '';
	if ($__vars['xf']['options']['enableMemberList']) {
		$__compilerTemp2 .= '
					<a class="blockLink ' . (($__vars['pageSelected'] == 'member_list') ? 'is-selected' : '') . '" href="' . $__templater->func('link', array('members/list', ), true) . '">' . 'Registered members' . '</a>
				';
	}
	$__templater->modifySideNavHtml(null, '
	<div class="block">
		<div class="block-container">
			<h3 class="block-header">' . 'Members' . '</h3>
			<div class="block-body">
				<a class="blockLink ' . (($__vars['pageSelected'] == 'overview') ? 'is-selected' : '') . '" href="' . $__templater->func('link', array('members', ), true) . '">' . 'Overview' . '</a>
				' . $__compilerTemp1 . '
				' . $__compilerTemp2 . '
			</div>
		</div>
	</div>
', 'replace');
	$__finalCompiled .= '
';
	$__templater->setPageParam('sideNavTitle', 'Members');
	$__finalCompiled .= '

';
	$__templater->modifySideNavHtml('_xfWidgetPositionSideNavMemberWrapperSidenav', $__templater->widgetPosition('member_wrapper_sidenav', array()), 'replace');
	$__finalCompiled .= '
' . $__templater->filter($__vars['innerContent'], array(array('raw', array()),), true);
	return $__finalCompiled;
});