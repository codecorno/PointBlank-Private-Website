<?php
// FROM HASH: dab31c707c94216843ce50af193e5c43
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => $__vars['inputName'] . '[showStaff]',
		'selected' => $__vars['option']['option_value']['showStaff'],
		'label' => 'Show staff banner',
		'hint' => 'If enabled, staff members will automatically have a banner added as the highest priority banner.',
		'_type' => 'option',
	),
	array(
		'name' => $__vars['inputName'] . '[displayMultiple]',
		'selected' => $__vars['option']['option_value']['displayMultiple'],
		'label' => 'Allow banner stacking',
		'hint' => 'If enabled, all banners applicable to a user will be shown. If disabled, only the highest priority banner will be displayed.',
		'_type' => 'option',
	),
	array(
		'name' => $__vars['inputName'] . '[showStaffAndOther]',
		'selected' => $__vars['option']['option_value']['showStaffAndOther'],
		'label' => 'Show staff and group banner',
		'hint' => 'If banner stacking is disabled, staff members will only have the staff banner. If this option is enabled, they will have a staff banner and the highest priority group banner.',
		'_type' => 'option',
	),
	array(
		'name' => $__vars['inputName'] . '[hideUserTitle]',
		'selected' => $__vars['option']['option_value']['hideUserTitle'],
		'label' => 'Hide standard user title',
		'hint' => 'If enabled, in situations where a user title is displayed with a banner, the user title will be hidden if a banner will be displayed. Custom, per-user titles will never be hidden.',
		'_type' => 'option',
	)), array(
		'label' => $__templater->escape($__vars['option']['title']),
		'hint' => $__templater->escape($__vars['hintHtml']),
		'explain' => $__templater->escape($__vars['explainHtml']),
		'html' => $__templater->escape($__vars['listedHtml']),
	));
	return $__finalCompiled;
});