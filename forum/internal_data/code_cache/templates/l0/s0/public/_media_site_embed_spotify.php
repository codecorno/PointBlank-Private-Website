<?php
// FROM HASH: 79bfd7511e2e1686fbbf49f0228374d4
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<div class="bbMediaWrapper">
	<div class="bbMediaWrapper-inner">
		<iframe src="https://open.spotify.com/embed?uri=spotify:' . $__templater->escape($__vars['id']) . '&theme=' . (($__templater->func('property', array('styleType', ), false) == 'light') ? 'white' : 'black') . '"
				width="500" height="282"
				frameborder="0" allowtransparency="false"></iframe>
	</div>
</div>';
	return $__finalCompiled;
});