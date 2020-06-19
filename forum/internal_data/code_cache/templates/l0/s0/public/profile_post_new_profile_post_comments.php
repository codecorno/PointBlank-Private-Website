<?php
// FROM HASH: 06e32e437d0055132a096a5ed73162a0
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->isTraversable($__vars['profilePostComments'])) {
		foreach ($__vars['profilePostComments'] AS $__vars['profilePostComment']) {
			$__finalCompiled .= '
	' . $__templater->callMacro('profile_post_macros', 'comment', array(
				'profilePost' => $__vars['profilePost'],
				'comment' => $__vars['profilePostComment'],
			), $__vars) . '
';
		}
	}
	return $__finalCompiled;
});