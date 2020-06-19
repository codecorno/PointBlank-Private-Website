<?php
// FROM HASH: c65cd23bba4f94370e02655624991f5e
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped(($__vars['isLiked'] ? 'Unlike content' : 'Like content'));
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	if ($__vars['isLiked']) {
		$__compilerTemp1 .= '
					' . 'Are you sure you want to unlike this content?' . '
				';
	} else {
		$__compilerTemp1 .= '
					' . 'Are you sure you want to like this content?' . '
					';
		if ($__vars['contentTitle']) {
			$__compilerTemp1 .= '<strong>' . $__templater->escape($__vars['contentTitle']) . '</strong>';
		}
		$__compilerTemp1 .= '
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
', array(
		'action' => $__vars['confirmUrl'],
		'class' => 'block',
	));
	return $__finalCompiled;
});