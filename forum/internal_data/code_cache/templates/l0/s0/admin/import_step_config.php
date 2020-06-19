<?php
// FROM HASH: dfb648920fd7467a2f6619bd20d864fc
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Configure importer' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['title']));
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	$__compilerTemp2 = '';
	$__compilerTemp2 .= '
					' . $__templater->filter($__templater->method($__vars['importer'], 'renderStepConfigOptions', array($__vars, )), array(array('raw', array()),), true) . '
				';
	if (strlen(trim($__compilerTemp2)) > 0) {
		$__compilerTemp1 .= '
				' . $__compilerTemp2 . '
			';
	} else {
		$__compilerTemp1 .= '
				<div class="block-row">' . 'No step configuration necessary.' . '</div>
			';
	}
	$__compilerTemp3 = '';
	if ($__templater->isTraversable($__vars['steps'])) {
		foreach ($__vars['steps'] AS $__vars['step']) {
			$__compilerTemp3 .= '
		' . $__templater->formHiddenVal('steps[]', $__vars['step'], array(
			)) . '
	';
		}
	}
	$__finalCompiled .= $__templater->form('

	<div class="block-container">
		<div class="block-body">
			' . $__compilerTemp1 . '
		</div>
		' . $__templater->formSubmitRow(array(
		'submit' => 'Continue' . $__vars['xf']['language']['ellipsis'],
	), array(
	)) . '
	</div>

	' . $__templater->formHiddenVal('config', $__templater->filter($__vars['baseConfig'], array(array('json', array()),), false), array(
	)) . '
	' . $__templater->formHiddenVal('importer', $__vars['importerId'], array(
	)) . '
	' . $__templater->formHiddenVal('retain_ids', $__vars['retainIds'], array(
	)) . '
	' . $__templater->formHiddenVal('log_table', $__vars['logTable'], array(
	)) . '
	' . $__compilerTemp3 . '
	' . $__templater->formHiddenVal('steps_configured', '1', array(
	)) . '
', array(
		'action' => $__templater->func('link', array('import/step-config', ), false),
		'class' => 'block js-importConfigForm',
		'ajax' => 'true',
		'data-replace' => '.js-importConfigForm',
	));
	return $__finalCompiled;
});