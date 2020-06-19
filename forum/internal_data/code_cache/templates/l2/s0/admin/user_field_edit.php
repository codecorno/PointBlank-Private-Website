<?php
// FROM HASH: 1b96fb1a84ad330de40c8610f4cbcb7f
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__compilerTemp1 = $__vars;
	$__compilerTemp1['extraOptions'] = $__templater->preEscaped('
		' . $__templater->callMacro('base_custom_field_macros', 'common_options', array(
		'field' => $__vars['field'],
		'supportsUserEditable' => true,
		'supportsEditableOnce' => true,
		'supportsModeratorEditable' => true,
	), $__vars) . '

		' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'show_registration',
		'selected' => $__vars['field']['show_registration'],
		'label' => 'Mostrar durante o registro',
		'hint' => 'Os campos obrigatórios serão sempre mostrados durante o registro.',
		'_type' => 'option',
	),
	array(
		'name' => 'viewable_profile',
		'selected' => $__vars['field']['viewable_profile'],
		'label' => 'Visível em páginas de perfil',
		'hint' => 'Isso não se aplica aos campos exibidos na página de preferências.',
		'_type' => 'option',
	),
	array(
		'name' => 'viewable_message',
		'selected' => $__vars['field']['viewable_message'],
		'label' => 'Visível na mensagem info do usuário',
		'hint' => 'Este campo só será mostrado na mensagem info do usuário se a propriedade de estilo \'Mostrar campos personalizados\' estiver ativada dentro do grupo \'Elementos da mensagem\'.',
		'_type' => 'option',
	)), array(
	)) . '
	');
	$__finalCompiled .= $__templater->includeTemplate('base_custom_field_edit', $__compilerTemp1);
	return $__finalCompiled;
});