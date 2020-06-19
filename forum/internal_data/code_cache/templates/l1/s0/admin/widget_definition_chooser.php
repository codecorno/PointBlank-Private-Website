<?php
// FROM HASH: b4c8e0f388ab57487742eb743e93ee05
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Choose a widget definition');
	$__finalCompiled .= '

';
	$__compilerTemp1 = array(array(
		'_type' => 'option',
	));
	$__compilerTemp1 = $__templater->mergeChoiceOptions($__compilerTemp1, $__vars['widgetDefinitions']);
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formSelectRow(array(
		'name' => 'definition_id',
	), $__compilerTemp1, array(
		'label' => 'Widget definition',
	)) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'submit' => 'Add widget',
		'icon' => 'add',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('widgets/add', ), false),
		'class' => 'block',
		'ajax' => 'true',
	));
	return $__finalCompiled;
});