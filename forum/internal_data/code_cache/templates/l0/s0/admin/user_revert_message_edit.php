<?php
// FROM HASH: 176fb7073766bafc3db6648bb71252dc
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Revert message edits');
	$__finalCompiled .= '

' . $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formRow($__templater->escape($__vars['user']['username']), array(
		'label' => 'User',
	)) . '
			' . $__templater->formRow('

				<div class="inputGroup">
					' . $__templater->formNumberBox(array(
		'name' => 'cutoff[amount]',
		'value' => '1',
		'min' => '0',
	)) . '
					<span class="inputGroup-splitter"></span>
					' . $__templater->formSelect(array(
		'name' => 'cutoff[unit]',
		'value' => 'days',
		'class' => 'input--inline',
	), array(array(
		'value' => 'hours',
		'label' => 'Hours',
		'_type' => 'option',
	),
	array(
		'value' => 'days',
		'label' => 'Days',
		'_type' => 'option',
	),
	array(
		'value' => 'weeks',
		'label' => 'Weeks',
		'_type' => 'option',
	),
	array(
		'value' => 'months',
		'label' => 'Months',
		'_type' => 'option',
	))) . '
				</div>
			', array(
		'rowtype' => 'input',
		'label' => 'Revert edits within last',
		'explain' => 'Only edits within the last ' . $__templater->escape($__vars['xf']['options']['editHistory']['length']) . ' days will be reverted.',
	)) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'submit' => 'Revert edits',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('users/revert-message-edit', $__vars['user'], ), false),
		'class' => 'block',
	));
	return $__finalCompiled;
});