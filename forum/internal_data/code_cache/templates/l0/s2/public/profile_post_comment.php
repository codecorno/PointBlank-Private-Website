<?php
// FROM HASH: 7f27ad1425396e201765f459966f8599
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->setPageParam('head.' . 'metaNoindex', $__templater->preEscaped('<meta name="robots" content="noindex" />'));
	$__finalCompiled .= '

<div class="block">
	<div class="block-container">
		<div class="block-body">
			' . $__templater->callMacro('profile_post_macros', 'comment', array(
		'comment' => $__vars['comment'],
		'profilePost' => $__vars['profilePost'],
	), $__vars) . '
		</div>
	</div>
</div>';
	return $__finalCompiled;
});