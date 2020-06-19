<?php
// FROM HASH: 0add4982a5a6d291cb3c175517b93101
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<div class="contentRow">
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
</div>';
	return $__finalCompiled;
});