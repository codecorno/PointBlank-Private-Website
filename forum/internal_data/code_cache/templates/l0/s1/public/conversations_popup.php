<?php
// FROM HASH: 8b84958f115dedc4b91143272203436f
return array('macros' => array('popup_item' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'userConv' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	<li class="menu-row menu-row--separated menu-row--clickable ' . ($__templater->method($__vars['userConv'], 'isUnread', array()) ? 'menu-row--highlighted' : '') . '">
		<div class="fauxBlockLink">
			<div class="contentRow">
				<div class="contentRow-figure">
					' . $__templater->func('avatar', array($__vars['userConv']['Master']['LastMessageUser'], 'xxs', false, array(
		'defaultname' => $__vars['userConv']['Master']['last_message_username'],
	))) . '
				</div>
				<div class="contentRow-main contentRow-main--close">
					<a href="' . $__templater->func('link', array('conversations/unread', $__vars['userConv'], ), true) . '" class="fauxBlockLink-blockLink">' . $__templater->escape($__vars['userConv']['Master']['title']) . '</a>
					<div class="contentRow-minor contentRow-minor--smaller contentRow-minor--hideLinks">
						' . 'With' . $__vars['xf']['language']['label_separator'] . '
						<ul class="listInline listInline--selfInline listInline--comma">
							<li>' . $__templater->func('username_link', array($__vars['userConv']['Master']['Starter'], false, array(
		'defaultname' => $__vars['userConv']['Master']['username'],
		'title' => 'Conversation starter',
		'href' => '',
	))) . '</li>' . trim('
							');
	if ($__templater->isTraversable($__vars['userConv']['Master']['recipients'])) {
		foreach ($__vars['userConv']['Master']['recipients'] AS $__vars['recipient']) {
			if ($__vars['recipient']['user_id'] != $__vars['userConv']['Master']['user_id']) {
				$__finalCompiled .= trim('
								<li>' . $__templater->func('username_link', array($__vars['recipient'], false, array(
					'defaultname' => 'Unknown member',
					'href' => '',
				))) . '</li>
							');
			}
		}
	}
	$__finalCompiled .= '
						</ul>
					</div>
					<div class="contentRow-minor contentRow-minor--smaller">
						' . $__templater->func('date_dynamic', array($__vars['userConv']['Master']['last_message_date'], array(
	))) . '
					</div>
				</div>
			</div>
		</div>
	</li>
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if (!$__templater->test($__vars['unreadConversations'], 'empty', array()) OR !$__templater->test($__vars['readConversations'], 'empty', array())) {
		$__finalCompiled .= '
	<div class="menu-scroller">
		<ol class="listPlain">
			';
		if ($__templater->isTraversable($__vars['unreadConversations'])) {
			foreach ($__vars['unreadConversations'] AS $__vars['userConv']) {
				$__finalCompiled .= '
				' . $__templater->callMacro(null, 'popup_item', array(
					'userConv' => $__vars['userConv'],
				), $__vars) . '
			';
			}
		}
		$__finalCompiled .= '
			';
		if ($__templater->isTraversable($__vars['readConversations'])) {
			foreach ($__vars['readConversations'] AS $__vars['userConv']) {
				$__finalCompiled .= '
				' . $__templater->callMacro(null, 'popup_item', array(
					'userConv' => $__vars['userConv'],
				), $__vars) . '
			';
			}
		}
		$__finalCompiled .= '
		</ol>
	</div>
';
	} else {
		$__finalCompiled .= '
	<div class="menu-row">' . 'You have no recent conversations.' . '</div>
';
	}
	$__finalCompiled .= '

';
	return $__finalCompiled;
});