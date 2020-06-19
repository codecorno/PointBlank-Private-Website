<?php
// FROM HASH: 3cca502c783f29af106c25142fff16da
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Confirm action');
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	if ($__templater->method($__vars['addOn'], 'isLegacy', array())) {
		$__compilerTemp1 .= '
					<div class="blockMessage blockMessage--warning blockMessage--iconic">
						' . 'Uninstalling legacy add-ons may leave orphaned data. Upgrade the add-on to a compatible version before uninstalling, if possible.' . '
					</div>
				';
	} else {
		$__compilerTemp1 .= '
					<div class="blockMessage blockMessage--important blockMessage--iconic">
						' . 'This will remove any data created by the add-on.' . '
					</div>
				';
	}
	$__finalCompiled .= $__templater->form('

	<div class="block-container">
		<div class="block-body">
			' . $__templater->formInfoRow('
				' . 'Please confirm that you want to uninstall the following add-on' . $__vars['xf']['language']['label_separator'] . '
				<strong>' . $__templater->escape($__vars['addOn']['title']) . ' ' . $__templater->escape($__vars['addOn']['version_string']) . '</strong>
				' . $__compilerTemp1 . '
			', array(
		'rowtype' => 'confirm',
	)) . '
		</div>

		' . $__templater->formSubmitRow(array(
		'submit' => 'Uninstall',
		'icon' => 'delete',
	), array(
		'rowtype' => 'simple',
	)) . '
	</div>

	' . $__templater->func('redirect_input', array(null, null, true)) . '

', array(
		'action' => $__templater->func('link', array('add-ons/uninstall', $__vars['addOn'], ), false),
		'class' => 'block',
	));
	return $__finalCompiled;
});