<?php
// FROM HASH: b5b331cee73a4c0505b115d2e0745111
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->includeCss('member.less');
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	$__compilerTemp1 .= '
					';
	if ($__templater->isTraversable($__vars['results'])) {
		foreach ($__vars['results'] AS $__vars['userId'] => $__vars['data']) {
			$__compilerTemp1 .= '
						<li class="block-row">
							' . $__templater->callMacro('member_notable', 'overview_row', array(
				'data' => $__vars['data'],
			), $__vars) . '
						</li>
					';
		}
	}
	$__compilerTemp1 .= '
				';
	if (strlen(trim($__compilerTemp1)) > 0) {
		$__finalCompiled .= '
	<div class="block"' . $__templater->func('widget_data', array($__vars['widget'], ), true) . '>
		<div class="block-container">
			<h3 class="block-minorHeader">
				<a href="' . $__templater->func('link', array('members', null, array('key' => $__vars['memberStat']['member_stat_key'], ), ), true) . '">
					' . $__templater->escape($__vars['title']) . '
				</a>
			</h3>
			<ol class="block-body">
				' . $__compilerTemp1 . '
			</ol>
		</div>
	</div>
';
	}
	return $__finalCompiled;
});