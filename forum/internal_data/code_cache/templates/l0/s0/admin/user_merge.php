<?php
// FROM HASH: bba9ddd24964d585566ba9cccd17f85f
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Merge users');
	$__finalCompiled .= '

' . $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formRow('
				' . $__templater->escape($__vars['user']['username']) . '
			', array(
		'label' => 'Source user',
		'explain' => 'This user will be deleted.',
	)) . '

			' . $__templater->formTextBoxRow(array(
		'name' => 'username',
		'ac' => 'single',
	), array(
		'label' => 'Target user',
		'explain' => '' . $__templater->escape($__vars['user']['username']) . ' will be merged into this user and all of ' . $__templater->escape($__vars['user']['username']) . '\'s content will now belong to this user.',
	)) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'submit' => 'Merge',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('users/merge', $__vars['user'], ), false),
		'ajax' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});