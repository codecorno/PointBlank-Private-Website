<?php
// FROM HASH: c561c656cac0645ca1618259f9c0636a
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<hr class="formRowSep" />

' . $__templater->formCodeEditorRow(array(
		'name' => 'options[template]',
		'value' => $__vars['template']['template'],
		'class' => 'codeEditor--short',
		'mode' => 'html',
	), array(
		'label' => 'Template',
		'explain' => 'Você pode usar a sintaxe do modelo XenForo aqui.',
	)) . '

' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'options[advanced_mode]',
		'value' => '1',
		'selected' => $__vars['options']['advanced_mode'],
		'label' => 'Modo avançado',
		'hint' => 'Se ativado, o HTML para sua página não será contido dentro de um bloco.',
		'_type' => 'option',
	)), array(
	)) . '

';
	if ($__vars['options']['template_title']) {
		$__finalCompiled .= '
	' . $__templater->formHiddenVal('options[template_title]', $__vars['options']['template_title'], array(
		)) . '
';
	}
	return $__finalCompiled;
});