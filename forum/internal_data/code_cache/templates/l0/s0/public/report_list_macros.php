<?php
// FROM HASH: 76f5656bd66f4625fdc11b1b1879cb7a
return array('macros' => array('item' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'report' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '

	';
	$__templater->includeCss('structured_list.less');
	$__finalCompiled .= '

	<div class="structItem structItem--report ' . (($__templater->method($__vars['report'], 'canView', array($__vars['report'], )) AND $__templater->method($__vars['report'], 'isAssignedTo', array())) ? 'is-highlighted' : '') . '">
		<div class="structItem-cell structItem-cell--icon">
			<div class="structItem-iconContainer">
				' . $__templater->func('avatar', array($__vars['report']['User'], 's', false, array(
	))) . '
			</div>
		</div>
		<div class="structItem-cell structItem-cell--main" data-xf-init="touch-proxy">
			<a href="' . $__templater->func('link', array('reports', $__vars['report'], ), true) . '" class="structItem-title" data-tp-primary="on">
				';
	if ($__templater->method($__vars['report'], 'canView', array($__vars['report'], ))) {
		$__finalCompiled .= '
					' . $__templater->escape($__vars['report']['title']) . '
				';
	} else {
		$__finalCompiled .= '
					' . 'Unknown content' . '
				';
	}
	$__finalCompiled .= '
			</a>

			<div class="structItem-minor">
				<ul class="structItem-extraInfo">
					<li>
						' . $__templater->escape($__templater->method($__vars['report'], 'getReportState', array())) . '
						';
	if ($__vars['report']['assigned_user_id'] AND $__templater->method($__vars['report'], 'canView', array($__vars['report'], ))) {
		$__finalCompiled .= '
							' . $__templater->filter($__vars['report']['AssignedUser']['username'], array(array('parens', array()),), true) . '
						';
	}
	$__finalCompiled .= '
					</li>
				</ul>

				<ul class="structItem-parts">
					<li>' . $__templater->func('username_link', array($__vars['report']['User'], false, array(
		'defaultname' => 'Unknown member',
	))) . '</li>
					<li class="structItem-startDate"><a href="' . $__templater->func('link', array('reports', $__vars['report'], ), true) . '" rel="nofollow">' . $__templater->func('date_dynamic', array($__vars['report']['first_report_date'], array(
	))) . '</a></li>
				</ul>
			</div>
		</div>
		<div class="structItem-cell structItem-cell--meta">
			<dl class="pairs pairs--justified">
				<dt>' . 'Reports' . '</dt>
				<dd>' . $__templater->filter($__vars['report']['report_count'], array(array('number', array()),), true) . '</dd>
			</dl>
			<dl class="pairs pairs--justified structItem-minor">
				<dt>' . 'Comments' . '</dt>
				<dd>' . $__templater->filter($__vars['report']['comment_count'], array(array('number', array()),), true) . '</dd>
			</dl>
		</div>
		<div class="structItem-cell structItem-cell--latest">
			<a href="' . $__templater->func('link', array('reports', $__vars['report'], ), true) . '" rel="nofollow">' . $__templater->func('date_dynamic', array($__vars['report']['last_modified_date'], array(
		'class' => 'structItem-latestDate',
	))) . '</a>
			<div class="structItem-minor">
				';
	if ($__templater->method($__vars['report'], 'canView', array($__vars['report'], ))) {
		$__finalCompiled .= '
					' . $__templater->func('username_link', array($__vars['report']['last_modified_cache'], false, array(
		))) . '
				';
	} else {
		$__finalCompiled .= '
					' . 'N/A' . '
				';
	}
	$__finalCompiled .= '
			</div>
		</div>
	</div>
';
	return $__finalCompiled;
},
'search_menu' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(), $__arguments, $__vars);
	$__finalCompiled .= '
	<div class="menu" data-menu="menu" aria-hidden="true">
		' . $__templater->form('
			<div class="menu-row">
				' . 'Find reports for member' . $__vars['xf']['language']['label_separator'] . '
				' . $__templater->formTextBox(array(
		'name' => 'username',
		'ac' => 'single',
		'maxlength' => $__templater->func('max_length', array($__vars['xf']['visitor'], 'username', ), false),
	)) . '
			</div>
			<div class="menu-footer">
				<span class="menu-footer-controls">' . $__templater->button('', array(
		'type' => 'submit',
		'class' => 'button--primary',
		'icon' => 'search',
	), '', array(
	)) . '</span>
			</div>
		', array(
		'action' => $__templater->func('link', array('reports/search', ), false),
		'class' => 'menu-content',
	)) . '
	</div>
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '

';
	return $__finalCompiled;
});