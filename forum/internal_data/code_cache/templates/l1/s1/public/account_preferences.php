<?php
// FROM HASH: 7d55d430a8c15384f817e32b54630a75
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Preferences');
	$__finalCompiled .= '

';
	$__templater->wrapTemplate('account_wrapper', $__vars);
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	if ($__templater->method($__vars['xf']['visitor'], 'canChangeStyle', array())) {
		$__compilerTemp1 .= '

				';
		$__compilerTemp2 = array(array(
			'value' => '0',
			'label' => 'Use default style' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['defaultStyle']['title']),
			'_type' => 'option',
		));
		$__compilerTemp2[] = array(
			'label' => 'Styles' . $__vars['xf']['language']['label_separator'],
			'_type' => 'optgroup',
			'options' => array(),
		);
		end($__compilerTemp2); $__compilerTemp3 = key($__compilerTemp2);
		if ($__templater->isTraversable($__vars['styles'])) {
			foreach ($__vars['styles'] AS $__vars['style']) {
				$__compilerTemp2[$__compilerTemp3]['options'][] = array(
					'value' => $__vars['style']['style_id'],
					'label' => $__templater->func('repeat', array('--', $__vars['style']['depth'], ), true) . ' ' . $__templater->escape($__vars['style']['title']) . ((!$__vars['style']['user_selectable']) ? ' *' : ''),
					'_type' => 'option',
				);
			}
		}
		$__compilerTemp1 .= $__templater->formSelectRow(array(
			'name' => 'user[style_id]',
			'value' => $__vars['xf']['visitor']['style_id'],
		), $__compilerTemp2, array(
			'label' => 'Style',
		)) . '
			';
	} else {
		$__compilerTemp1 .= '
				' . $__templater->formHiddenVal('user[style_id]', $__vars['xf']['visitor']['style_id'], array(
		)) . '
			';
	}
	$__compilerTemp4 = '';
	if ($__templater->method($__vars['xf']['visitor'], 'canChangeLanguage', array())) {
		$__compilerTemp4 .= '
				';
		$__compilerTemp5 = array();
		$__compilerTemp6 = $__templater->method($__vars['languageTree'], 'getFlattened', array(0, ));
		if ($__templater->isTraversable($__compilerTemp6)) {
			foreach ($__compilerTemp6 AS $__vars['treeEntry']) {
				$__compilerTemp5[] = array(
					'value' => $__vars['treeEntry']['record']['language_id'],
					'label' => $__templater->func('repeat', array('--', $__vars['treeEntry']['depth'], ), true) . ' ' . $__templater->escape($__vars['treeEntry']['record']['title']),
					'_type' => 'option',
				);
			}
		}
		$__compilerTemp4 .= $__templater->formSelectRow(array(
			'name' => 'user[language_id]',
			'value' => $__vars['xf']['visitor']['language_id'],
		), $__compilerTemp5, array(
			'label' => 'Language',
		)) . '
			';
	} else {
		$__compilerTemp4 .= '
				' . $__templater->formHiddenVal('user[language_id]', ($__vars['xf']['visitor']['language_id'] ? $__vars['xf']['visitor']['language_id'] : $__vars['xf']['options']['defaultLanguageId']), array(
		)) . '
			';
	}
	$__compilerTemp7 = $__templater->mergeChoiceOptions(array(), $__vars['timeZones']);
	$__compilerTemp8 = '';
	if ($__vars['xf']['options']['enableNotices'] AND ($__templater->func('count', array($__vars['xf']['session']['dismissedNotices'], ), false) > 0)) {
		$__compilerTemp8 .= '
				<hr class="formRowSep" />

				' . $__templater->formCheckBoxRow(array(
		), array(array(
			'name' => 'restore_notices',
			'label' => 'Restore dismissed notices',
			'hint' => 'Any notices you have previously dismissed will be restored to view if you check this option.',
			'_type' => 'option',
		)), array(
		)) . '
			';
	}
	$__compilerTemp9 = '';
	if ($__templater->method($__vars['xf']['visitor'], 'canUsePushNotifications', array())) {
		$__compilerTemp9 .= '
				' . $__templater->formRow('
					' . $__templater->button('
						' . 'Checking device capabilities' . $__vars['xf']['language']['ellipsis'] . '
					', array(
			'class' => 'is-disabled',
			'data-xf-init' => 'push-toggle',
		), '', array(
		)) . '
				', array(
			'label' => 'Push notifications',
			'rowtype' => 'button',
			'explain' => 'Enabling push notifications requires a supported device. Enabling push notifications will enable them for this device only. If you log out of this device, you will need to re-enable push notifications.',
		)) . '

				' . $__templater->formCheckBoxRow(array(
		), array(array(
			'name' => 'option[push_on_conversation]',
			'checked' => $__vars['xf']['visitor']['Option']['push_on_conversation'],
			'label' => 'Receive push notification when a new conversation message is received',
			'_type' => 'option',
		)), array(
			'label' => '',
		)) . '

				';
		$__templater->inlineJs('
					jQuery.extend(true, XF.config, {
						skipServiceWorkerRegistration: true,
						skipPushNotificationCta: true
					});

					jQuery.extend(XF.phrases, {
						push_enable_label: "' . $__templater->filter('Enable push notifications', array(array('escape', array('js', )),), false) . '",
						push_disable_label: "' . $__templater->filter('Disable push notifications', array(array('escape', array('js', )),), false) . '",
						push_not_supported_label: "' . $__templater->filter('Push notifications not supported', array(array('escape', array('js', )),), false) . '",
						push_blocked_label: "' . $__templater->filter('Push notifications blocked', array(array('escape', array('js', )),), false) . '"
					});
				');
		$__compilerTemp9 .= '
			';
	} else {
		$__compilerTemp9 .= '
				' . $__templater->formHiddenVal('option[push_on_conversation]', $__vars['xf']['visitor']['Option']['push_on_conversation'], array(
		)) . '
			';
	}
	$__compilerTemp10 = '';
	if (!$__templater->test($__vars['alertOptOuts'], 'empty', array())) {
		$__compilerTemp10 .= '
			';
		$__templater->includeCss('notification_opt_out.less');
		$__compilerTemp10 .= '
			<h2 class="block-formSectionHeader"><span class="block-formSectionHeader-aligner">' . 'Receive a notification when someone' . $__vars['xf']['language']['ellipsis'] . '</span></h2>
			<div class="block-body">
				';
		$__vars['canPush'] = $__templater->method($__vars['xf']['visitor'], 'canUsePushNotifications', array());
		$__compilerTemp10 .= '
				';
		if ($__templater->isTraversable($__vars['alertOptOuts'])) {
			foreach ($__vars['alertOptOuts'] AS $__vars['contentType'] => $__vars['options']) {
				$__compilerTemp10 .= '
					';
				if ($__templater->isTraversable($__vars['options'])) {
					foreach ($__vars['options'] AS $__vars['action'] => $__vars['label']) {
						$__compilerTemp10 .= '
						';
						$__compilerTemp11 = '';
						if ($__vars['canPush']) {
							$__compilerTemp11 .= '
									<li class="notificationChoices-choice notificationChoices-choice--push">
										' . $__templater->formCheckBox(array(
								'standalone' => 'true',
							), array(array(
								'name' => 'push[' . $__vars['contentType'] . '_' . $__vars['action'] . ']',
								'checked' => $__templater->method($__vars['xf']['visitor']['Option'], 'doesReceivePush', array($__vars['contentType'], $__vars['action'], )),
								'label' => 'Push',
								'_type' => 'option',
							))) . '
										' . $__templater->formHiddenVal('push_shown[' . $__vars['contentType'] . '_' . $__vars['action'] . ']', '1', array(
							)) . '
									</li>
								';
						}
						$__compilerTemp10 .= $__templater->formRow('
							<ul class="notificationChoices">
								<li class="notificationChoices-choice notificationChoices-choice--alert">
									' . $__templater->formCheckBox(array(
							'standalone' => 'true',
						), array(array(
							'name' => 'alert[' . $__vars['contentType'] . '_' . $__vars['action'] . ']',
							'data-xf-init' => ($__vars['canPush'] ? 'disabler' : ''),
							'data-container' => '< .notificationChoices | .notificationChoices-choice--push',
							'checked' => $__templater->method($__vars['xf']['visitor']['Option'], 'doesReceiveAlert', array($__vars['contentType'], $__vars['action'], )),
							'label' => 'Alert',
							'_type' => 'option',
						))) . '
								</li>
								' . $__compilerTemp11 . '
							</ul>
						', array(
							'label' => $__templater->escape($__vars['label']),
							'data-content-type' => $__vars['contentType'],
							'data-action' => $__vars['action'],
						)) . '
					';
					}
				}
				$__compilerTemp10 .= '
					<hr class="formRowSep" />
				';
			}
		}
		$__compilerTemp10 .= '

			</div>
		';
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__compilerTemp1 . '

			' . $__compilerTemp4 . '

			' . $__templater->formSelectRow(array(
		'name' => 'user[timezone]',
		'value' => $__vars['xf']['visitor']['timezone'],
	), $__compilerTemp7, array(
		'label' => 'Time zone',
	)) . '

			' . $__templater->callMacro('helper_account', 'email_options_row', array(
		'showConversationOption' => true,
	), $__vars) . '

			' . $__templater->formCheckBoxRow(array(
	), array(array(
		'value' => 'watch_no_email',
		'name' => 'option[creation_watch_state]',
		'checked' => ($__vars['xf']['visitor']['Option']['creation_watch_state'] ? true : false),
		'label' => 'Automatically watch content you create' . $__vars['xf']['language']['ellipsis'],
		'_dependent' => array($__templater->formCheckBox(array(
	), array(array(
		'value' => 'watch_email',
		'name' => 'option[creation_watch_state]',
		'checked' => ($__vars['xf']['visitor']['Option']['creation_watch_state'] == 'watch_email'),
		'label' => 'and receive email notifications',
		'_type' => 'option',
	)))),
		'_type' => 'option',
	),
	array(
		'value' => 'watch_no_email',
		'name' => 'option[interaction_watch_state]',
		'checked' => ($__vars['xf']['visitor']['Option']['interaction_watch_state'] ? true : false),
		'label' => 'Automatically watch content you interact with' . $__vars['xf']['language']['ellipsis'],
		'_dependent' => array($__templater->formCheckBox(array(
	), array(array(
		'value' => 'watch_email',
		'name' => 'option[interaction_watch_state]',
		'checked' => ($__vars['xf']['visitor']['Option']['interaction_watch_state'] == 'watch_email'),
		'label' => 'and receive email notifications',
		'_type' => 'option',
	)))),
		'_type' => 'option',
	),
	array(
		'name' => 'option[content_show_signature]',
		'checked' => $__vars['xf']['visitor']['Option']['content_show_signature'],
		'label' => 'Show people\'s signatures with their messages',
		'_type' => 'option',
	)), array(
		'label' => 'Content options',
	)) . '

			' . $__templater->callMacro('helper_account', 'activity_privacy_row', array(), $__vars) . '

			' . $__templater->callMacro('custom_fields_macros', 'custom_fields_edit', array(
		'type' => 'users',
		'group' => 'preferences',
		'set' => $__vars['xf']['visitor']['Profile']['custom_fields'],
	), $__vars) . '

			' . $__compilerTemp8 . '

			' . $__compilerTemp9 . '
		</div>

		' . $__compilerTemp10 . '

		' . $__templater->formSubmitRow(array(
		'icon' => 'save',
		'sticky' => 'true',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('account/preferences', ), false),
		'ajax' => 'true',
		'class' => 'block',
		'data-force-flash-message' => 'true',
	));
	return $__finalCompiled;
});