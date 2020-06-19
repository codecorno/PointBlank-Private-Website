<?php
// FROM HASH: 9a0167ffe60b946fcbf033e66ca8ec9b
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if (!$__templater->test($__vars['users'], 'empty', array())) {
		$__finalCompiled .= '
	<div class="block"' . $__templater->func('widget_data', array($__vars['widget'], ), true) . '>
		<div class="block-container">
			<h3 class="block-minorHeader">' . $__templater->escape($__vars['title']) . '</h3>
			<div class="block-body block-row">
				<ul class="listHeap">
					';
		if ($__templater->isTraversable($__vars['users'])) {
			foreach ($__vars['users'] AS $__vars['user']) {
				$__finalCompiled .= '
						<li>
							' . $__templater->func('avatar', array($__vars['user'], 's', false, array(
					'img' => 'true',
				))) . '
						</li>
					';
			}
		}
		$__finalCompiled .= '
				</ul>
			</div>
		</div>
	</div>
';
	}
	return $__finalCompiled;
});