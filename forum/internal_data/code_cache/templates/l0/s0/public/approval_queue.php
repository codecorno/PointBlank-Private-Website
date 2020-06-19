<?php
// FROM HASH: ab0c70366d245eaeab8a0be55298e484
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Approval queue');
	$__finalCompiled .= '

';
	$__templater->includeCss('approval_queue.less');
	$__finalCompiled .= '
';
	$__templater->includeJs(array(
		'src' => 'xf/approval_queue.js',
	));
	$__finalCompiled .= '

';
	$__vars['sortOrders'] = array('content_date' => 'Content date', );
	$__finalCompiled .= '

<div class="block">
	<div class="block-container block-container--none">
		<div class="block-filterBar block-filterBar--standalone">
			<div class="filterBar">
				';
	$__compilerTemp1 = '';
	$__compilerTemp1 .= '
							';
	if ($__vars['filters']['content_type']) {
		$__compilerTemp1 .= '
								<li><a href="' . $__templater->func('link', array('approval-queue', null, $__templater->filter($__vars['filters'], array(array('replace', array(array('content_type' => null, ), )),), false), ), true) . '"
									class="filterBar-filterToggle" data-xf-init="tooltip" title="' . $__templater->filter('Remove this filter', array(array('for_attr', array()),), true) . '">
									<span class="filterBar-filterToggle-label">' . 'Content type' . $__vars['xf']['language']['label_separator'] . '</span>
									' . ($__templater->escape($__templater->method($__vars['xf']['app'], 'getContentTypePhrase', array($__vars['filters']['content_type'], ))) ?: $__templater->escape($__vars['filters']['content_type'])) . '
								</a></li>
							';
	}
	$__compilerTemp1 .= '
							';
	if ($__vars['filters']['order'] AND $__vars['sortOrders'][$__vars['filters']['order']]) {
		$__compilerTemp1 .= '
								<li><a href="' . $__templater->func('link', array('approval-queue', null, $__templater->filter($__vars['filters'], array(array('replace', array(array('order' => null, 'direction' => null, ), )),), false), ), true) . '"
									class="filterBar-filterToggle" data-xf-init="tooltip" title="' . $__templater->filter('Return to the default order', array(array('for_attr', array()),), true) . '">
									<span class="filterBar-filterToggle-label">' . 'Sort by' . $__vars['xf']['language']['label_separator'] . '</span>
									' . $__templater->escape($__vars['sortOrders'][$__vars['filters']['order']]) . '
									' . $__templater->fontAwesome((($__vars['filters']['direction'] == 'asc') ? 'fa-angle-up' : 'fa-angle-down'), array(
		)) . '
									<span class="u-srOnly">';
		if ($__vars['filters']['direction'] == 'asc') {
			$__compilerTemp1 .= 'Ascending';
		} else {
			$__compilerTemp1 .= 'Descending';
		}
		$__compilerTemp1 .= '</span>
								</a></li>
							';
	}
	$__compilerTemp1 .= '
						';
	if (strlen(trim($__compilerTemp1)) > 0) {
		$__finalCompiled .= '
					<ul class="filterBar-filters">
						' . $__compilerTemp1 . '
					</ul>
				';
	}
	$__finalCompiled .= '

				<a class="filterBar-menuTrigger" data-xf-click="menu" role="button" tabindex="0" aria-expanded="false" aria-haspopup="true">' . 'Filters' . '</a>
				<div class="menu menu--wide" data-menu="menu" aria-hidden="true"
					data-href="' . $__templater->func('link', array('approval-queue/filters', null, $__vars['filters'], ), true) . '"
					data-load-target=".js-filterMenuBody">
					<div class="menu-content">
						<h4 class="menu-header">' . 'Show only' . $__vars['xf']['language']['label_separator'] . '</h4>
						<div class="js-filterMenuBody">
							<div class="menu-row">' . 'Loading' . $__vars['xf']['language']['ellipsis'] . '</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

';
	if (!$__templater->test($__vars['unapprovedItems'], 'empty', array())) {
		$__finalCompiled .= '
	';
		$__compilerTemp2 = '';
		$__vars['i'] = 0;
		if ($__templater->isTraversable($__vars['unapprovedItems'])) {
			foreach ($__vars['unapprovedItems'] AS $__vars['unapprovedItem']) {
				$__vars['i']++;
				$__compilerTemp2 .= '
				<div class="block">
					<div class="block-container approvalQueue-item js-approvalQueue-item">
						<div class="block-body">
							' . $__templater->filter($__templater->method($__templater->method($__vars['unapprovedItem'], 'getHandler', array()), 'render', array($__vars['unapprovedItem'], )), array(array('raw', array()),), true) . '
						</div>
					</div>
				</div>
			';
			}
		}
		$__finalCompiled .= $__templater->form('
		<div class="blocks">
			' . $__compilerTemp2 . '

			<div class="block">
				<div class="block-container block-container--none">
					' . $__templater->formSubmitRow(array(
			'icon' => 'save',
			'sticky' => '.js-stickyParent',
		), array(
			'rowtype' => 'standalone',
		)) . '
				</div>
			</div>
		</div>
	', array(
			'action' => $__templater->func('link', array('approval-queue/process', ), false),
			'ajax' => 'true',
			'class' => 'js-stickyParent approvalQueue',
		)) . '
';
	} else if ($__vars['filters']) {
		$__finalCompiled .= '
	<div class="blockMessage">' . 'No items matched your filter.' . '</div>
';
	} else {
		$__finalCompiled .= '
	<div class="blockMessage">' . 'There is no content currently awaiting approval.' . '</div>
';
	}
	return $__finalCompiled;
});