<?php
// FROM HASH: 064fbec64adb9f2fb25586da93064828
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if (!$__templater->test($__vars['pages'], 'empty', array())) {
		$__finalCompiled .= '
	';
		$__compilerTemp1 = '';
		if ($__templater->isTraversable($__vars['pages'])) {
			foreach ($__vars['pages'] AS $__vars['page']) {
				$__compilerTemp1 .= '
						';
				if ((($__vars['page']['page_id'] == 'trophies') AND $__vars['xf']['options']['enableTrophies']) OR ($__vars['page']['page_id'] != 'trophies')) {
					$__compilerTemp1 .= '
							<a href="' . $__templater->escape($__vars['page']['public_url']) . '" class="blockLink ' . (($__vars['pageSelected'] == $__vars['page']['page_name']) ? 'is-selected' : '') . '">' . $__templater->escape($__vars['page']['title']) . '</a>
						';
				}
				$__compilerTemp1 .= '
					';
			}
		}
		$__templater->modifySideNavHtml(null, '
		<div class="block">
			<div class="block-container">
				<h2 class="block-header">' . 'Help' . '</h2>
				<div class="block-body">
					' . $__compilerTemp1 . '
				</div>
			</div>
		</div>
	', 'replace');
		$__finalCompiled .= '
	';
		$__templater->setPageParam('sideNavTitle', 'Help pages');
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	$__templater->modifySideNavHtml('_xfWidgetPositionSideNavHelpPageSidenav', $__templater->widgetPosition('help_page_sidenav', array()), 'replace');
	$__finalCompiled .= '

' . $__templater->filter($__vars['innerContent'], array(array('raw', array()),), true);
	return $__finalCompiled;
});