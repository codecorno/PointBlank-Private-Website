<?php
// FROM HASH: cc8f0c74d2da312606f4d62ecda836e9
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Confirmar ação');
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	if ($__templater->isTraversable($__vars['templateIds'])) {
		foreach ($__vars['templateIds'] AS $__vars['templateId']) {
			$__compilerTemp1 .= '
		' . $__templater->formHiddenVal('template_ids[]', $__vars['templateId'], array(
			)) . '
	';
		}
	}
	$__compilerTemp2 = '';
	if ($__templater->isTraversable($__vars['propertyIds'])) {
		foreach ($__vars['propertyIds'] AS $__vars['propertyId']) {
			$__compilerTemp2 .= '
		' . $__templater->formHiddenVal('property_ids[]', $__vars['propertyId'], array(
			)) . '
	';
		}
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formInfoRow('
				' . 'Confirme que deseja reverter ' . $__templater->func('count', array($__vars['templateIds'], ), true) . ' modelos e ' . $__templater->func('count', array($__vars['propertyIds'], ), true) . ' propriedades de estilo do estilo <em>' . $__templater->escape($__vars['style']['title']) . '</em> para seus valores padrão.' . '
			', array(
		'rowtype' => 'confirm',
	)) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'submit' => 'Reverter',
		'name' => 'perform_revert',
		'value' => '1',
	), array(
		'rowtype' => 'simple',
	)) . '
	</div>
	' . $__templater->func('redirect_input', array(null, null, true)) . '
	' . $__compilerTemp1 . '
	' . $__compilerTemp2 . '
', array(
		'action' => $__templater->func('link', array('styles/mass-revert', $__vars['style'], ), false),
		'ajax' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});