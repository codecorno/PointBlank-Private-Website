<?php
// FROM HASH: 8d147900eda2c7ac637a63f988d97f64
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('BB code button manager');
	$__finalCompiled .= '

';
	$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('Add dropdown', array(
		'href' => $__templater->func('link', array('button-manager/dropdown/add', ), false),
		'icon' => 'add',
	), '', array(
	)) . '
');
	$__finalCompiled .= '

<div class="block">
	<div class="block-container">
		<h2 class="block-header">
			' . 'Editor toolbars' . '
			<span class="block-desc">' . 'The rich-text editor supports a total of four possible button configurations which are applied depending on the device viewport size.' . '</span>
		</h2>
		<div class="block-body">
			';
	$__compilerTemp1 = '';
	if ($__templater->isTraversable($__vars['toolbarTypes'])) {
		foreach ($__vars['toolbarTypes'] AS $__vars['type'] => $__vars['phrases']) {
			$__compilerTemp1 .= '
					' . $__templater->dataRow(array(
				'label' => $__templater->escape($__vars['phrases']['title']),
				'explain' => $__templater->escape($__vars['phrases']['description']),
				'href' => $__templater->func('link', array('button-manager/edit', null, array('type' => $__vars['type'], ), ), false),
			), array()) . '
				';
		}
	}
	$__finalCompiled .= $__templater->dataList('
				' . $__compilerTemp1 . '
			', array(
	)) . '
		</div>
	</div>
</div>

';
	if (!$__templater->test($__vars['editorDropdowns'], 'empty', array())) {
		$__finalCompiled .= '
	';
		$__compilerTemp2 = '';
		if ($__templater->isTraversable($__vars['editorDropdowns'])) {
			foreach ($__vars['editorDropdowns'] AS $__vars['editorDropdown']) {
				$__compilerTemp2 .= '
						' . $__templater->dataRow(array(
					'label' => $__templater->escape($__vars['editorDropdown']['title']),
					'href' => $__templater->func('link', array('button-manager/dropdown/edit', $__vars['editorDropdown'], ), false),
					'icon' => ($__vars['editorDropdown']['icon'] ?: 'none'),
					'delete' => $__templater->func('link', array('button-manager/dropdown/delete', $__vars['editorDropdown'], ), false),
				), array(array(
					'name' => 'active[' . $__vars['editorDropdown']['cmd'] . ']',
					'selected' => $__vars['editorDropdown']['active'],
					'class' => 'dataList-cell--separated',
					'submit' => 'true',
					'tooltip' => 'Enable / disable \'' . $__vars['editorDropdown']['title'] . '\'',
					'_type' => 'toggle',
					'html' => '',
				))) . '
					';
			}
		}
		$__finalCompiled .= $__templater->form('
		<div class="block-container">
			<h2 class="block-header">
				' . 'Editor dropdowns' . '
			</h2>
			<div class="block-body">
				' . $__templater->dataList('
					' . $__compilerTemp2 . '
				', array(
		)) . '
			</div>
		</div>
	', array(
			'action' => $__templater->func('link', array('button-manager/dropdown/toggle', ), false),
			'class' => 'block',
			'ajax' => 'true',
		)) . '
';
	}
	return $__finalCompiled;
});