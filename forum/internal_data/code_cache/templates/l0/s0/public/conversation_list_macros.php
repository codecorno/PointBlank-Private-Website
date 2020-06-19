<?php
// FROM HASH: 08ff14cf38831f424f5bec31c1aebcc5
return array('macros' => array('item' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'userConv' => '!',
		'allowInlineMod' => true,
	), $__arguments, $__vars);
	$__finalCompiled .= '

	';
	$__templater->includeCss('structured_list.less');
	$__finalCompiled .= '

	<div class="structItem structItem--conversation ' . ($__templater->method($__vars['userConv'], 'isUnread', array()) ? 'is-unread' : '') . ' js-inlineModContainer" data-author="' . ($__templater->escape($__vars['userConv']['Master']['Starter']['username']) ?: $__templater->escape($__vars['userConv']['Master']['username'])) . '">
		<div class="structItem-cell structItem-cell--icon">
			<div class="structItem-iconContainer">
				' . $__templater->func('avatar', array($__vars['userConv']['Master']['Starter'], 's', false, array(
		'defaultname' => $__vars['userConv']['Master']['username'],
	))) . '
			</div>
		</div>
		<div class="structItem-cell structItem-cell--main" data-xf-init="touch-proxy">
			';
	$__compilerTemp1 = '';
	$__compilerTemp1 .= '
					';
	if ($__vars['userConv']['is_starred']) {
		$__compilerTemp1 .= '
						<li>
							<i class="structItem-status structItem-status--starred" aria-hidden="true" title="' . $__templater->filter('Starred', array(array('for_attr', array()),), true) . '"></i>
							<span class="u-srOnly">' . 'Starred' . '</span>
						</li>
					';
	}
	$__compilerTemp1 .= '
					';
	if (!$__vars['userConv']['Master']['conversation_open']) {
		$__compilerTemp1 .= '
						<li>
							<i class="structItem-status structItem-status--locked" aria-hidden="true" title="' . $__templater->filter('Locked', array(array('for_attr', array()),), true) . '"></i>
							<span class="u-srOnly">' . 'Locked' . '</span>
						</li>
					';
	}
	$__compilerTemp1 .= '
				';
	if (strlen(trim($__compilerTemp1)) > 0) {
		$__finalCompiled .= '
				<ul class="structItem-statuses">
				' . $__compilerTemp1 . '
				</ul>
			';
	}
	$__finalCompiled .= '

			<a href="' . $__templater->func('link', array('conversations/unread', $__vars['userConv'], ), true) . '" class="structItem-title" data-tp-primary="on">' . $__templater->escape($__vars['userConv']['title']) . '</a>

			<div class="structItem-minor">
				';
	$__compilerTemp2 = '';
	$__compilerTemp2 .= '
						';
	if ($__vars['allowInlineMod']) {
		$__compilerTemp2 .= '
							<li>' . $__templater->formCheckBox(array(
			'standalone' => 'true',
		), array(array(
			'value' => $__vars['userConv']['conversation_id'],
			'class' => 'js-inlineModToggle',
			'_type' => 'option',
		))) . '</li>
						';
	}
	$__compilerTemp2 .= '
					';
	if (strlen(trim($__compilerTemp2)) > 0) {
		$__finalCompiled .= '
					<ul class="structItem-extraInfo">
					' . $__compilerTemp2 . '
					</ul>
				';
	}
	$__finalCompiled .= '

				<ul class="structItem-parts">
					<li>
						<ul class="listInline listInline--comma listInline--selfInline">
							<li>' . $__templater->func('username_link', array($__vars['userConv']['Master']['Starter'], false, array(
		'defaultname' => $__vars['userConv']['Master']['username'],
		'title' => 'Conversation starter',
	))) . '</li>' . trim('
							');
	if ($__templater->isTraversable($__vars['userConv']['Master']['recipients'])) {
		foreach ($__vars['userConv']['Master']['recipients'] AS $__vars['recipient']) {
			if ($__vars['recipient']['user_id'] != $__vars['userConv']['Master']['user_id']) {
				$__finalCompiled .= trim('
								<li>' . $__templater->func('username_link', array($__vars['recipient'], false, array(
					'defaultname' => 'Unknown member',
				))) . '</li>
							');
			}
		}
	}
	$__finalCompiled .= '
						</ul>
					</li>
					<li class="structItem-startDate"><a href="' . $__templater->func('link', array('conversations', $__vars['userConv'], ), true) . '" rel="nofollow">' . $__templater->func('date_dynamic', array($__vars['userConv']['Master']['start_date'], array(
	))) . '</a></li>
				</ul>

				';
	if ($__vars['userConv']['reply_count'] >= $__vars['xf']['options']['messagesPerPage']) {
		$__finalCompiled .= '
					<span class="structItem-pageJump">
					';
		$__compilerTemp3 = $__templater->func('last_pages', array($__vars['userConv']['reply_count'] + 1, $__vars['xf']['options']['messagesPerPage'], $__vars['xf']['options']['lastPageLinks'], ), false);
		if ($__templater->isTraversable($__compilerTemp3)) {
			foreach ($__compilerTemp3 AS $__vars['p']) {
				$__finalCompiled .= '
						<a href="' . $__templater->func('link', array('conversations', $__vars['userConv'], array('page' => $__vars['p'], ), ), true) . '">' . $__templater->escape($__vars['p']) . '</a>
					';
			}
		}
		$__finalCompiled .= '
					</span>
				';
	}
	$__finalCompiled .= '
			</div>
		</div>
		<div class="structItem-cell structItem-cell--meta">
			<dl class="pairs pairs--justified">
				<dt>' . 'Replies' . '</dt>
				<dd>' . $__templater->filter($__vars['userConv']['reply_count'], array(array('number', array()),), true) . '</dd>
			</dl>
			<dl class="pairs pairs--justified structItem-minor">
				<dt>' . 'Participants' . '</dt>
				<dd>' . $__templater->filter($__vars['userConv']['Master']['recipient_count'], array(array('number', array()),), true) . '</dd>
			</dl>
		</div>
		<div class="structItem-cell structItem-cell--latest">
			<a href="' . $__templater->func('link', array('conversations/latest', $__vars['userConv'], ), true) . '" rel="nofollow">' . $__templater->func('date_dynamic', array($__vars['userConv']['last_message_date'], array(
		'class' => 'structItem-latestDate',
	))) . '</a>
			<div class="structItem-minor">' . $__templater->func('username_link', array($__vars['userConv']['Master']['last_message_cache'], false, array(
	))) . '</div>
		</div>
	</div>
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';

	return $__finalCompiled;
});