<?php
// FROM HASH: 9d41a7ce60b96b1f01e964f015086c6e
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => $__vars['inputName'] . '[enabled]',
		'value' => '1',
		'selected' => $__vars['option']['option_value']['enabled'],
		'label' => 'Enable automated email handler',
		'data-hide' => 'true',
		'_dependent' => array('
			<div class="inputChoices-spacer">' . 'Connection type' . '</div>
			' . $__templater->formRadio(array(
		'name' => $__vars['inputName'] . '[type]',
		'value' => ($__vars['option']['option_value']['type'] ? $__vars['option']['option_value']['type'] : 'pop3'),
	), array(array(
		'value' => 'pop3',
		'label' => 'POP3',
		'_type' => 'option',
	),
	array(
		'value' => 'imap',
		'label' => 'IMAP',
		'_type' => 'option',
	))) . '

			<div class="inputChoices-spacer">' . 'Host' . '</div>
			<div class="inputGroup">
				' . $__templater->formTextBox(array(
		'name' => $__vars['inputName'] . '[host]',
		'value' => $__vars['option']['option_value']['host'],
		'placeholder' => 'Host',
		'size' => '40',
		'id' => $__vars['inputName'] . '_host',
	)) . '
				<span class="inputGroup-text">:</span>
				' . $__templater->formTextBox(array(
		'name' => $__vars['inputName'] . '[port]',
		'value' => $__vars['option']['option_value']['port'],
		'placeholder' => 'Port',
		'size' => '5',
	)) . '
			</div>

			<div class="inputChoices-spacer">' . 'User name and password' . '</div>
			<div class="inputGroup">
				' . $__templater->formTextBox(array(
		'name' => $__vars['inputName'] . '[username]',
		'value' => $__vars['option']['option_value']['username'],
		'placeholder' => 'User name',
		'size' => '15',
		'id' => $__vars['inputName'] . '_username',
	)) . '
				<span class="inputGroup-splitter"></span>
				' . $__templater->formTextBox(array(
		'name' => $__vars['inputName'] . '[password]',
		'value' => $__vars['option']['option_value']['password'],
		'type' => 'password',
		'size' => '15',
	)) . '
			</div>

			<div class="inputChoices-spacer">' . 'Encryption' . '</div>
			' . $__templater->formRadio(array(
		'name' => $__vars['inputName'] . '[encryption]',
		'value' => ($__vars['option']['option_value']['encryption'] ? $__vars['option']['option_value']['encryption'] : ''),
	), array(array(
		'label' => 'None',
		'_type' => 'option',
	),
	array(
		'value' => 'tls',
		'label' => 'TLS',
		'_type' => 'option',
	),
	array(
		'value' => 'ssl',
		'label' => 'SSL',
		'_type' => 'option',
	))) . '
		'),
		'_type' => 'option',
	)), array(
		'label' => $__templater->escape($__vars['option']['title']),
		'hint' => $__templater->escape($__vars['hintHtml']),
		'explain' => $__templater->escape($__vars['explainHtml']),
		'html' => $__templater->escape($__vars['listedHtml']),
	));
	return $__finalCompiled;
});