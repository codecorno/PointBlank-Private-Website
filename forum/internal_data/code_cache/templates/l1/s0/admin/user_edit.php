<?php
// FROM HASH: 2e5f0e6860db781c7bdab4f04b8a95f1
return array('macros' => array('privacy_select' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'name' => '!',
		'label' => '!',
		'user' => '!',
		'hideEveryone' => false,
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	$__compilerTemp1 = array(array(
		'value' => 'none',
		'label' => 'Nobody',
		'_type' => 'option',
	));
	if (!$__vars['hideEveryone']) {
		$__compilerTemp1[] = array(
			'value' => 'everyone',
			'label' => 'All visitors',
			'_type' => 'option',
		);
	}
	$__compilerTemp1[] = array(
		'value' => 'members',
		'label' => 'Members only',
		'_type' => 'option',
	);
	$__compilerTemp1[] = array(
		'value' => 'followed',
		'label' => 'People ' . ($__vars['user']['username'] ? $__templater->escape($__vars['user']['username']) : (('[' . 'User') . ']')) . ' follows',
		'_type' => 'option',
	);
	$__finalCompiled .= $__templater->formSelectRow(array(
		'name' => 'privacy[' . $__vars['name'] . ']',
		'value' => $__vars['user']['Privacy'][$__vars['name']],
	), $__compilerTemp1, array(
		'label' => $__templater->escape($__vars['label']),
	)) . '
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->method($__vars['user'], 'isInsert', array())) {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Add user');
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Edit user' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['user']['username']));
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	if ($__templater->method($__vars['user'], 'isUpdate', array())) {
		$__compilerTemp1 = '';
		if ($__vars['user']['is_banned']) {
			$__compilerTemp1 .= '
					<a href="' . $__templater->func('link', array('banning/users/lift', $__vars['user'], ), true) . '" class="menu-linkRow" data-xf-click="overlay">' . 'Lift ban' . '</a>
				';
		} else if ((!$__vars['user']['is_moderator']) AND (!$__vars['user']['is_admin'])) {
			$__compilerTemp1 .= '
					<a href="' . $__templater->func('link', array('banning/users/add', $__vars['user'], ), true) . '" class="menu-linkRow" data-xf-click="overlay">' . 'Ban user' . '</a>
				';
		}
		$__compilerTemp2 = '';
		if ((!$__vars['user']['is_moderator']) AND (!$__vars['user']['is_admin'])) {
			$__compilerTemp2 .= '
					<a href="' . $__templater->func('link', array('users/merge', $__vars['user'], ), true) . '" class="menu-linkRow" data-xf-click="overlay">' . 'Merge with user' . '</a>
					<a href="' . $__templater->func('link', array('users/delete-conversations', $__vars['user'], ), true) . '" class="menu-linkRow" data-xf-click="overlay">' . 'Delete conversations' . '</a>
				';
		}
		$__compilerTemp3 = '';
		if ((!$__vars['user']['is_super_admin']) AND $__vars['xf']['options']['editHistory']['enabled']) {
			$__compilerTemp3 .= '
					<a href="' . $__templater->func('link', array('users/revert-message-edit', $__vars['user'], ), true) . '" class="menu-linkRow" data-xf-click="overlay">' . 'Revert message edits' . '</a>
				';
		}
		$__compilerTemp4 = '';
		if (!$__vars['user']['is_super_admin']) {
			$__compilerTemp4 .= '
					<a href="' . $__templater->func('link', array('users/remove-reactions', $__vars['user'], ), true) . '" class="menu-linkRow" data-xf-click="overlay">' . 'Remove reactions' . '</a>
					<a href="' . $__templater->func('link', array('users/manage-watched-threads', $__vars['user'], ), true) . '" class="menu-linkRow" data-xf-click="overlay">' . 'Manage watched threads' . '</a>
				';
		}
		$__compilerTemp5 = '';
		if ($__templater->method($__vars['user'], 'isAwaitingEmailConfirmation', array())) {
			$__compilerTemp5 .= '
					<a href="' . $__templater->func('link', array('users/resend-confirmation', $__vars['user'], ), true) . '" class="menu-linkRow" data-xf-click="overlay">' . 'Resend account confirmation' . '</a>
				';
		}
		$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	<div>
		' . $__templater->button('', array(
			'href' => $__templater->func('link', array('users/delete', $__vars['user'], ), false),
			'icon' => 'delete',
			'overlay' => 'true',
		), '', array(
		)) . '

		' . $__templater->button('Actions', array(
			'class' => 'menuTrigger',
			'data-xf-click' => 'menu',
			'aria-expanded' => 'false',
			'aria-haspopup' => 'true',
		), '', array(
		)) . '
		<div class="menu" data-menu="menu" aria-hidden="true">
			<div class="menu-content">
				<h3 class="menu-header">' . 'Actions' . '</h3>
				' . '
				<a href="' . $__templater->func('link_type', array('public', 'members', $__vars['user'], ), true) . '" class="menu-linkRow" target="_blank">' . 'View public profile' . '</a>

				' . $__compilerTemp1 . '

				' . $__compilerTemp2 . '

				' . $__compilerTemp3 . '

				' . $__compilerTemp4 . '

				' . $__compilerTemp5 . '
				' . '
			</div>
		</div>
	</div>
');
	}
	$__finalCompiled .= '

';
	if ($__vars['success']) {
		$__finalCompiled .= '
	<div class="blockMessage blockMessage--success blockMessage--iconic">' . 'Your changes have been saved.' . '</div>
';
	}
	$__finalCompiled .= '

<div class="block">
	';
	if ($__vars['user']['user_id']) {
		$__finalCompiled .= '
	';
		$__compilerTemp6 = '';
		$__compilerTemp6 .= '
				' . '
				';
		if ($__vars['user']['is_admin']) {
			$__compilerTemp6 .= '
					<li><a href="' . $__templater->func('link', array('admins/edit', $__vars['user'], ), true) . '">' . ($__vars['user']['is_super_admin'] ? 'Super administrator' : 'Administrator') . '</a></li>
				';
		}
		$__compilerTemp6 .= '
				';
		if ($__vars['user']['is_moderator']) {
			$__compilerTemp6 .= '
					<li><a href="' . $__templater->func('link', array('moderators', ), true) . '">' . 'Moderator' . '</a></li>
				';
		}
		$__compilerTemp6 .= '
				';
		if ($__vars['user']['Option']['is_discouraged']) {
			$__compilerTemp6 .= '
					<li>' . 'Discouraged' . '</li>
				';
		}
		$__compilerTemp6 .= '
				';
		if ($__vars['user']['is_banned']) {
			$__compilerTemp6 .= '
					<li><a href="' . $__templater->func('link', array('banning/users/lift', $__vars['user'], ), true) . '" data-xf-click="overlay">' . 'Banned' . '</a></li>
				';
		}
		$__compilerTemp6 .= '
				' . '
			';
		if (strlen(trim($__compilerTemp6)) > 0) {
			$__finalCompiled .= '
		<div class="block-outer">
			<ul class="listInline listInline--bullet">
			' . $__compilerTemp6 . '
			</ul>
		</div>
	';
		}
		$__finalCompiled .= '
	';
	}
	$__finalCompiled .= '

	';
	$__compilerTemp7 = '';
	if ($__vars['user']['is_super_admin']) {
		$__compilerTemp7 .= '
			<div class="block-body">
				' . $__templater->formPasswordBoxRow(array(
			'name' => 'visitor_password',
		), array(
			'label' => 'Your password',
			'explain' => 'You must enter your current password to validate this request.',
		)) . '
			</div>
		';
	}
	$__compilerTemp8 = '';
	if ($__vars['user']['user_id']) {
		$__compilerTemp8 .= '
					<a class="tabs-tab" role="tab" tabindex="0"
						id="user-extras"
						aria-controls="user-extras"
						href="' . $__templater->func('link', array('users/edit', $__vars['user'], ), true) . '#user-extras">' . 'Extra' . '</a>
					<a class="tabs-tab" role="tab" tabindex="0"
						id="user-ips"
						aria-controls="user-ips"
						href="' . $__templater->func('link', array('users/edit', $__vars['user'], ), true) . '#user-ips">' . 'IP addresses' . '</a>
					<a class="tabs-tab" role="tab" tabindex="0"
						id="user-changes"
						aria-controls="user-changes"
						href="' . $__templater->func('link', array('users/edit', $__vars['user'], ), true) . '#user-changes">' . 'Change log' . '</a>
				';
	}
	$__compilerTemp9 = '';
	if ($__templater->method($__vars['user'], 'exists', array())) {
		$__compilerTemp9 .= '
						' . $__templater->formRadioRow(array(
			'name' => 'change_password',
		), array(array(
			'value' => '',
			'checked' => 'checked',
			'label' => 'Do not change',
			'_type' => 'option',
		),
		array(
			'value' => 'generate',
			'label' => 'Send password reset',
			'hint' => 'A password reset confirmation will be emailed to the user and they won\'t be able to log in until they set a new password.',
			'_type' => 'option',
		),
		array(
			'value' => 'change',
			'label' => 'Set new password' . $__vars['xf']['language']['label_separator'],
			'_dependent' => array($__templater->formTextBox(array(
			'name' => 'password',
			'autocomplete' => 'off',
		))),
			'_type' => 'option',
		)), array(
			'label' => 'Password',
		)) . '
					';
	} else {
		$__compilerTemp9 .= '
						' . $__templater->formTextBoxRow(array(
			'name' => 'password',
			'autocomplete' => 'off',
		), array(
			'label' => 'Password',
		)) . '
					';
	}
	$__compilerTemp10 = '';
	if ($__vars['user']['user_id']) {
		$__compilerTemp10 .= '
						';
		$__compilerTemp11 = '';
		if ($__vars['user']['Option']['use_tfa']) {
			$__compilerTemp11 .= '
								<ul class="inputChoices">
									<li class="inputChoices-choice inputChoices-plainChoice">' . 'Enabled' . '</li>
									<li class="inputChoices-choice">' . $__templater->formCheckBox(array(
				'standalone' => 'true',
			), array(array(
				'name' => 'disable_tfa',
				'label' => 'Disable two-step verification',
				'_type' => 'option',
			))) . '</li>
								</ul>
							';
		} else {
			$__compilerTemp11 .= '
								' . 'Disabled' . '
							';
		}
		$__compilerTemp10 .= $__templater->formRow('
							' . $__compilerTemp11 . '
						', array(
			'label' => 'Two-step verification',
		)) . '

						' . $__templater->formRow('
							' . $__templater->func('avatar', array($__vars['user'], 's', false, array(
			'href' => $__templater->func('link', array('users/avatar', $__vars['user'], ), false),
			'data-xf-click' => 'overlay',
		))) . '
							<a href="' . $__templater->func('link', array('users/avatar', $__vars['user'], ), true) . '" data-xf-click="overlay">' . 'Edit avatar' . '</a>
						', array(
			'label' => 'Avatar',
		)) . '
						' . $__templater->formRow('
							' . $__templater->func('date_dynamic', array($__vars['user']['register_date'], array(
		))) . '
						', array(
			'label' => 'Joined',
		)) . '
						';
		if ($__vars['user']['last_activity']) {
			$__compilerTemp10 .= '
							' . $__templater->formRow('
								' . $__templater->func('date_dynamic', array($__vars['user']['last_activity'], array(
			))) . '
							', array(
				'label' => 'Last activity',
			)) . '
						';
		}
		$__compilerTemp10 .= '
					';
	}
	$__compilerTemp12 = '';
	if ($__vars['user']['user_id']) {
		$__compilerTemp12 .= '
							';
		if (!$__vars['user']['is_moderator']) {
			$__compilerTemp12 .= '<a href="' . $__templater->func('link', array('moderators', ), true) . '">' . 'Make this user a moderator' . '</a>';
		}
		$__compilerTemp12 .= '
							';
		if ((!$__vars['user']['is_admin']) AND (!$__vars['user']['is_moderator'])) {
			$__compilerTemp12 .= '/';
		}
		$__compilerTemp12 .= '
							';
		if (!$__vars['user']['is_admin']) {
			$__compilerTemp12 .= '<a href="' . $__templater->func('link', array('admins', ), true) . '">' . 'Make this user an administrator' . '</a>';
		}
		$__compilerTemp12 .= '
						';
	}
	$__vars['_userChangesHtml'] = $__templater->preEscaped('
						' . $__compilerTemp12 . '
					');
	$__compilerTemp13 = $__templater->mergeChoiceOptions(array(), $__vars['userGroups']);
	$__compilerTemp14 = $__templater->mergeChoiceOptions(array(), $__vars['userGroups']);
	$__compilerTemp15 = array(array(
		'value' => '0',
		'label' => $__vars['xf']['language']['parenthesis_open'] . 'Use default style' . $__vars['xf']['language']['parenthesis_close'],
		'_type' => 'option',
	));
	$__compilerTemp16 = $__templater->method($__vars['styleTree'], 'getFlattened', array(0, ));
	if ($__templater->isTraversable($__compilerTemp16)) {
		foreach ($__compilerTemp16 AS $__vars['treeEntry']) {
			$__compilerTemp15[] = array(
				'value' => $__vars['treeEntry']['record']['style_id'],
				'label' => $__templater->func('repeat', array('--', $__vars['treeEntry']['depth'], ), true) . ' ' . $__templater->escape($__vars['treeEntry']['record']['title']),
				'_type' => 'option',
			);
		}
	}
	$__compilerTemp17 = array();
	$__compilerTemp18 = $__templater->method($__vars['languageTree'], 'getFlattened', array(0, ));
	if ($__templater->isTraversable($__compilerTemp18)) {
		foreach ($__compilerTemp18 AS $__vars['treeEntry']) {
			$__compilerTemp17[] = array(
				'value' => $__vars['treeEntry']['record']['language_id'],
				'label' => $__templater->func('repeat', array('--', $__vars['treeEntry']['depth'], ), true) . '
								' . $__templater->escape($__vars['treeEntry']['record']['title']),
				'_type' => 'option',
			);
		}
	}
	$__compilerTemp19 = $__templater->mergeChoiceOptions(array(), $__vars['timeZones']);
	$__compilerTemp20 = '';
	if ($__vars['user']['user_id']) {
		$__compilerTemp20 .= '
				<li data-href="' . $__templater->func('link', array('users/extra', $__vars['user'], ), true) . '" role="tabpanel" aria-labelledby="user-extras">
					<div class="block-body block-row">' . 'Loading' . $__vars['xf']['language']['ellipsis'] . '</div>
				</li>
			';
	}
	$__compilerTemp21 = '';
	if ($__vars['user']['user_id']) {
		$__compilerTemp21 .= '
				<li data-href="' . $__templater->func('link', array('users/user-ips', $__vars['user'], ), true) . '" role="tabpanel" aria-labelledby="user-ips">
					<div class="block-body block-row">' . 'Loading' . $__vars['xf']['language']['ellipsis'] . '</div>
				</li>
			';
	}
	$__compilerTemp22 = '';
	if ($__vars['user']['user_id']) {
		$__compilerTemp22 .= '
				<li data-href="' . $__templater->func('link', array('users/change-log', $__vars['user'], ), true) . '" role="tabpanel" aria-labelledby="user-changes">
					<div class="block-body block-row">' . 'Loading' . $__vars['xf']['language']['ellipsis'] . '</div>
				</li>
			';
	}
	$__finalCompiled .= $__templater->form('
		' . $__compilerTemp7 . '

		<h2 class="block-tabHeader tabs hScroller" data-xf-init="tabs h-scroller" data-state="replace" role="tablist">
			<span class="hScroller-scroll">
				' . '
				<a class="tabs-tab is-active" role="tab" tabindex="0"
					id="user-details"
					aria-controls="user-details"
					href="' . $__templater->func('link', array('users/edit', $__vars['user'], ), true) . '#user-details">' . 'User details' . '</a>
				' . $__compilerTemp8 . '
				' . '
			</span>
		</h2>

		<ul class="tabPanes">
			' . '
			<li class="is-active" role="tabpanel" aria-labelledby="user-details">
				<div class="block-body">
					' . $__templater->formTextBoxRow(array(
		'name' => 'user[username]',
		'value' => $__vars['user']['username'],
		'maxlength' => ($__vars['xf']['options']['usernameLength']['max'] ?: $__templater->func('max_length', array($__vars['user'], 'username', ), false)),
	), array(
		'label' => 'User name',
	)) . '

					' . $__templater->formTextBoxRow(array(
		'name' => 'user[email]',
		'value' => $__vars['user']['email'],
		'type' => 'email',
		'dir' => 'ltr',
		'maxlength' => $__templater->func('max_length', array($__vars['user'], 'email', ), false),
	), array(
		'label' => 'Email',
	)) . '

					' . $__compilerTemp9 . '

					' . $__compilerTemp10 . '

					<hr class="formRowSep" />

					' . '' . '

					' . $__templater->formSelectRow(array(
		'name' => 'user[user_group_id]',
		'value' => $__vars['user']['user_group_id'],
	), $__compilerTemp13, array(
		'label' => 'User group',
		'explain' => $__templater->filter($__vars['_userChangesHtml'], array(array('raw', array()),), true),
	)) . '

					' . $__templater->formCheckBoxRow(array(
		'name' => 'user[secondary_group_ids]',
		'value' => $__vars['user']['secondary_group_ids'],
		'listclass' => 'listColumns',
	), $__compilerTemp14, array(
		'label' => 'Secondary user groups',
	)) . '

					' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'user[is_staff]',
		'selected' => $__vars['user']['is_staff'],
		'label' => 'Display user as staff',
		'hint' => 'If selected, this user will be listed publicly as a staff member.',
		'_type' => 'option',
	)), array(
	)) . '

					' . $__templater->formSelectRow(array(
		'name' => 'user[user_state]',
		'value' => $__vars['user']['user_state'],
	), array(array(
		'value' => 'valid',
		'label' => 'Valid',
		'_type' => 'option',
	),
	array(
		'value' => 'email_confirm',
		'label' => 'Awaiting email confirmation',
		'_type' => 'option',
	),
	array(
		'value' => 'email_confirm_edit',
		'label' => 'Awaiting email confirmation (from edit)',
		'_type' => 'option',
	),
	array(
		'value' => 'email_bounce',
		'label' => 'Email invalid (bounced)',
		'_type' => 'option',
	),
	array(
		'value' => 'moderated',
		'label' => 'Awaiting approval',
		'_type' => 'option',
	),
	array(
		'value' => 'rejected',
		'label' => 'Rejected',
		'_type' => 'option',
	),
	array(
		'value' => 'disabled',
		'label' => 'Disabled',
		'_type' => 'option',
	)), array(
		'label' => 'User state',
		'explain' => '
							' . 'When in a user state other than \'' . 'Valid' . '\', users will receive permissions from the ' . (((('<a href="' . $__templater->func('link', array('user-groups/edit', array('user_group_id' => 1, 'title' => $__vars['userGroups']['1'], ), ), true)) . '" target="_blank">') . $__templater->escape($__vars['userGroups']['1'])) . '</a>') . ' group.' . '
						',
	)) . '

					<hr class="formRowSep" />

					' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'option[is_discouraged]',
		'selected' => $__vars['user']['Option']['is_discouraged'],
		'hint' => 'Discouraged users are subjected to annoying random delays and failures in system behavior, designed to \'encourage\' them to go away and troll some other site.',
		'label' => 'Discouraged',
		'_type' => 'option',
	)), array(
		'explain' => '<a href="' . $__templater->func('link', array('banning/discouraged-ips', ), true) . '">' . 'Alternatively, you may use IP-based discouragement.' . '</a>',
	)) . '
				</div>

				<h3 class="block-formSectionHeader">
					<span class="collapseTrigger collapseTrigger--block" data-xf-click="toggle" data-target="< :up:next">
						<span class="block-formSectionHeader-aligner">' . 'Personal details' . '</span>
					</span>
				</h3>
				<div class="block-body block-body--collapsible">
					' . $__templater->callMacro('public:helper_user_dob_edit', 'dob_edit', array(
		'dobData' => $__vars['user']['Profile'],
	), $__vars) . '

					<hr class="formRowSep" />

					' . $__templater->formTextBoxRow(array(
		'name' => 'profile[location]',
		'value' => $__vars['user']['Profile']['location_'],
	), array(
		'label' => 'Location',
	)) . '
					' . $__templater->formTextBoxRow(array(
		'name' => 'profile[website]',
		'value' => $__vars['user']['Profile']['website_'],
		'type' => 'url',
		'dir' => 'ltr',
	), array(
		'label' => 'Website',
	)) . '
					' . $__templater->callMacro('public:custom_fields_macros', 'custom_fields_edit', array(
		'type' => 'users',
		'group' => 'personal',
		'set' => $__vars['user']['Profile']['custom_fields'],
		'editMode' => 'admin',
	), $__vars) . '
					' . $__templater->formTextAreaRow(array(
		'name' => 'profile[about]',
		'value' => $__vars['user']['Profile']['about_'],
		'autosize' => 'true',
	), array(
		'label' => 'About',
		'hint' => 'You may use BB code',
	)) . '
				</div>

				<h3 class="block-formSectionHeader">
					<span class="collapseTrigger collapseTrigger--block" data-xf-click="toggle" data-target="< :up:next">
						<span class="block-formSectionHeader-aligner">' . 'Profile info' . '</span>
					</span>
				</h3>
				<div class="block-body block-body--collapsible">
					' . $__templater->formTextBoxRow(array(
		'name' => 'user[custom_title]',
		'value' => $__vars['user']['custom_title_'],
		'maxlength' => $__templater->func('max_length', array($__vars['user'], 'custom_title', ), false),
	), array(
		'label' => 'Custom title',
	)) . '
					' . $__templater->formTextAreaRow(array(
		'name' => 'profile[signature]',
		'value' => $__vars['user']['Profile']['signature_'],
		'autosize' => 'true',
	), array(
		'label' => 'Signature',
		'hint' => 'You may use BB code',
	)) . '

					<hr class="formRowSep" />

					' . $__templater->formNumberBoxRow(array(
		'name' => 'user[message_count]',
		'value' => $__vars['user']['message_count'],
		'min' => '0',
	), array(
		'label' => 'Messages',
	)) . '
					' . $__templater->formNumberBoxRow(array(
		'name' => 'user[reaction_score]',
		'value' => $__vars['user']['reaction_score'],
	), array(
		'label' => 'Reaction score',
	)) . '
					' . $__templater->formNumberBoxRow(array(
		'name' => 'user[trophy_points]',
		'value' => $__vars['user']['trophy_points'],
		'min' => '0',
	), array(
		'label' => 'Trophy points',
	)) . '
				</div>

				<h3 class="block-formSectionHeader">
					<span class="collapseTrigger collapseTrigger--block" data-xf-click="toggle" data-target="< :up:next">
						<span class="block-formSectionHeader-aligner">' . 'Additional contact' . '</span>
					</span>
				</h3>
				<div class="block-body block-body--collapsible">
					' . $__templater->callMacro('public:custom_fields_macros', 'custom_fields_edit', array(
		'type' => 'users',
		'group' => 'contact',
		'set' => $__vars['user']['Profile']['custom_fields'],
		'editMode' => 'admin',
	), $__vars) . '
				</div>

				<h3 class="block-formSectionHeader">
					<span class="collapseTrigger collapseTrigger--block" data-xf-click="toggle" data-target="< :up:next">
						<span class="block-formSectionHeader-aligner">' . 'Preferences' . '</span>
					</span>
				</h3>
				<div class="block-body block-body--collapsible">
					' . $__templater->formSelectRow(array(
		'name' => 'user[style_id]',
		'value' => $__vars['user']['style_id'],
	), $__compilerTemp15, array(
		'label' => 'Style',
	)) . '

					<hr class="formRowSep" />

					' . $__templater->formSelectRow(array(
		'name' => 'user[language_id]',
		'value' => $__vars['user']['language_id'],
	), $__compilerTemp17, array(
		'label' => 'Language',
	)) . '

					' . $__templater->formSelectRow(array(
		'name' => 'user[timezone]',
		'value' => $__vars['user']['timezone'],
	), $__compilerTemp19, array(
		'label' => 'Time zone',
	)) . '

					<hr class="formRowSep" />

					' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'option[content_show_signature]',
		'selected' => $__vars['user']['Option']['content_show_signature'],
		'label' => '
							' . 'Show signatures with messages',
		'_type' => 'option',
	),
	array(
		'name' => 'option[email_on_conversation]',
		'selected' => $__vars['user']['Option']['email_on_conversation'],
		'label' => '
							' . 'Receive email when a new conversation message is received',
		'_type' => 'option',
	)), array(
	)) . '

					' . $__templater->formSelectRow(array(
		'name' => 'option[creation_watch_state]',
		'value' => $__vars['user']['Option']['creation_watch_state'],
	), array(array(
		'value' => 'watch_no_email',
		'label' => 'Yes',
		'_type' => 'option',
	),
	array(
		'value' => 'watch_email',
		'label' => 'Yes, with email',
		'_type' => 'option',
	),
	array(
		'value' => '',
		'label' => 'No',
		'_type' => 'option',
	)), array(
		'label' => 'Watch content on creation',
	)) . '

					' . $__templater->formSelectRow(array(
		'name' => 'option[interaction_watch_state]',
		'value' => $__vars['user']['Option']['interaction_watch_state'],
	), array(array(
		'value' => 'watch_no_email',
		'label' => 'Yes',
		'_type' => 'option',
	),
	array(
		'value' => 'watch_email',
		'label' => 'Yes, with email',
		'_type' => 'option',
	),
	array(
		'value' => '',
		'label' => 'No',
		'_type' => 'option',
	)), array(
		'label' => 'Watch content on interaction',
	)) . '

					' . $__templater->callMacro('public:custom_fields_macros', 'custom_fields_edit', array(
		'type' => 'users',
		'group' => 'preferences',
		'set' => $__vars['user']['Profile']['custom_fields'],
		'editMode' => 'admin',
	), $__vars) . '
				</div>

				<h3 class="block-formSectionHeader">
					<span class="collapseTrigger collapseTrigger--block" data-xf-click="toggle" data-target="< :up:next">
						<span class="block-formSectionHeader-aligner">' . 'Privacy' . '</span>
					</span>
				</h3>
				<div class="block-body block-body--collapsible">
					' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'user[visible]',
		'selected' => $__vars['user']['visible'],
		'label' => 'Show online status',
		'_dependent' => array('
								' . $__templater->formCheckBox(array(
	), array(array(
		'name' => 'user[activity_visible]',
		'selected' => $__vars['user']['activity_visible'],
		'label' => '
										' . 'Show current activity' . '
									',
		'_type' => 'option',
	))) . '
							'),
		'_type' => 'option',
	),
	array(
		'name' => 'option[receive_admin_email]',
		'selected' => $__vars['user']['Option']['receive_admin_email'],
		'label' => '
							' . 'Receive news and update emails' . '
						',
		'_type' => 'option',
	),
	array(
		'name' => 'option[show_dob_date]',
		'selected' => $__vars['user']['Option']['show_dob_date'],
		'label' => '
							' . 'Show day and month of birth' . '
						',
		'_type' => 'option',
	),
	array(
		'name' => 'option[show_dob_year]',
		'selected' => $__vars['user']['Option']['show_dob_year'],
		'label' => '
							' . 'Show year of birth' . '
						',
		'_type' => 'option',
	)), array(
		'label' => 'General privacy',
	)) . '

					<hr class="formRowSep" />

					' . '
					' . $__templater->callMacro(null, 'privacy_select', array(
		'name' => 'allow_view_profile',
		'label' => 'View this user\'s profile page details',
		'user' => $__vars['user'],
	), $__vars) . '

					' . '
					' . $__templater->callMacro(null, 'privacy_select', array(
		'name' => 'allow_post_profile',
		'label' => 'Post messages on this user\'s profile page',
		'user' => $__vars['user'],
		'hideEveryone' => true,
	), $__vars) . '

					' . '
					' . $__templater->callMacro(null, 'privacy_select', array(
		'name' => 'allow_receive_news_feed',
		'label' => 'Receive this user\'s news feed',
		'user' => $__vars['user'],
	), $__vars) . '

					<hr class="formRowSep" />

					' . '
					' . $__templater->callMacro(null, 'privacy_select', array(
		'name' => 'allow_send_personal_conversation',
		'label' => 'Initiate conversations with this user',
		'user' => $__vars['user'],
		'hideEveryone' => true,
	), $__vars) . '

					' . '
					' . $__templater->callMacro(null, 'privacy_select', array(
		'name' => 'allow_view_identities',
		'label' => 'View this user\'s identities',
		'user' => $__vars['user'],
	), $__vars) . '
				</div>

				' . $__templater->formSubmitRow(array(
		'icon' => 'save',
		'sticky' => 'true',
	), array(
	)) . '
			</li>

			' . $__compilerTemp20 . '

			' . $__compilerTemp21 . '

			' . $__compilerTemp22 . '
			' . '
		</ul>
	', array(
		'action' => $__templater->func('link', array('users/save', $__vars['user'], ), false),
		'ajax' => 'true',
		'class' => 'block-container',
		'novalidate' => 'novalidate',
	)) . '
</div>

';
	return $__finalCompiled;
});