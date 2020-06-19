<?php
// FROM HASH: ff8ae1873192b555b90f09b42bfb465d
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Template history');
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	if ($__templater->isTraversable($__vars['list'])) {
		foreach ($__vars['list'] AS $__vars['history']) {
			$__compilerTemp1 .= '
					' . $__templater->dataRow(array(
			), array(array(
				'_type' => 'cell',
				'html' => '<input type="radio" name="old" value="' . $__templater->escape($__vars['history']['template_history_id']) . '" ' . (($__vars['oldId'] == $__vars['history']['template_history_id']) ? 'checked="checked"' : '') . ' />',
			),
			array(
				'_type' => 'cell',
				'html' => '<input type="radio" name="new" value="' . $__templater->escape($__vars['history']['template_history_id']) . '" ' . (($__vars['newId'] == $__vars['history']['template_history_id']) ? 'checked="checked"' : '') . ' />',
			),
			array(
				'_type' => 'cell',
				'html' => $__templater->func('date_dynamic', array($__vars['history']['edit_date'], array(
			))),
			),
			array(
				'href' => $__templater->func('link', array('templates/history', $__vars['template'], array('view' => $__vars['history']['template_history_id'], ), ), false),
				'overlay' => 'true',
				'_type' => 'action',
				'html' => 'View pre-edit version',
			))) . '
				';
		}
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->dataList('
				' . $__templater->dataRow(array(
		'rowtype' => 'header',
	), array(array(
		'class' => 'dataList-cell--min',
		'_type' => 'cell',
		'html' => 'Old',
	),
	array(
		'class' => 'dataList-cell--min',
		'_type' => 'cell',
		'html' => 'New',
	),
	array(
		'_type' => 'cell',
		'html' => 'Edit date',
	),
	array(
		'_type' => 'cell',
		'html' => '',
	))) . '
				' . $__templater->dataRow(array(
	), array(array(
		'_type' => 'cell',
		'html' => '',
	),
	array(
		'_type' => 'cell',
		'html' => '<input type="radio" name="new" value="0" ' . (($__vars['newId'] == 0) ? 'checked="checked"' : '') . ' />',
	),
	array(
		'_type' => 'cell',
		'html' => 'Current version',
	),
	array(
		'_type' => 'cell',
		'html' => '',
	))) . '
				' . $__compilerTemp1 . '
			', array(
		'data-xf-init' => 'responsive-data-list',
	)) . '
		</div>
		<div class="block-footer">
			<span class="block-footer-controls">' . $__templater->button('Compare', array(
		'type' => 'submit',
	), '', array(
	)) . '</span>
		</div>
	</div>
', array(
		'method' => 'post',
		'action' => $__templater->func('link', array('templates/history', $__vars['template'], ), false),
		'ajax' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});