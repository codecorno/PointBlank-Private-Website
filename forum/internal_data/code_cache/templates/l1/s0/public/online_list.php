<?php
// FROM HASH: 5eaa74160efcf9029a58a90d06d25860
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Current visitors');
	$__templater->pageParams['pageNumber'] = $__vars['page'];
	$__finalCompiled .= '

';
	$__templater->setPageParam('head.' . 'metaNoindex', $__templater->preEscaped('<meta name="robots" content="noindex" />'));
	$__finalCompiled .= '

<div class="block">
	<div class="block-container">
		<h2 class="block-tabHeader tabs hScroller" data-xf-init="h-scroller">
			<span class="hScroller-scroll">
				' . '
				<a href="' . $__templater->func('link', array('online', ), true) . '" class="tabs-tab ' . (($__vars['typeLimit'] == '') ? 'is-active' : '') . '">' . 'Everyone' . '</a>
				<a href="' . $__templater->func('link', array('online', null, array('type' => 'member', ), ), true) . '" class="tabs-tab ' . (($__vars['typeLimit'] == 'member') ? 'is-active' : '') . '">' . 'Members' . '</a>
				<a href="' . $__templater->func('link', array('online', null, array('type' => 'guest', ), ), true) . '" class="tabs-tab ' . (($__vars['typeLimit'] == 'guest') ? 'is-active' : '') . '">' . 'Guests' . '</a>
				<a href="' . $__templater->func('link', array('online', null, array('type' => 'robot', ), ), true) . '" class="tabs-tab ' . (($__vars['typeLimit'] == 'robot') ? 'is-active' : '') . '">' . 'Robots' . '</a>
				' . '
			</span>
		</h2>
		';
	if (!$__templater->test($__vars['activities'], 'empty', array())) {
		$__finalCompiled .= '
			<ol class="block-body">
				';
		if ($__templater->isTraversable($__vars['activities'])) {
			foreach ($__vars['activities'] AS $__vars['activity']) {
				$__finalCompiled .= '
					<li class="block-row block-row--separated">
						<div class="contentRow">
							<div class="contentRow-figure">
								' . $__templater->func('avatar', array($__vars['activity']['User'], 's', false, array(
				))) . '
							</div>
							<div class="contentRow-main">
								';
				if ($__templater->method($__vars['xf']['visitor'], 'canViewIps', array())) {
					$__finalCompiled .= '
									<div class="contentRow-extra">
										<a href="' . ($__vars['activity']['user_id'] ? $__templater->func('link', array('online/user-ip', null, array('user_id' => $__vars['activity']['user_id'], ), ), true) : $__templater->func('link', array('online/guest-ip', null, array('ip' => $__templater->filter($__vars['activity']['ip'], array(array('hex', array()),), false), ), ), true)) . '" data-xf-click="overlay"><span>' . $__templater->filter($__vars['activity']['ip'], array(array('ip', array()),), true) . '</span></a>
									</div>
								';
				}
				$__finalCompiled .= '
								';
				if ($__vars['activity']['User']) {
					$__finalCompiled .= '
									<h3 class="contentRow-header">' . $__templater->func('username_link', array($__vars['activity']['User'], true, array(
					))) . '</h3>

									' . $__templater->func('user_blurb', array($__vars['activity']['User'], array(
						'class' => 'contentRow-lesser',
					))) . '
								';
				} else if ($__vars['activity']['robot_key']) {
					$__finalCompiled .= '
									<h3 class="contentRow-header">
										' . 'Robot' . $__vars['xf']['language']['label_separator'] . ' ';
					if ($__vars['activity']['robot_link']) {
						$__finalCompiled .= '<a href="' . $__templater->escape($__vars['activity']['robot_link']) . '" target="_blank">' . $__templater->escape($__vars['activity']['robot_title']) . '</a>';
					} else {
						$__finalCompiled .= $__templater->escape($__vars['activity']['robot_title']);
					}
					$__finalCompiled .= '
									</h3>
								';
				} else {
					$__finalCompiled .= '
									<h3 class="contentRow-header">' . 'Guest' . '</h3>
								';
				}
				$__finalCompiled .= '

								<div class="contentRow-minor">
									<ul class="listInline listInline--bullet">
										';
				if ((!$__vars['activity']['User']) OR $__templater->method($__vars['activity']['User'], 'canViewCurrentActivity', array())) {
					$__finalCompiled .= '
											<li>
											';
					if ($__vars['activity']['description']) {
						$__finalCompiled .= '
												' . $__templater->escape($__vars['activity']['description']);
						if ($__vars['activity']['item_title']) {
							$__finalCompiled .= ' <em><a href="' . $__templater->escape($__vars['activity']['item_url']) . '">' . $__templater->escape($__vars['activity']['item_title']) . '</a></em>';
						}
						$__finalCompiled .= '
											';
					} else if ($__vars['xf']['visitor']['is_admin']) {
						$__finalCompiled .= '
												<span title="' . $__templater->escape($__vars['activity']['controller_name']) . '::' . $__templater->escape($__vars['activity']['controller_action']) . '">' . 'Viewing unknown page' . '</span>
											';
					} else {
						$__finalCompiled .= '
												' . 'Viewing unknown page' . '
											';
					}
					$__finalCompiled .= '
											</li>
										';
				}
				$__finalCompiled .= '

										<li>' . $__templater->func('date_dynamic', array($__vars['activity']['view_date'], array(
				))) . '</li>

										';
				if (($__vars['activity']['view_state'] == 'error') AND $__templater->method($__vars['xf']['visitor'], 'canBypassUserPrivacy', array())) {
					$__finalCompiled .= '
											<li>
											' . $__templater->fontAwesome('fa-exclamation-triangle', array(
						'title' => $__templater->filter('Viewing an error', array(array('for_attr', array()),), false),
					)) . '
											<span class="u-srOnly">' . 'Viewing an error' . '</span>
											</li>
										';
				}
				$__finalCompiled .= '
									</ul>
								</div>
							</div>
						</div>
					</li>
				';
			}
		}
		$__finalCompiled .= '
			</ol>
		';
	} else {
		$__finalCompiled .= '
			<div class="block-row">' . 'No results found.' . '</div>
		';
	}
	$__finalCompiled .= '
	</div>

	' . $__templater->func('page_nav', array(array(
		'page' => $__vars['page'],
		'total' => $__vars['total'],
		'link' => 'online',
		'params' => $__vars['linkParams'],
		'wrapperclass' => 'block-outer block-outer--after',
		'perPage' => $__vars['perPage'],
	))) . '
</div>

';
	$__templater->modifySidebarHtml('_xfWidgetPositionSidebarOnlineListSidebar', $__templater->widgetPosition('online_list_sidebar', array()), 'replace');
	return $__finalCompiled;
});