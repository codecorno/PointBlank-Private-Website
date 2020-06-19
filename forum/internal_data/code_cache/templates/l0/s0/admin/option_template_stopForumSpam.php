<?php
// FROM HASH: d818457f3bc678e442056c8ba6902cf6
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => $__vars['inputName'] . '[enabled]',
		'label' => 'Check new registrations against the StopForumSpam database.',
		'value' => '1',
		'selected' => $__vars['option']['option_value']['enabled'],
		'data-hide' => 'true',
		'hint' => 'StopForumSpam will test the data collected from registering users and attempt to determine if they are a known spammer. Each warning flag that the user generates will be counted. Flags include user name, email and IP address.',
		'_dependent' => array($__templater->formCheckBox(array(
	), array(array(
		'name' => '_moderateThreshold',
		'selected' => $__vars['option']['option_value']['moderateThreshold'],
		'label' => 'Moderate registrations when this many warning flags are detected' . $__vars['xf']['language']['label_separator'],
		'_dependent' => array('
					' . $__templater->formNumberBox(array(
		'name' => $__vars['inputName'] . '[moderateThreshold]',
		'min' => '1',
		'max' => '3',
		'value' => $__vars['option']['option_value']['moderateThreshold'],
	)) . '
					<p class="formRow-explain">' . 'Specify the number of warning flags (1-3) that are required for a registration to be placed in the moderation queue rather than accepted automatically.' . '</p>
				'),
		'_type' => 'option',
	),
	array(
		'name' => '_denyThreshold',
		'selected' => $__vars['option']['option_value']['denyThreshold'],
		'label' => 'Reject registrations when this many warning flags are detected' . $__vars['xf']['language']['label_separator'],
		'_dependent' => array('
					' . $__templater->formNumberBox(array(
		'name' => $__vars['inputName'] . '[denyThreshold]',
		'min' => '1',
		'max' => '3',
		'value' => $__vars['option']['option_value']['denyThreshold'],
	)) . '
					<p class="formRow-explain">' . 'Specify the number of warning flags (1-3) that are required for a registration to be automatically rejected. This number should be equal to or greater than the value for the moderation threshold above.' . '</p>
				'),
		'_type' => 'option',
	),
	array(
		'name' => '_lastSeenCutOff',
		'selected' => $__vars['option']['option_value']['lastSeenCutOff'],
		'label' => 'Only count flags recorded within the last' . $__vars['xf']['language']['label_separator'],
		'_dependent' => array('
					' . $__templater->formNumberBox(array(
		'name' => $__vars['inputName'] . '[lastSeenCutOff]',
		'min' => '0',
		'value' => $__vars['option']['option_value']['lastSeenCutOff'],
		'units' => 'Days',
	)) . '
					<p class="formRow-explain">' . 'In order to avoid false positives, you may opt to only count hits against the spam database that have been previously recorded within a limited time period.' . '</p>
				'),
		'_type' => 'option',
	),
	array(
		'name' => '_frequencyCutOff',
		'selected' => $__vars['option']['option_value']['frequencyCutOff'],
		'label' => 'Only count flags recorded at least this many times' . $__vars['xf']['language']['label_separator'],
		'_dependent' => array('
					' . $__templater->formNumberBox(array(
		'name' => $__vars['inputName'] . '[frequencyCutOff]',
		'min' => '0',
		'value' => $__vars['option']['option_value']['frequencyCutOff'],
	)) . '
					<p class="formRow-explain">' . 'You may choose to only count hits against the spam database that have previously been reported a certain number of times.' . '</p>
				'),
		'_type' => 'option',
	),
	array(
		'name' => $__vars['inputName'] . '[hashEmail]',
		'selected' => $__vars['option']['option_value']['hashEmail'],
		'label' => 'Hash emails before submission',
		'hint' => 'If selected, the user\'s email address will be converted to an MD5 hash before submission. This reduces the number of checks which are possible, including blacklist checks.',
		'_type' => 'option',
	)))),
		'_type' => 'option',
	),
	array(
		'name' => $__vars['inputName'] . '[submitRejections]',
		'value' => '1',
		'selected' => $__vars['option']['option_value']['submitRejections'],
		'label' => 'Submit spammer information to StopForumSpam using API key' . $__vars['xf']['language']['label_separator'],
		'_dependent' => array('
			' . $__templater->formTextBox(array(
		'name' => $__vars['inputName'] . '[apiKey]',
		'value' => $__vars['option']['option_value']['apiKey'],
	)) . '
			<p class="formRow-explain">' . 'If you have an API key from StopForumSpam, you may have spammer activity on your site automatically reported back to StopForumSpam in order to improve the database for other users. This option will cause users who are banned via the spam cleaner to be reported.' . '</p>
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