<?php
// FROM HASH: c020b963a4b03c6668bd5837712c4d64
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if (!$__vars['options']['advanced_mode']) {
		$__finalCompiled .= '
	<div class="block">
		<div class="block-container">
			<h3 class="block-minorHeader">' . $__templater->escape($__vars['title']) . '</h3>
			<div class="block-body block-row">
				' . $__templater->filter($__vars['template'], array(array('raw', array()),), true) . '
			</div>
		</div>
	</div>
';
	} else {
		$__finalCompiled .= '
	' . $__templater->filter($__vars['template'], array(array('raw', array()),), true) . '
';
	}
	return $__finalCompiled;
});