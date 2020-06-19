<?php
// FROM HASH: 378b973d6c53ac25f1c0d8042c90b68b
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Latest activity');
	$__finalCompiled .= '

';
	$__compilerTemp1 = $__vars;
	$__compilerTemp1['pageSelected'] = 'latest_activity';
	$__templater->wrapTemplate('whats_new_wrapper', $__compilerTemp1);
	$__finalCompiled .= '

<div class="block">
	<div class="block-container">
		';
	if (!$__templater->test($__vars['newsFeedItems'], 'empty', array())) {
		$__finalCompiled .= '
			<ul class="block-body js-newsFeedTarget">
				';
		if ($__templater->isTraversable($__vars['newsFeedItems'])) {
			foreach ($__vars['newsFeedItems'] AS $__vars['item']) {
				$__finalCompiled .= '
					' . $__templater->callMacro('news_feed_macros', 'feed_row', array(
					'item' => $__vars['item'],
				), $__vars) . '
				';
			}
		}
		$__finalCompiled .= '
			</ul>
			<div class="block-footer js-newsFeedLoadMore">
				<span class="block-footer-controls">' . $__templater->button('
					' . 'Show older items' . '
				', array(
			'href' => $__templater->func('link', array('whats-new/latest-activity', null, array('before_id' => $__vars['oldestItemId'], ), ), false),
			'rel' => 'nofollow',
			'data-xf-click' => 'inserter',
			'data-append' => '.js-newsFeedTarget',
			'data-replace' => '.js-newsFeedLoadMore',
		), '', array(
		)) . '</span>
			</div>
		';
	} else if ($__vars['beforeId']) {
		$__finalCompiled .= '
			<div class="block-body js-newsFeedTarget">
				<div class="block-row block-row--separated">' . 'There are no more items to show.' . '</div>
			</div>
		';
	} else {
		$__finalCompiled .= '
			<div class="block-body js-newsFeedTarget">
				<div class="block-row">' . 'The news feed is currently empty.' . '</div>
			</div>
		';
	}
	$__finalCompiled .= '
	</div>
</div>';
	return $__finalCompiled;
});