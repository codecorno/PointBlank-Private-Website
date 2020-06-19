<?php
// FROM HASH: 1357ffdb245356d57b9710fbbd52484b
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Confirm action');
	$__finalCompiled .= '

' . $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formInfoRow('
				' . 'This feature will attempt to automatically merge your custom template changes with any updates to the template they are based on. As this is an automated process, it is recommended that you check your customized templates after merging to ensure that they are correct.' . '
				<strong>' . 'If a conflict is discovered while attempting to merge, no automatic merging will happen. You will need to manually resolve the conflict.' . '</strong>
			', array(
		'rowtype' => 'confirm',
	)) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'submit' => 'Merge',
	), array(
		'rowtype' => 'simple',
	)) . '
	</div>
	' . $__templater->func('redirect_input', array(null, null, true)) . '
', array(
		'action' => $__templater->func('link', array('templates/auto-merge', ), false),
		'ajax' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});