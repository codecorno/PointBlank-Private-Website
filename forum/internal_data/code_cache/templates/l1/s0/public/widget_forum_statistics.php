<?php
// FROM HASH: 16bb69f825bdb818e2610ad80593ec6a
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<div class="block"' . $__templater->func('widget_data', array($__vars['widget'], ), true) . '>
	<div class="block-container">
		<h3 class="block-minorHeader">' . $__templater->escape($__vars['title']) . '</h3>
		<div class="block-body block-row">
			<dl class="pairs pairs--justified">
				<dt>' . 'Threads' . '</dt>
				<dd>' . $__templater->filter($__vars['forumStatistics']['threads'], array(array('number', array()),), true) . '</dd>
			</dl>

			<dl class="pairs pairs--justified">
				<dt>' . 'Messages' . '</dt>
				<dd>' . $__templater->filter($__vars['forumStatistics']['messages'], array(array('number', array()),), true) . '</dd>
			</dl>

			<dl class="pairs pairs--justified">
				<dt>' . 'Members' . '</dt>
				<dd>' . $__templater->filter($__vars['forumStatistics']['users'], array(array('number', array()),), true) . '</dd>
			</dl>

			<dl class="pairs pairs--justified">
				<dt>' . 'Latest member' . '</dt>
				<dd>' . $__templater->func('username_link', array($__vars['forumStatistics']['latestUser'], false, array(
	))) . '</dd>
			</dl>
		</div>
	</div>
</div>';
	return $__finalCompiled;
});