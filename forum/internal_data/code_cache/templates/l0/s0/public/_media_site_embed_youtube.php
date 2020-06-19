<?php
// FROM HASH: 401d4e2b51ca3b01c0747680e28078ba
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<div class="bbMediaWrapper">
	<div class="bbMediaWrapper-inner">
		<iframe src="https://www.youtube.com/embed/' . $__templater->escape($__vars['id']) . '?wmode=opaque&start=' . $__templater->escape($__vars['start']) . '"
				width="560" height="315"
				frameborder="0" allowfullscreen="true"></iframe>
	</div>
</div>';
	return $__finalCompiled;
});