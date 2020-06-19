<?php
// FROM HASH: 2a15f64e79c120236d56a61bb5e18a1e
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Registered members');
	$__templater->pageParams['pageNumber'] = $__vars['page'];
	$__finalCompiled .= '

';
	$__compilerTemp1 = $__vars;
	$__compilerTemp1['pageSelected'] = 'member_list';
	$__templater->wrapTemplate('member_wrapper', $__compilerTemp1);
	$__finalCompiled .= '

' . $__templater->callMacro('metadata_macros', 'canonical_url', array(
		'canonicalUrl' => $__templater->func('link', array('canonical:members/list', null, array('page' => $__vars['page'], ), ), false),
	), $__vars) . '

<div class="block">
	<div class="block-container">
		<ol class="block-body">
			';
	if ($__templater->isTraversable($__vars['users'])) {
		foreach ($__vars['users'] AS $__vars['userId'] => $__vars['user']) {
			$__finalCompiled .= '
				<li class="block-row block-row--separated">
					' . $__templater->callMacro('member_list_macros', 'item', array(
				'user' => $__vars['user'],
			), $__vars) . '
				</li>
			';
		}
	}
	$__finalCompiled .= '
		</ol>
	</div>
	' . $__templater->func('page_nav', array(array(
		'link' => 'members/list',
		'page' => $__vars['page'],
		'total' => $__vars['total'],
		'wrapperclass' => 'block-outer block-outer--after',
		'perPage' => $__vars['perPage'],
	))) . '
</div>

';
	$__templater->modifySidebarHtml('_xfWidgetPositionSidebarMemberListSidebar', $__templater->widgetPosition('member_list_sidebar', array()), 'replace');
	return $__finalCompiled;
});