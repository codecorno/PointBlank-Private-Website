<?php
// FROM HASH: 35c67646767b277a6979d4efe2a2d867
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped($__templater->escape($__vars['page']['title']));
	$__finalCompiled .= '
';
	if ($__vars['page']['description']) {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageDescription'] = $__templater->preEscaped($__templater->filter($__vars['page']['description'], array(array('raw', array()),), true));
		$__templater->pageParams['pageDescriptionMeta'] = true;
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

' . $__templater->callMacro('metadata_macros', 'canonical_url', array(
		'canonicalUrl' => $__templater->func('link', array('canonical:pages', $__vars['page'], ), false),
	), $__vars) . '

';
	$__templater->breadcrumbs($__templater->method($__vars['page'], 'getBreadcrumbs', array(false, )));
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	$__compilerTemp2 = '';
	$__compilerTemp2 .= '
		' . $__templater->callMacro('bookmark_macros', 'link', array(
		'content' => $__vars['page']['Node'],
		'confirmUrl' => $__templater->func('link', array('pages/bookmark', $__vars['page'], ), false),
		'class' => 'button button--link',
	), $__vars) . '
	';
	if (strlen(trim($__compilerTemp2)) > 0) {
		$__compilerTemp1 .= '
	' . $__compilerTemp2 . '
';
	}
	$__templater->pageParams['pageAction'] = $__templater->preEscaped($__compilerTemp1);
	$__finalCompiled .= '

';
	$__vars['logVisits'] = $__templater->preEscaped('
	<div class="block-outer block-outer--after">
		<ul class="listInline listInline--bullet">
			<li><dl class="pairs pairs--inline">
				<dt>' . 'Published' . '</dt>
				<dd>' . $__templater->func('date', array($__vars['page']['publish_date'], ), true) . '</dd>
			</dl></li>
			<li><dl class="pairs pairs--inline">
				<dt>' . 'Page views' . '</dt>
				<dd>' . $__templater->func('number', array($__vars['page']['view_count'], ), true) . '</dd>
			</dl></li>
		</ul>
	</div>
');
	$__finalCompiled .= '

';
	if ($__vars['page']['advanced_mode']) {
		$__finalCompiled .= '
	' . $__templater->includeTemplate($__templater->method($__vars['page'], 'getTemplateName', array()), $__vars) . '

	';
		if ($__vars['page']['log_visits']) {
			$__finalCompiled .= '
		<div class="block">
			' . $__templater->filter($__vars['logVisits'], array(array('raw', array()),), true) . '
		</div>
	';
		}
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	<div class="block">
		<div class="block-container">
			<div class="block-body block-row">
				' . $__templater->includeTemplate($__templater->method($__vars['page'], 'getTemplateName', array()), $__vars) . '
			</div>
		</div>
		';
		if ($__vars['page']['log_visits']) {
			$__finalCompiled .= '
			' . $__templater->filter($__vars['logVisits'], array(array('raw', array()),), true) . '
		';
		}
		$__finalCompiled .= '
	</div>
';
	}
	$__finalCompiled .= '

<div class="blockMessage blockMessage--none">
	' . $__templater->callMacro('share_page_macros', 'buttons', array(
		'iconic' => true,
		'label' => 'Share' . $__vars['xf']['language']['label_separator'],
	), $__vars) . '
</div>

';
	$__compilerTemp3 = '';
	$__compilerTemp4 = '';
	$__compilerTemp4 .= '
				';
	if ($__vars['page']['list_siblings'] AND !$__templater->test($__vars['siblings'], 'empty', array())) {
		$__compilerTemp4 .= '
					';
		if ($__vars['parent']) {
			$__compilerTemp4 .= '
						<a href="' . $__templater->func('link', array($__templater->method($__vars['parent'], 'getRoute', array()), $__vars['parent'], ), true) . '" class="blockLink">' . $__templater->escape($__vars['parent']['title']) . '</a>
					';
		}
		$__compilerTemp4 .= '

					';
		if ($__templater->isTraversable($__vars['siblings'])) {
			foreach ($__vars['siblings'] AS $__vars['node']) {
				$__compilerTemp4 .= '
						<a href="' . $__templater->func('link', array($__templater->method($__vars['node'], 'getRoute', array()), $__vars['node'], ), true) . '" class="blockLink ' . (($__vars['node']['node_id'] == $__vars['page']['node_id']) ? 'is-selected' : '') . '">
							<span class="u-depth' . ($__vars['parent'] ? 1 : 0) . '">' . $__templater->escape($__vars['node']['title']) . '</span>
						</a>
						';
				if (($__vars['node']['node_id'] == $__vars['page']['node_id']) AND ($__vars['page']['list_children'] AND !$__templater->test($__vars['children'], 'empty', array()))) {
					$__compilerTemp4 .= '
							';
					if ($__templater->isTraversable($__vars['children'])) {
						foreach ($__vars['children'] AS $__vars['childNode']) {
							$__compilerTemp4 .= '
								<a href="' . $__templater->func('link', array($__templater->method($__vars['childNode'], 'getRoute', array()), $__vars['childNode'], ), true) . '" class="blockLink">
									<span class="u-depth' . ($__vars['parent'] ? 2 : 1) . '">' . $__templater->escape($__vars['childNode']['title']) . '</span>
								</a>
							';
						}
					}
					$__compilerTemp4 .= '
						';
				}
				$__compilerTemp4 .= '
					';
			}
		}
		$__compilerTemp4 .= '
				';
	} else if ($__vars['page']['list_children'] AND !$__templater->test($__vars['children'], 'empty', array())) {
		$__compilerTemp4 .= '
						';
		if ($__templater->isTraversable($__vars['children'])) {
			foreach ($__vars['children'] AS $__vars['childNode']) {
				$__compilerTemp4 .= '
							<a href="' . $__templater->func('link', array($__templater->method($__vars['childNode'], 'getRoute', array()), $__vars['childNode'], ), true) . '" class="blockLink">
								' . $__templater->escape($__vars['childNode']['title']) . '
							</a>
						';
			}
		}
		$__compilerTemp4 .= '
				';
	}
	$__compilerTemp4 .= '
			';
	if (strlen(trim($__compilerTemp4)) > 0) {
		$__compilerTemp3 .= '
	<div class="block">
		<div class="block-container">
			<div class="block-body">
			' . $__compilerTemp4 . '
			</div>
		</div>
	</div>
	';
	}
	$__templater->modifySideNavHtml(null, '
	' . $__compilerTemp3 . '
', 'replace');
	return $__finalCompiled;
});