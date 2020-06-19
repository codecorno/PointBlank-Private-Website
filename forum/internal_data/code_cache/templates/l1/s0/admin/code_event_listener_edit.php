<?php
// FROM HASH: db2ac1d5fd6331b1524063ba8cc0eee9
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->method($__vars['listener'], 'isInsert', array())) {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Add code event listener');
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Edit code event listener' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['listener']['event_id']));
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	if ($__templater->method($__vars['listener'], 'isUpdate', array())) {
		$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('', array(
			'href' => $__templater->func('link', array('code-events/listeners/delete', $__vars['listener'], ), false),
			'icon' => 'delete',
			'overlay' => 'true',
		), '', array(
		)) . '
');
	}
	$__finalCompiled .= '

';
	$__templater->includeCss('code_event.less');
	$__finalCompiled .= '

';
	$__compilerTemp1 = array(array(
		'_type' => 'option',
	));
	$__compilerTemp1 = $__templater->mergeChoiceOptions($__compilerTemp1, $__vars['events']);
	$__finalCompiled .= $__templater->form('

	<div class="block-container">
		<div class="block-body">
			' . $__templater->formSelectRow(array(
		'name' => 'event_id',
		'value' => $__vars['listener']['event_id'],
		'data-xf-init' => 'desc-loader',
		'data-desc-url' => $__templater->func('link', array('code-events/get-description', ), false),
	), $__compilerTemp1, array(
		'label' => 'Listen to event',
		'html' => '
					<div class="js-descTarget eventDescription" dir="ltr">' . $__templater->filter($__vars['listener']['Event']['description'], array(array('raw', array()),), true) . '</div>
				',
	)) . '

			' . $__templater->formTextBoxRow(array(
		'name' => 'hint',
		'value' => $__vars['listener']['hint'],
		'maxlength' => $__templater->func('max_length', array($__vars['listener'], 'hint', ), false),
		'dir' => 'ltr',
	), array(
		'label' => 'Event hint',
		'explain' => 'When certain events are triggered, they will be triggered with a hint. This hint can be used to limit your listener to only being executed when it\'s needed, increasing performance.<br />
<br />
If an event is triggered with a hint, the potential hint values will be listed in the event description above. If you specify a hint here, your listener will only be run if your hint matches the hint provided when the event is triggered.<br />
<br />
<strong>Note:</strong> If the hint is a class name, you should omit the leading \\ character.',
	)) . '

			<hr class="formRowSep" />

			' . $__templater->formRow('
				' . $__templater->callMacro('helper_callback_fields', 'callback_fields', array(
		'data' => $__vars['listener'],
	), $__vars) . '
			', array(
		'rowtype' => 'input',
		'label' => 'Execute callback',
	)) . '

			' . $__templater->formNumberBoxRow(array(
		'name' => 'execute_order',
		'value' => $__vars['listener']['execute_order'],
		'min' => '0',
	), array(
		'label' => 'Callback execution order',
		'explain' => 'Lower execution orders will run first. Note that listeners that specify an event hint will always run after listeners that don\'t.',
	)) . '

			' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'active',
		'selected' => $__vars['listener']['active'],
		'label' => 'Enable callback execution',
		'_type' => 'option',
	)), array(
	)) . '

			<hr class="formRowSep" />

			' . $__templater->formTextBoxRow(array(
		'name' => 'description',
		'value' => $__vars['listener']['description'],
	), array(
		'label' => 'Description',
	)) . '

			' . $__templater->callMacro('addon_macros', 'addon_edit', array(
		'addOnId' => $__vars['listener']['addon_id'],
	), $__vars) . '

			' . $__templater->formHiddenVal('event_listener_id', $__vars['listener']['event_listener_id'], array(
	)) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'sticky' => 'true',
		'icon' => 'save',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('code-events/listeners/save', $__vars['listener'], ), false),
		'ajax' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});