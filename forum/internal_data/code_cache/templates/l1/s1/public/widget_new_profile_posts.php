<?php
// FROM HASH: 5dc96f42480e3c1874282ed2fea2e50f
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if (!$__templater->test($__vars['profilePosts'], 'empty', array())) {
		$__finalCompiled .= '
	<div class="block"' . $__templater->func('widget_data', array($__vars['widget'], ), true) . '>
		<div class="block-container">
			';
		if ($__vars['style'] == 'full') {
			$__finalCompiled .= '
				<h3 class="block-header">
					<a href="' . $__templater->escape($__vars['link']) . '" rel="nofollow">' . $__templater->escape($__vars['title']) . '</a>
				</h3>
			';
		} else {
			$__finalCompiled .= '
				<h3 class="block-minorHeader">
					<a href="' . $__templater->escape($__vars['link']) . '" rel="nofollow">' . $__templater->escape($__vars['title']) . '</a>
				</h3>
			';
		}
		$__finalCompiled .= '
			<div class="block-body js-replyNewMessageContainer">
				';
		if ($__templater->method($__vars['xf']['visitor'], 'canPostOnProfile', array())) {
			$__finalCompiled .= '
					';
			$__vars['firstProfilePost'] = $__templater->filter($__vars['profilePosts'], array(array('first', array()),), false);
			$__finalCompiled .= '
					' . $__templater->callMacro('profile_post_macros', 'submit', array(
				'user' => $__vars['xf']['visitor'],
				'lastDate' => ($__vars['firstProfilePost']['post_date'] ?: 0),
				'containerSelector' => '< .js-replyNewMessageContainer',
				'style' => $__vars['style'],
				'context' => 'all',
			), $__vars) . '
				';
		}
		$__finalCompiled .= '
				';
		if ($__templater->isTraversable($__vars['profilePosts'])) {
			foreach ($__vars['profilePosts'] AS $__vars['profilePost']) {
				$__finalCompiled .= '
					';
				if ($__vars['style'] == 'full') {
					$__finalCompiled .= '
						' . $__templater->callMacro('profile_post_macros', 'profile_post', array(
						'profilePost' => $__vars['profilePost'],
						'showTargetUser' => true,
						'allowInlineMod' => false,
					), $__vars) . '
					';
				} else {
					$__finalCompiled .= '
						<div class="block-row">
							' . $__templater->callMacro('profile_post_macros', 'profile_post_simple', array(
						'profilePost' => $__vars['profilePost'],
						'limitHeight' => true,
					), $__vars) . '
						</div>
					';
				}
				$__finalCompiled .= '
				';
			}
		}
		$__finalCompiled .= '
			</div>
			';
		if ($__vars['style'] == 'full') {
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
		</div>
	</div>
';
	}
	return $__finalCompiled;
});