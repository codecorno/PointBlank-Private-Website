<?php
// FROM HASH: ebe6e70c488577386f04fa79efd90aa0
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
	<div class="block">
	' . $__templater->filter($__vars['template'], array(array('raw', array()),), true) . '
	</div>
';
	}
	return $__finalCompiled;
});