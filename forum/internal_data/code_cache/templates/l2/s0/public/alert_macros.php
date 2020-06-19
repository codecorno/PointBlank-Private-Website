<?php
// FROM HASH: 794f2f56e943e6a7d75ad97b3f62df1d
return array('macros' => array('row' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'alert' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	<div class="contentRow">
		<div class="contentRow-figure">
			' . $__templater->func('avatar', array($__vars['alert']['User'], 'xxs', false, array(
		'defaultname' => $__vars['alert']['username'],
	))) . '
		</div>
		<div class="contentRow-main contentRow-main--close">
			' . $__templater->filter($__templater->method($__vars['alert'], 'render', array()), array(array('raw', array()),), true) . '
			<div class="contentRow-minor contentRow-minor--smaller">
				' . $__templater->func('date_dynamic', array($__vars['alert']['event_date'], array(
	))) . '
			</div>
		</div>
	</div>
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';

	return $__finalCompiled;
});