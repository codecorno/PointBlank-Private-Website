<?php
// FROM HASH: fcbcf7b7fd754ea5ad734cbd45d5b45e
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__vars['firstUnshownProfilePost']) {
		$__finalCompiled .= '
	<div class="message message--simple">
		<div class="message-inner">
			<div class="message-cell message-cell--alert">
				' . 'There are more posts to display.' . ' <a href="' . $__templater->func('link', array('profile-posts', $__vars['firstUnshownProfilePost'], ), true) . '">' . 'View them?' . '</a>
			</div>
		</div>
	</div>
';
	}
	$__finalCompiled .= '

';
	if ($__templater->isTraversable($__vars['profilePosts'])) {
		foreach ($__vars['profilePosts'] AS $__vars['profilePost']) {
			$__finalCompiled .= '
	';
			if ($__vars['style'] == 'simple') {
				$__finalCompiled .= '
		<div class="block-row">
			' . $__templater->callMacro('profile_post_macros', 'profile_post_simple', array(
					'profilePost' => $__vars['profilePost'],
				), $__vars) . '
		</div>
	';
			} else {
				$__finalCompiled .= '
		' . $__templater->callMacro('profile_post_macros', 'profile_post', array(
					'profilePost' => $__vars['profilePost'],
				), $__vars) . '
	';
			}
			$__finalCompiled .= '
';
		}
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
});