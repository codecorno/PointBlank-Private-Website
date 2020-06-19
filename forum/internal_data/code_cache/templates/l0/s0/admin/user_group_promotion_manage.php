<?php
// FROM HASH: d3affddd15f03625550e7ce062ec95a8
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Manage promoted users');
	$__finalCompiled .= '

';
	$__compilerTemp1 = array(array(
		'value' => '0',
		'label' => $__vars['xf']['language']['parenthesis_open'] . 'Any' . $__vars['xf']['language']['parenthesis_close'],
		'_type' => 'option',
	));
	$__compilerTemp1 = $__templater->mergeChoiceOptions($__compilerTemp1, $__vars['userGroupPromotions']);
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<h2 class="block-header">' . 'Search promotion history' . '</h2>
		<div class="block-body">
			' . $__templater->formTextBoxRow(array(
		'name' => 'username',
		'ac' => 'single',
	), array(
		'hint' => 'You may leave this blank.',
		'label' => 'User name',
	)) . '

			' . $__templater->formSelectRow(array(
		'name' => 'promotion_id',
	), $__compilerTemp1, array(
		'label' => 'Promotion',
	)) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'icon' => 'search',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('user-group-promotions/history', ), false),
		'class' => 'block',
	)) . '

';
	$__compilerTemp2 = array(array(
		'value' => '0',
		'label' => $__vars['xf']['language']['parenthesis_open'] . 'Any' . $__vars['xf']['language']['parenthesis_close'],
		'_type' => 'option',
	));
	$__compilerTemp2 = $__templater->mergeChoiceOptions($__compilerTemp2, $__vars['userGroupPromotions']);
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<h2 class="block-header">' . 'Manually promote user' . '</h2>
		<div class="block-body">
			' . $__templater->formTextBoxRow(array(
		'name' => 'username',
		'ac' => 'single',
	), array(
		'label' => 'User name',
	)) . '

			' . $__templater->formSelectRow(array(
		'name' => 'promotion_id',
	), $__compilerTemp2, array(
		'label' => 'Promotion',
	)) . '

			' . $__templater->formRadioRow(array(
		'name' => 'action',
	), array(array(
		'value' => 'promote',
		'selected' => true,
		'label' => 'Promote this user',
		'_type' => 'option',
	),
	array(
		'value' => 'demote',
		'label' => 'Prevent this user from receiving this promotion automatically',
		'_type' => 'option',
	)), array(
		'label' => 'Action',
	)) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'icon' => 'save',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('user-group-promotions/manual', ), false),
		'class' => 'block',
	));
	return $__finalCompiled;
});