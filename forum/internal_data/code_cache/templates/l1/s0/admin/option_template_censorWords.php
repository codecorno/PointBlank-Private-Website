<?php
// FROM HASH: 9851bde25879d8ee67a7d1c32b7c3d2b
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__compilerTemp1 = '';
	if ($__templater->isTraversable($__vars['choices'])) {
		foreach ($__vars['choices'] AS $__vars['counter'] => $__vars['choice']) {
			$__compilerTemp1 .= '
			<li class="inputGroup">
				' . $__templater->formTextBox(array(
				'name' => $__vars['inputName'] . '[' . $__vars['counter'] . '][word]',
				'value' => $__vars['choice']['word'],
				'placeholder' => 'Word or phrase',
				'size' => '20',
			)) . '
				<span class="inputGroup-splitter"></span>
				' . $__templater->formTextBox(array(
				'name' => $__vars['inputName'] . '[' . $__vars['counter'] . '][replace]',
				'value' => $__vars['choice']['replace'],
				'placeholder' => 'Replacement (optional)',
				'size' => '20',
			)) . '
			</li>
		';
		}
	}
	$__finalCompiled .= $__templater->formRow('

	<ul class="listPlain inputGroup-container">
		' . $__compilerTemp1 . '

		<li class="inputGroup" data-xf-init="field-adder" data-increment-format="' . $__templater->escape($__vars['inputName']) . '[{counter}]">
			' . $__templater->formTextBox(array(
		'name' => $__vars['inputName'] . '[' . $__vars['nextCounter'] . '][word]',
		'placeholder' => 'Word or phrase',
		'size' => '20',
	)) . '
			<span class="inputGroup-splitter"></span>
			' . $__templater->formTextBox(array(
		'name' => $__vars['inputName'] . '[' . $__vars['nextCounter'] . '][replace]',
		'placeholder' => 'Replacement (optional)',
		'size' => '20',
	)) . '
		</li>
	</ul>
', array(
		'rowtype' => 'input',
		'label' => $__templater->escape($__vars['option']['title']),
		'hint' => $__templater->escape($__vars['hintHtml']),
		'explain' => $__templater->escape($__vars['explainHtml']),
		'html' => $__templater->escape($__vars['listedHtml']),
	));
	return $__finalCompiled;
});