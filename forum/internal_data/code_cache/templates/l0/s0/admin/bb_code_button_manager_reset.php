<?php
// FROM HASH: 39cf51384669813e639d5aaa0caa5e1e
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Reset button configuration');
	$__finalCompiled .= '

' . $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formInfoRow('
				' . 'Please confirm that you want to reset the following button configuration' . $__vars['xf']['language']['label_separator'] . '
				<strong><a href="' . $__templater->func('link', array('button-manager/edit', null, array('type' => $__vars['type'], ), ), true) . '">' . $__templater->escape($__vars['typeTitle']) . '</a></strong>
			', array(
		'rowtype' => 'confirm close',
	)) . '
			' . $__templater->formInfoRow('
				<p class="block-rowMessage block-rowMessage--warning block-rowMessage--iconic">
					<strong>' . 'Note' . $__vars['xf']['language']['label_separator'] . '</strong>
					' . 'It is not possible to reverse this action. The button configuration for this toolbar will be reset to its default state.' . '
				</p>
			', array(
	)) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'icon' => 'refresh',
		'submit' => 'Reset button configuration',
	), array(
		'rowtype' => 'simple',
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('button-manager/reset', null, array('type' => $__vars['type'], ), ), false),
		'ajax' => 'true',
		'class' => 'block',
		'data-force-flash-message' => 'on',
	));
	return $__finalCompiled;
});