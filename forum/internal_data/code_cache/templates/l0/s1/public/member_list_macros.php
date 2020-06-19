<?php
// FROM HASH: 479241a1c4c51a5a86d590840c47f8fc
return array('macros' => array('item' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'user' => '!',
		'extraData' => '',
		'extraDataBig' => false,
	), $__arguments, $__vars);
	$__finalCompiled .= '
	<div class="contentRow">
		<div class="contentRow-figure">
			' . $__templater->func('avatar', array($__vars['user'], 's', false, array(
		'notooltip' => 'true',
	))) . '
		</div>
		<div class="contentRow-main">
			';
	if ($__vars['extraData']) {
		$__finalCompiled .= '
				<div class="contentRow-extra ' . ($__vars['extraDataBig'] ? 'contentRow-extra--largest' : '') . '">' . $__templater->escape($__vars['extraData']) . '</div>
			';
	}
	$__finalCompiled .= '
			<h3 class="contentRow-header">' . $__templater->func('username_link', array($__vars['user'], true, array(
		'notooltip' => 'true',
	))) . '</h3>

			' . $__templater->func('user_blurb', array($__vars['user'], array(
		'class' => 'contentRow-lesser',
	))) . '

			<div class="contentRow-minor">
				<ul class="listInline listInline--bullet">
					' . '
					<li><dl class="pairs pairs--inline">
						<dt>' . 'Messages' . '</dt>
						<dd>' . $__templater->filter($__vars['user']['message_count'], array(array('number', array()),), true) . '</dd>
					</dl></li>
					' . '
					<li><dl class="pairs pairs--inline">
						<dt>' . 'Reaction score' . '</dt>
						<dd>' . $__templater->filter($__vars['user']['reaction_score'], array(array('number', array()),), true) . '</dd>
					</dl></li>
					' . '
					' . '
					';
	if ($__vars['xf']['options']['enableTrophies']) {
		$__finalCompiled .= '
						<li><dl class="pairs pairs--inline">
							<dt>' . 'Points' . '</dt>
							<dd>' . $__templater->filter($__vars['user']['trophy_points'], array(array('number', array()),), true) . '</dd>
						</dl></li>
					';
	}
	$__finalCompiled .= '
					' . '
				</ul>
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