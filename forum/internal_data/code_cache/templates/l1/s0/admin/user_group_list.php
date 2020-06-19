<?php
// FROM HASH: e744afb67ee140f3dc3e27aa985ca5ca
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('User groups');
	$__finalCompiled .= '

';
	$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('
		' . 'Add user group' . '
	', array(
		'href' => $__templater->func('link', array('user-groups/add', ), false),
		'icon' => 'add',
	), '', array(
	)) . '
');
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	if ($__templater->isTraversable($__vars['userGroups'])) {
		foreach ($__vars['userGroups'] AS $__vars['userGroup']) {
			$__compilerTemp1 .= '
					' . $__templater->dataRow(array(
			), array(array(
				'hash' => $__vars['userGroup']['user_group_id'],
				'href' => $__templater->func('link', array('user-groups/edit', $__vars['userGroup'], ), false),
				'label' => $__templater->escape($__vars['userGroup']['title']),
				'hint' => $__templater->escape($__vars['userGroup']['user_title']),
				'_type' => 'main',
				'html' => '',
			),
			array(
				'href' => $__templater->func('link', array('user-groups/delete', $__vars['userGroup'], ), false),
				'_type' => 'delete',
				'html' => '',
			))) . '
				';
		}
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-outer">
		' . $__templater->callMacro('filter_macros', 'quick_filter', array(
		'key' => 'user-groups',
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
			<span class="block-footer-counter">' . $__templater->func('display_totals', array($__templater->func('count', array($__vars['userGroups'], ), false), ), true) . '</span>
		</div>
	</div>
', array(
		'action' => $__templater->func('link', array('user-groups/toggle', ), false),
		'class' => 'block',
		'ajax' => 'true',
	));
	return $__finalCompiled;
});