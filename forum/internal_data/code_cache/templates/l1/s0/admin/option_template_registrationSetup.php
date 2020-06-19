<?php
// FROM HASH: a37cf6b67eeb542a18837cee1cdb61df
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => $__vars['inputName'] . '[enabled]',
		'selected' => $__vars['option']['option_value']['enabled'],
		'label' => 'Enable registration',
		'_type' => 'option',
	),
	array(
		'name' => $__vars['inputName'] . '[emailConfirmation]',
		'selected' => $__vars['option']['option_value']['emailConfirmation'],
		'label' => 'Enable email confirmation',
		'hint' => 'If selected, users will need to click on a link in an email before their registration is completed.',
		'_type' => 'option',
	),
	array(
		'name' => $__vars['inputName'] . '[moderation]',
		'selected' => $__vars['option']['option_value']['moderation'],
		'label' => 'Enable manual approval',
		'hint' => 'If selected, an administrator will need to manually approve users before their registration is completed.',
		'_type' => 'option',
	),
	array(
		'name' => $__vars['inputName'] . '[requireDob]',
		'selected' => $__vars['option']['option_value']['requireDob'],
		'label' => 'Require date of birth',
		'_type' => 'option',
	),
	array(
		'selected' => ($__vars['option']['option_value']['minimumAge'] ? true : false),
		'label' => 'Minimum age' . $__vars['xf']['language']['label_separator'],
		'_dependent' => array($__templater->formNumberBox(array(
		'name' => $__vars['inputName'] . '[minimumAge]',
		'value' => ($__vars['option']['option_value']['minimumAge'] ?: 13),
		'min' => '1',
		'units' => 'Years',
	))),
		'_type' => 'option',
	),
	array(
		'name' => $__vars['inputName'] . '[requireEmailChoice]',
		'selected' => $__vars['option']['option_value']['requireEmailChoice'],
		'label' => 'Require site email preference',
		'hint' => 'If selected, users must choose at registration whether or not to receive site emails. The default value depends on <code>registrationDefaults</code> and users may change their preference later.',
		'_type' => 'option',
	),
	array(
		'name' => $__vars['inputName'] . '[requireLocation]',
		'selected' => $__vars['option']['option_value']['requireLocation'],
		'label' => 'Require location',
		'_type' => 'option',
	)), array(
		'label' => $__templater->escape($__vars['option']['title']),
		'hint' => $__templater->escape($__vars['hintHtml']),
		'explain' => $__templater->escape($__vars['explainHtml']),
		'html' => $__templater->escape($__vars['listedHtml']),
	));
	return $__finalCompiled;
});