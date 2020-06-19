<?php
// FROM HASH: b40c48b16c686c0c18f899f3ef65886e
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__vars['xf']['visitor']['user_id']) {
		$__finalCompiled .= '
	<div class="block"' . $__templater->func('widget_data', array($__vars['widget'], ), true) . '>
		<div class="block-container">
			<div class="block-body block-row">
				' . $__templater->callMacro('account_visitor_menu', 'visitor_panel_row', array(), $__vars) . '
			</div>
		</div>
	</div>
';
	}
	return $__finalCompiled;
});