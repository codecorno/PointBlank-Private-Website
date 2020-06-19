<?php
// FROM HASH: dca6068de0fbc1e7a2ebc8e03920c18e
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Oops! We ran into some problems.');
	$__finalCompiled .= '

<div class="blockMessage">
	';
	if ($__vars['error']) {
		$__finalCompiled .= '
		' . $__templater->filter($__vars['error'], array(array('raw', array()),), true) . '
	';
	} else {
		$__finalCompiled .= '
		<ul>
		';
		if ($__templater->isTraversable($__vars['errors'])) {
			foreach ($__vars['errors'] AS $__vars['error']) {
				$__finalCompiled .= '
			<li>' . $__templater->filter($__vars['error'], array(array('raw', array()),), true) . '</li>
		';
			}
		}
		$__finalCompiled .= '
		</ul>
	';
	}
	$__finalCompiled .= '
</div>';
	return $__finalCompiled;
});