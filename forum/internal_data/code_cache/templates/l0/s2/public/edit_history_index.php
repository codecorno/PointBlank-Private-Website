<?php
// FROM HASH: 956a7ecfb9039af9bc2099d591e17f83
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped($__templater->escape($__vars['title']) . ' - ' . 'Edit history');
	$__finalCompiled .= '

';
	$__templater->breadcrumbs($__vars['breadcrumbs']);
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	if ($__templater->isTraversable($__vars['editHistory'])) {
		foreach ($__vars['editHistory'] AS $__vars['historyId'] => $__vars['history']) {
			$__compilerTemp1 .= '
					' . $__templater->dataRow(array(
				'rowclass' => 'dataList-row--noHover',
			), array(array(
				'class' => 'dataList-cell--min dataList-cell--alt dataList-cell--action',
				'_type' => 'cell',
				'html' => '
							<label><input type="radio" name="old" value="' . $__templater->escape($__vars['historyId']) . '" ' . (($__vars['oldId'] == $__vars['historyId']) ? 'checked' : '') . ' /></label>
						',
			),
			array(
				'class' => 'dataList-cell--min dataList-cell--alt dataList-cell--action',
				'_type' => 'cell',
				'html' => '
							<label><input type="radio" name="new" value="' . $__templater->escape($__vars['historyId']) . '" /></label>
						',
			),
			array(
				'_type' => 'cell',
				'html' => '
							' . $__templater->func('date_dynamic', array($__vars['history']['edit_date'], array(
			))) . '
						',
			),
			array(
				'_type' => 'cell',
				'html' => '
							' . $__templater->func('username_link', array($__vars['history']['User'], false, array(
			))) . '
						',
			),
			array(
				'href' => $__templater->func('link', array('edit-history/view', $__vars['history'], ), false),
				'overlay' => 'true',
				'_type' => 'action',
				'html' => '
							' . 'View' . '
						',
			))) . '
				';
		}
	}
	$__compilerTemp2 = '';
	if ($__vars['editCount'] AND ($__vars['editCount'] > $__templater->method($__vars['editHistory'], 'count', array()))) {
		$__compilerTemp2 .= '
					' . $__templater->dataRow(array(
		), array(array(
			'class' => 'dataList-cell--alt',
			'colspan' => '5',
			'_type' => 'cell',
			'html' => '
							' . 'This content has been edited a total of ' . $__templater->filter($__vars['editCount'], array(array('number', array()),), true) . ' times. Some older edit history records have been removed.' . '
						',
		))) . '
				';
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
		'width' => '30%',
		'_type' => 'cell',
		'html' => 'Edit date',
	),
	array(
		'colspan' => '2',
		'_type' => 'cell',
		'html' => 'Member',
	))) . '
				' . $__templater->dataRow(array(
		'rowclass' => 'dataList-row--noHover',
	), array(array(
		'class' => 'dataList-cell--min dataList-cell--alt dataList-cell--action',
		'_type' => 'cell',
		'html' => '
						&nbsp;
					',
	),
	array(
		'class' => 'dataList-cell--min dataList-cell--alt dataList-cell--action',
		'_type' => 'cell',
		'html' => '
						<label><input type="radio" name="new" value="0" checked="true" /></label>
					',
	),
	array(
		'colspan' => '3',
		'_type' => 'cell',
		'html' => '
						<em>' . 'Current version' . '</em>
					',
	))) . '
				' . $__compilerTemp1 . '
				' . $__compilerTemp2 . '
			', array(
	)) . '
		</div>
		<div class="block-footer">
			<span class="block-footer-controls">
				' . $__templater->button('Compare versions', array(
		'type' => 'submit',
		'name' => 'compare',
		'value' => '1',
		'class' => 'button--primary',
	), '', array(
	)) . '
			</span>
		</div>
	</div>
	' . $__templater->formHiddenVal('content_type', $__vars['contentType'], array(
	)) . '
	' . $__templater->formHiddenVal('content_id', $__vars['contentId'], array(
	)) . '
', array(
		'action' => $__templater->func('link', array('edit-history', ), false),
		'class' => 'block',
		'ajax' => 'true',
	));
	return $__finalCompiled;
});