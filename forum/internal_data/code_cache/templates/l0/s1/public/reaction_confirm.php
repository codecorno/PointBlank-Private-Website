<?php
// FROM HASH: f1751dfe68e7ca1f9276a0d8d6cb48ff
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Confirm action');
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	if ($__vars['isUnreact']) {
		$__compilerTemp1 .= '
					' . 'Are you sure you want to remove your reaction?' . '
				';
	} else {
		$__compilerTemp1 .= '
					' . 'Are you sure you want to apply this reaction?' . '
					<strong>' . $__templater->func('reaction', array(array(
			'id' => $__vars['reaction']['reaction_id'],
			'showtitle' => 'true',
			'class' => 'reaction--inline',
		))) . '</strong>
				';
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formInfoRow('
				' . $__compilerTemp1 . '
			', array(
		'rowtype' => 'confirm',
	)) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'icon' => 'confirm',
	), array(
		'rowtype' => 'simple',
	)) . '
	</div>
	' . $__templater->formHiddenVal('reaction_id', $__vars['reaction']['reaction_id'], array(
	)) . '
', array(
		'action' => $__vars['confirmUrl'],
		'class' => 'block',
	));
	return $__finalCompiled;
});