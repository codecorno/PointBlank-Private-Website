<?php
// FROM HASH: f54126607f6a62b0fbd18b927b430632
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Email list');
	$__finalCompiled .= '

<div class="block">
	<div class="block-container">
		<div class="block-body block-row">
			<pre style="max-height: 400px; overflow: auto;">';
	if ($__templater->isTraversable($__vars['users'])) {
		foreach ($__vars['users'] AS $__vars['user']) {
			if ($__vars['user']['email']) {
				$__finalCompiled .= $__templater->escape($__vars['user']['email']) . '	' . $__templater->escape($__vars['user']['username']) . '
';
			}
		}
	}
	$__finalCompiled .= '</pre>
		</div>
	</div>
</div>';
	return $__finalCompiled;
});