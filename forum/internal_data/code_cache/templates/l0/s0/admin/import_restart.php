<?php
// FROM HASH: fbe4f4fbafa70926b5cf8a62568210fb
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Restart import' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['title']));
	$__finalCompiled .= '

<div class="blocks">
	' . $__templater->form('
		<div class="block-container">
			<div class="block-body">
				' . $__templater->formInfoRow('
					' . 'A pending import from ' . $__templater->escape($__vars['title']) . ' has been found. You may restart this import using the button below.' . '
				', array(
		'rowtype' => 'confirm',
	)) . '
			</div>
			' . $__templater->formSubmitRow(array(
		'submit' => 'Restart import',
	), array(
		'rowtype' => 'simple',
	)) . '
		</div>
	', array(
		'action' => $__templater->func('link', array('import/run', ), false),
		'class' => 'block',
	)) . '

	<div class="blocks-textJoiner"><span></span><em>' . 'or' . '</em><span></span></div>

	' . $__templater->form('
		<div class="block-container">
			<div class="block-body">
				' . $__templater->formInfoRow('
					' . 'Alternatively, you may cancel this import to start a new one.' . '

					<div>
						' . $__templater->formCheckBox(array(
		'standalone' => 'true',
	), array(array(
		'name' => 'confirm',
		'data-xf-init' => 'disabler',
		'data-container' => '.js-submitDisable',
		'label' => '
								' . 'Confirm action' . '
							',
		'_type' => 'option',
	))) . '
					</div>
				', array(
		'rowtype' => 'confirm',
	)) . '
			</div>
			' . $__templater->formSubmitRow(array(
		'submit' => 'Cancel import',
	), array(
		'rowtype' => 'simple',
		'rowclass' => 'js-submitDisable',
	)) . '
		</div>
	', array(
		'action' => $__templater->func('link', array('import/cancel', ), false),
		'class' => 'block',
	)) . '
</div>';
	return $__finalCompiled;
});