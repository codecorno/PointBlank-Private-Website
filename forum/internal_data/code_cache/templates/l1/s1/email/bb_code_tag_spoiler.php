<?php
// FROM HASH: 6bb509ec721583ff77376cbc5ef131e0
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<div class="spoilerPlaceholder">
	';
	if ($__vars['title']) {
		$__finalCompiled .= $__templater->escape($__vars['title']) . ' -';
	}
	$__finalCompiled .= '
	' . 'Spoiler content hidden.' . '
</div>';
	return $__finalCompiled;
});