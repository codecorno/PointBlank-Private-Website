<?php
// FROM HASH: e82b4f736310fac911a4f65729b848ba
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->test($__vars['resultTypeSets'], 'empty', array())) {
		$__finalCompiled .= '
	<div class="menu-row">' . 'No results found.' . '</div>
';
	} else {
		$__finalCompiled .= '
	<div>
	';
		if ($__templater->isTraversable($__vars['resultTypeSets'])) {
			foreach ($__vars['resultTypeSets'] AS $__vars['resultType']) {
				$__finalCompiled .= '
		' . $__templater->escape($__templater->method($__vars['resultType'], 'render', array($__templater->func('templater', array(), false), ))) . '
	';
			}
		}
		$__finalCompiled .= '
	</div>
';
	}
	return $__finalCompiled;
});