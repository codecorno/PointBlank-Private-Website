<?php
// FROM HASH: d0b6d378d124776c4f9d480718609a29
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__compilerTemp1 = '';
	$__compilerTemp1 .= '
					' . $__templater->callMacro('share_page_macros', 'buttons', array(
		'iconic' => $__vars['options']['iconic'],
	), $__vars) . '
				';
	if (strlen(trim($__compilerTemp1)) > 0) {
		$__finalCompiled .= '
	<div class="block"' . $__templater->func('widget_data', array($__vars['widget'], ), true) . '>
		<div class="block-container">
			<h3 class="block-minorHeader">' . $__templater->escape($__vars['title']) . '</h3>
			<div class="block-body block-row">
				' . $__compilerTemp1 . '
			</div>
		</div>
	</div>
';
	}
	return $__finalCompiled;
});