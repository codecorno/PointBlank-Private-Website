<?php
// FROM HASH: 1f18d0f4c374738a8eeb4c1cf9f24578
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Confirm action');
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	if ($__vars['phrase']['language_id']) {
		$__compilerTemp1 .= '
					' . 'Please confirm that you want to revert the changes made to the following' . $__vars['xf']['language']['label_separator'] . '
				';
	} else {
		$__compilerTemp1 .= '
					' . 'Please confirm that you want to delete the following' . $__vars['xf']['language']['label_separator'] . '
				';
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formInfoRow('
				' . $__compilerTemp1 . '
				<strong>
					' . ($__vars['phrase']['language_id'] ? (('<em class="u-dimmed">' . $__templater->escape($__vars['phrase']['Language']['title'])) . ':</em> ') : '') . '
					<a href="' . $__templater->func('link', array('phrases/edit', $__vars['phrase'], ), true) . '">' . $__templater->escape($__vars['phrase']['title']) . '</a>
				</strong>
			', array(
		'rowtype' => 'confirm',
	)) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'submit' => ($__vars['phrase']['language_id'] ? 'Revert' : 'Delete'),
		'icon' => ((!$__vars['phrase']['language_id']) ? 'delete' : ''),
	), array(
		'rowtype' => 'simple',
	)) . '
	</div>
	' . $__templater->func('redirect_input', array(null, null, true)) . '
', array(
		'action' => $__templater->func('link', array('phrases/delete', $__vars['phrase'], ), false),
		'ajax' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});