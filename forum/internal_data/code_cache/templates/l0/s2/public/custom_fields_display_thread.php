<?php
// FROM HASH: 1954ab3e7b143f8f28b08d0ff62dd427
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ((($__vars['post']['position'] < 1) AND !$__templater->test($__vars['customFields'], 'empty', array()))) {
		$__finalCompiled .= '
	<div>
		';
		if ($__templater->isTraversable($__vars['customFields'])) {
			foreach ($__vars['customFields'] AS $__vars['fieldId'] => $__vars['field']) {
				$__finalCompiled .= '
			<dl class="pairs pairs--inline">
				<dt>' . $__templater->escape($__vars['field']['label']) . '</dt>
				<dd>' . $__templater->filter($__vars['field']['value'], array(array('raw', array()),), true) . '</dd>
			</dl>
		';
			}
		}
		$__finalCompiled .= '
	</div>
';
	}
	return $__finalCompiled;
});