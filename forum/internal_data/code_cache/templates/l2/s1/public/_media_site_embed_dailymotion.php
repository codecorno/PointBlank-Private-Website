<?php
// FROM HASH: 7a56e47b9dae45386bece232016dd6ce
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<div class="bbMediaWrapper">
	<div class="bbMediaWrapper-inner">
		<iframe src="https://www.dailymotion.com/embed/video/' . $__templater->escape($__vars['id']) . '?start=' . $__templater->escape($__vars['start']) . '&width=560&hideInfos=1"
				width="560" height="315"
				allowfullscreen
				frameborder="0"></iframe>
	</div>
</div>';
	return $__finalCompiled;
});