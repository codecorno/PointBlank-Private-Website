<?php
// FROM HASH: 3a11de3a168ab2f2802d4c2de0e06dde
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageH1'] = $__templater->preEscaped($__templater->escape($__vars['xf']['options']['boardTitle']));
	$__finalCompiled .= '
';
	if ($__vars['xf']['options']['forumsDefaultPage'] != 'new_posts') {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('New posts');
		$__templater->pageParams['pageNumber'] = $__vars['page'];
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

' . $__templater->callMacro('metadata_macros', 'metadata', array(
		'description' => $__vars['xf']['options']['boardDescription'],
	), $__vars) . '

';
	if (($__vars['page'] > 1) OR $__vars['originalFindNewId']) {
		$__finalCompiled .= '
	';
		$__templater->setPageParam('head.' . 'metaNoindex', $__templater->preEscaped('<meta name="robots" content="noindex" />'));
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '
';
	if ($__vars['page'] <= 1) {
		$__finalCompiled .= '
	' . $__templater->callMacro('metadata_macros', 'canonical_url', array(
			'canonicalUrl' => $__templater->func('link', array('canonical:' . $__vars['selfRoute'], ), false),
		), $__vars) . '
';
	}
	$__finalCompiled .= '

';
	$__compilerTemp1 = $__vars;
	$__compilerTemp1['pageSelected'] = $__templater->preEscaped('new_posts');
	$__templater->wrapTemplate('forum_overview_wrapper', $__compilerTemp1);
	$__finalCompiled .= '

';
	$__templater->modifySidebarHtml('_xfWidgetPositionSidebarForumNewPostsSidebar', $__templater->widgetPosition('forum_new_posts_sidebar', array()), 'replace');
	$__finalCompiled .= '

';
	if ($__vars['canInlineMod']) {
		$__finalCompiled .= '
	';
		$__templater->includeJs(array(
			'src' => 'xf/inline_mod.js',
			'min' => '1',
		));
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

<div class="block" data-xf-init="' . ($__vars['canInlineMod'] ? 'inline-mod' : '') . '" data-type="thread" data-href="' . $__templater->func('link', array('inline-mod', ), true) . '">
	';
	if ($__vars['findNew']['result_count']) {
		$__finalCompiled .= '
		<div class="block-outer">';
		$__compilerTemp2 = '';
		$__compilerTemp3 = '';
		$__compilerTemp3 .= '
						' . $__templater->callMacro('whats_new_posts', 'buttons', array(
			'findNew' => $__vars['findNew'],
			'canInlineMod' => $__vars['canInlineMod'],
		), $__vars) . '
					';
		if (strlen(trim($__compilerTemp3)) > 0) {
			$__compilerTemp2 .= '
				<div class="block-outer-opposite">
					<div class="buttonGroup">
					' . $__compilerTemp3 . '
					</div>
				</div>
			';
		}
		$__finalCompiled .= trim('
			' . $__templater->func('page_nav', array(array(
			'page' => $__vars['page'],
			'total' => $__vars['findNew']['result_count'],
			'link' => 'forums/new-posts',
			'params' => array('f' => $__vars['findNew']['find_new_id'], ),
			'wrapperclass' => 'block-outer-main',
			'perPage' => $__vars['perPage'],
		))) . '

			' . $__compilerTemp2 . '
		') . '</div>
	';
	}
	$__finalCompiled .= '

	<div class="block-container">
		' . $__templater->callMacro('whats_new_posts', 'filter_bar', array(
		'findNew' => $__vars['findNew'],
		'rerunRoute' => 'forums/new-posts',
		'rerunQuery' => array('f' => $__vars['findNew']['find_new_id'], ),
		'filterRoute' => 'forums/new-posts',
	), $__vars) . '

		' . $__templater->callMacro('whats_new_posts', 'results', array(
		'findNew' => $__vars['findNew'],
		'threads' => $__vars['threads'],
		'latestRoute' => 'forums/new-posts',
	), $__vars) . '
	</div>

	';
	if ($__vars['findNew']['result_count']) {
		$__finalCompiled .= '
		<div class="block-outer block-outer--after">
			' . $__templater->func('page_nav', array(array(
			'page' => $__vars['page'],
			'total' => $__vars['findNew']['result_count'],
			'link' => 'forums/new-posts',
			'params' => array('f' => $__vars['findNew']['find_new_id'], ),
			'wrapperclass' => 'block-outer-main',
			'perPage' => $__vars['perPage'],
		))) . '

			' . $__templater->func('show_ignored', array(array(
			'wrapperclass' => 'block-outer-opposite',
		))) . '
		</div>
	';
	}
	$__finalCompiled .= '
</div>';
	return $__finalCompiled;
});