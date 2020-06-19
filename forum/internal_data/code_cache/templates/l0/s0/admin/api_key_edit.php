<?php
// FROM HASH: 5a763dc1eef8e9a27a7e6bc6de2d82ef
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->method($__vars['apiKey'], 'isInsert', array())) {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Add API key');
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Edit API key');
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	if ($__templater->method($__vars['apiKey'], 'isUpdate', array())) {
		$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('', array(
			'href' => $__templater->func('link', array('api-keys/delete', $__vars['apiKey'], ), false),
			'icon' => 'delete',
			'overlay' => 'true',
		), '', array(
		)) . '
');
	}
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	if ($__templater->method($__vars['apiKey'], 'isUpdate', array())) {
		$__compilerTemp1 .= '
				' . $__templater->formRow('
					<code>' . $__templater->escape($__vars['apiKey']['api_key_snippet']) . '</code>
					' . $__templater->button('View full key' . '
					', array(
			'href' => $__templater->func('link', array('api-keys/view-key', $__vars['apiKey'], ), false),
			'data-xf-click' => 'overlay',
			'class' => 'button--link',
		), '', array(
		)) . '

					' . $__templater->button('Regenerate key' . '
					', array(
			'href' => $__templater->func('link', array('api-keys/regenerate', $__vars['apiKey'], ), false),
			'data-xf-click' => 'overlay',
			'class' => 'button--link',
		), '', array(
		)) . '
				', array(
			'label' => 'API key',
			'rowtype' => 'button',
		)) . '
			';
	}
	$__compilerTemp2 = '';
	if ($__templater->method($__vars['apiKey'], 'isUpdate', array())) {
		$__compilerTemp2 .= '
				' . $__templater->callMacro('api_key_macros', 'key_type_row', array(
			'apiKey' => $__vars['apiKey'],
		), $__vars) . '

				' . $__templater->formRow('
					' . $__templater->func('date_dynamic', array($__vars['apiKey']['creation_date'], array(
		))) . '
					&middot;
					' . 'By ' . ($__templater->escape($__vars['apiKey']['Creator']['username']) ?: 'N/A') . '' . '
				', array(
			'label' => 'Created',
		)) . '

				';
		$__compilerTemp3 = '';
		if ($__vars['apiKey']['last_use_date']) {
			$__compilerTemp3 .= '
						' . $__templater->func('date_dynamic', array($__vars['apiKey']['last_use_date'], array(
			))) . '
					';
		} else {
			$__compilerTemp3 .= '
						' . 'N/A' . '
					';
		}
		$__compilerTemp2 .= $__templater->formRow('
					' . $__compilerTemp3 . '
				', array(
			'label' => 'Last used',
		)) . '
			';
	} else {
		$__compilerTemp2 .= '
				' . $__templater->formRadioRow(array(
			'name' => 'key_type',
			'value' => 'guest',
		), array(array(
			'value' => 'guest',
			'label' => 'Guest key',
			'_type' => 'option',
		),
		array(
			'label' => 'User key',
			'value' => 'user',
			'data-xf-init' => 'disabler',
			'_dependent' => array($__templater->formTextBox(array(
			'name' => 'username',
			'ac' => 'single',
			'autocomplete' => 'off',
			'value' => (($__vars['apiKey']['key_type'] == 'user') ? $__vars['apiKey']['User']['username'] : ''),
			'maxlength' => $__templater->func('max_length', array($__vars['xf']['visitor'], 'username', ), false),
		))),
			'afterhint' => 'Enter the username of the user this key should authenticate as.',
			'_type' => 'option',
		),
		array(
			'value' => 'super',
			'label' => 'Super user key',
			'_type' => 'option',
		)), array(
			'label' => 'Key type',
			'explain' => 'This cannot be changed after creation. Changes require a new API key to be generated.',
		)) . '
			';
	}
	$__compilerTemp4 = array();
	if ($__templater->isTraversable($__vars['scopes'])) {
		foreach ($__vars['scopes'] AS $__vars['scope']) {
			$__compilerTemp4[] = array(
				'name' => 'scopes[]',
				'value' => $__vars['scope']['api_scope_id'],
				'checked' => $__vars['apiKey']['scopes'][$__vars['scope']['api_scope_id']],
				'label' => $__templater->escape($__vars['scope']['api_scope_id']),
				'hint' => $__templater->escape($__vars['scope']['description']),
				'_type' => 'option',
			);
		}
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__compilerTemp1 . '

			' . $__templater->formTextBoxRow(array(
		'name' => 'title',
		'maxlength' => $__templater->func('max_length', array($__vars['apiKey'], 'title', ), false),
		'value' => $__vars['apiKey']['title'],
	), array(
		'label' => 'Title',
		'explain' => 'Provide a title for this key as seen in the list of API keys.',
	)) . '

			' . $__compilerTemp2 . '

			' . $__templater->formRadioRow(array(
		'name' => 'allow_all_scopes',
		'value' => $__vars['apiKey']['allow_all_scopes'],
	), array(array(
		'value' => '0',
		'label' => 'Selected scopes only' . ':',
		'_dependent' => array($__templater->formCheckBox(array(
	), $__compilerTemp4)),
		'_type' => 'option',
	),
	array(
		'value' => '1',
		'label' => 'All scopes',
		'_type' => 'option',
	)), array(
		'label' => 'Allowed scopes',
		'explain' => 'Scopes allow an API key to only access certain parts of the API. For security, it is recommended that you only allow access to the scopes that you need, particularly for super user or highly-privileged user keys.',
	)) . '

			<hr class="formRowSep" />

			' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'active',
		'selected' => $__vars['apiKey']['active'],
		'label' => 'API key is active',
		'hint' => 'Use this to disable the API key.',
		'_type' => 'option',
	)), array(
	)) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'icon' => 'save',
		'sticky' => 'true',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('api-keys/save', $__vars['apiKey'], ), false),
		'ajax' => 'true',
		'data-force-flash-message' => 'on',
		'class' => 'block',
	));
	return $__finalCompiled;
});