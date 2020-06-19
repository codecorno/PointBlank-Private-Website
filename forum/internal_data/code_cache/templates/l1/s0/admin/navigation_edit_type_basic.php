<?php
// FROM HASH: 77a1d2c69da96f8d6387dcc2875843c2
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->formTextBoxRow(array(
		'name' => $__vars['formPrefix'] . '[link]',
		'value' => $__vars['config']['link'],
		'code' => 'true',
	), array(
		'label' => 'Link',
	)) . '

' . $__templater->formTextBoxRow(array(
		'name' => $__vars['formPrefix'] . '[display_condition]',
		'value' => $__vars['config']['display_condition'],
		'code' => 'true',
	), array(
		'label' => 'Display condition',
		'explain' => 'This should be entered as a template-style expression.',
	)) . '

';
	$__compilerTemp1 = '';
	if ($__templater->isTraversable($__vars['config']['extra_attributes'])) {
		foreach ($__vars['config']['extra_attributes'] AS $__vars['name'] => $__vars['value']) {
			$__compilerTemp1 .= '
			<li class="inputGroup"dir="ltr" >
				' . $__templater->formTextBox(array(
				'name' => $__vars['formPrefix'] . '[extra_attr_names][]',
				'value' => $__vars['name'],
				'size' => '15',
				'code' => 'true',
				'placeholder' => 'Name',
			)) . '
				<span class="inputGroup-splitter"></span>
				' . $__templater->formTextBox(array(
				'name' => $__vars['formPrefix'] . '[extra_attr_values][]',
				'value' => $__vars['value'],
				'size' => '25',
				'code' => 'true',
				'placeholder' => 'Value',
			)) . '
			</li>
		';
		}
	}
	$__finalCompiled .= $__templater->formRow('

	<ul class="listPlain inputGroup-container">
		' . $__compilerTemp1 . '
		<li class="inputGroup" data-xf-init="field-adder" dir="ltr" >
			' . $__templater->formTextBox(array(
		'name' => $__vars['formPrefix'] . '[extra_attr_names][]',
		'size' => '15',
		'code' => 'true',
		'placeholder' => 'Name',
	)) . '
			<span class="inputGroup-splitter"></span>
			' . $__templater->formTextBox(array(
		'name' => $__vars['formPrefix'] . '[extra_attr_values][]',
		'size' => '25',
		'code' => 'true',
		'placeholder' => 'Value',
	)) . '
		</li>
	</ul>
', array(
		'rowtype' => 'input',
		'label' => 'Extra attributes',
	));
	return $__finalCompiled;
});