<?php
// FROM HASH: f5c6c0496c6fd1eb7cf914ac810adc7a
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if (!$__templater->test($__vars['trophies'], 'empty', array())) {
		$__finalCompiled .= '
	<div class="block">
		<div class="block-container">
			<ol class="block-body">
				';
		if ($__templater->isTraversable($__vars['trophies'])) {
			foreach ($__vars['trophies'] AS $__vars['trophy']) {
				$__finalCompiled .= '
					<li class="block-row block-row--separated">
						<div class="contentRow">
							<span class="contentRow-figure contentRow-figure--text contentRow-figure--fixedSmall">' . $__templater->escape($__vars['trophy']['trophy_points']) . '</span>
							<div class="contentRow-main">
								<h2 class="contentRow-header">' . $__templater->escape($__vars['trophy']['title']) . '</h2>
								<div class="contentRow-minor">' . $__templater->filter($__vars['trophy']['description'], array(array('raw', array()),), true) . '</div>
							</div>
						</div>
					</li>
				';
			}
		}
		$__finalCompiled .= '
			</ol>
		</div>
	</div>
';
	} else {
		$__finalCompiled .= '
	<div class="blockMessage">' . 'No items have been created yet.' . '</div>
';
	}
	return $__finalCompiled;
});