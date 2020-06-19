<?php
// FROM HASH: 125043056b16d59b644304e746fc319e
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if (!$__templater->test($__vars['threads'], 'empty', array()) OR ($__templater->test($__vars['threads'], 'empty', array()) AND ($__vars['filter'] != 'latest'))) {
		$__finalCompiled .= '
	<div class="block"' . $__templater->func('widget_data', array($__vars['widget'], ), true) . '>
		<div class="block-container">
			';
		if ($__vars['style'] == 'full') {
			$__finalCompiled .= '
				<h3 class="block-header">
					<a href="' . $__templater->escape($__vars['link']) . '" rel="nofollow">' . $__templater->escape($__vars['title']) . '</a>
				</h3>
				<div class="block-body">
					<div class="structItemContainer">
						';
			if (!$__templater->test($__vars['threads'], 'empty', array())) {
				$__finalCompiled .= '
							';
				if ($__templater->isTraversable($__vars['threads'])) {
					foreach ($__vars['threads'] AS $__vars['thread']) {
						$__finalCompiled .= '
								' . $__templater->callMacro('thread_list_macros', 'item', array(
							'allowInlineMod' => false,
							'thread' => $__vars['thread'],
						), $__vars) . '
							';
					}
				}
				$__finalCompiled .= '
						';
			} else if ($__vars['filter'] == 'unread') {
				$__finalCompiled .= '
							<div class="block-row">
								' . 'You have no unread posts. You may <a href="' . $__templater->func('link', array('whats-new/posts', null, array('skip' => 1, ), ), true) . '" rel="nofollow">view all latest posts</a> instead.' . '
							</div>
						';
			} else {
				$__finalCompiled .= '
							<div class="block-row">
								' . 'No results found.' . '
							</div>
						';
			}
			$__finalCompiled .= '
					</div>
				</div>
				';
			if ($__vars['hasMore']) {
				$__finalCompiled .= '
					<div class="block-footer">
						<span class="block-footer-controls">
							' . $__templater->button('View more' . $__vars['xf']['language']['ellipsis'], array(
					'href' => $__vars['link'],
					'rel' => 'nofollow',
				), '', array(
				)) . '
						</span>
					</div>
				';
			}
			$__finalCompiled .= '
			';
		} else {
			$__finalCompiled .= '
				<h3 class="block-minorHeader">
					<a href="' . $__templater->escape($__vars['link']) . '" rel="nofollow">' . $__templater->escape($__vars['title']) . '</a>
				</h3>
				<ul class="block-body">
					';
			if (!$__templater->test($__vars['threads'], 'empty', array())) {
				$__finalCompiled .= '
						';
				if ($__templater->isTraversable($__vars['threads'])) {
					foreach ($__vars['threads'] AS $__vars['thread']) {
						$__finalCompiled .= '
							<li class="block-row">
								' . $__templater->callMacro('thread_list_macros', 'item_new_posts', array(
							'thread' => $__vars['thread'],
						), $__vars) . '
							</li>
						';
					}
				}
				$__finalCompiled .= '
					';
			} else if ($__vars['filter'] == 'unread') {
				$__finalCompiled .= '
						<li class="block-row block-row--minor">
							' . 'You have no unread posts. You may <a href="' . $__templater->func('link', array('whats-new/posts', null, array('skip' => 1, ), ), true) . '" rel="nofollow">view all latest posts</a> instead.' . '
						</li>
					';
			} else {
				$__finalCompiled .= '
						<li class="block-row block-row--minor">
							' . 'No results found.' . '
						</li>
					';
			}
			$__finalCompiled .= '
				</ul>
			';
		}
		$__finalCompiled .= '
		</div>
	</div>
';
	}
	return $__finalCompiled;
});