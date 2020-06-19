<?php
// FROM HASH: 84687b08e1697f07078849e525204f09
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Confirm action');
	$__finalCompiled .= '

' . $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formInfoRow('
				' . 'Please confirm that you want to reset the following notice' . $__vars['xf']['language']['label_separator'] . '
				<strong><a href="' . $__templater->func('link', array('notices/edit', $__vars['notice'], ), true) . '">' . $__templater->escape($__vars['notice']['title']) . '</a></strong>
				' . 'Resetting this notice will display it to all of the users matching the notice criteria, even if they have previously dismissed it. Note that this will not restore the notice to guests who have dismissed it.' . '
			', array(
		'rowtype' => 'confirm',
	)) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'submit' => 'Reset',
	), array(
		'rowtype' => 'simple',
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('notices/reset', $__vars['notice'], ), false),
		'ajax' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});