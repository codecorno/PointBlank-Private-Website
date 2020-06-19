<?php
// FROM HASH: 27245f507e2edc253c245c01fd32f537
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<div class="bbMediaWrapper">
	<div class="bbMediaWrapper-inner">
		<iframe src="https://player.vimeo.com/video/' . $__templater->escape($__vars['id']) . ($__vars['start'] ? ('#t=' . $__templater->escape($__vars['start'])) : '') . '"
				width="560" height="315"
				frameborder="0" allowfullscreen="true"></iframe>
	</div>
</div>';
	return $__finalCompiled;
});