<?php
// FROM HASH: 9c4fc4d8ee60611e6d77a186d70c943b
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Admin permissions');
	$__finalCompiled .= '

';
	$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('Add admin permission', array(
		'href' => $__templater->func('link', array('admin-permissions/add', ), false),
		'icon' => 'add',
	), '', array(
	)) . '
');
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	if ($__templater->isTraversable($__vars['permissions'])) {
		foreach ($__vars['permissions'] AS $__vars['permission']) {
			$__compilerTemp1 .= '
					' . $__templater->dataRow(array(
				'href' => $__templater->func('link', array('admin-permissions/edit', $__vars['permission'], ), false),
				'label' => $__templater->escape($__vars['permission']['title']),
				'hint' => $__templater->escape($__vars['permission']['admin_permission_id']),
				'delete' => $__templater->func('link', array('admin-permissions/delete', $__vars['permission'], ), false),
			), array()) . '
				';
		}
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-outer">
		' . $__templater->callMacro('filter_macros', 'quick_filter', array(
		'key' => 'admin-permissions',
		'class' => 'block-outer-opposite',
	), $__vars) . '
	</div>
	<div class="block-container">
		<div class="block-body">
			' . $__templater->dataList('
				' . $__compilerTemp1 . '
			', array(
	)) . '
		</div>
		<div class="block-footer">
			<span class="block-footer-counter">' . $__templater->func('display_totals', array($__vars['permissions'], ), true) . '</span>
		</div>
	</div>
', array(
		'action' => '',
		'class' => 'block',
	));
	return $__finalCompiled;
});