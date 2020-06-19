<?php
// FROM HASH: c6d2f5f9fcc74c23fbba5ee15fb0f9bd
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped($__templater->escape($__vars['style']['title']) . ' - ' . 'Style properties');
	$__finalCompiled .= '

';
	$__templater->setPageParam('breadcrumbPath', 'styles');
	$__finalCompiled .= '
';
	$__templater->setPageParam('section', 'styleProperties');
	$__finalCompiled .= '

';
	if ($__templater->method($__vars['style'], 'canEditStylePropertyDefinitions', array())) {
		$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('
		' . 'Add property group' . '
	', array(
			'href' => $__templater->func('link', array('style-properties/groups/add', null, array('style_id' => $__vars['style']['style_id'], ), ), false),
			'icon' => 'add',
		), '', array(
		)) . '
');
	}
	$__finalCompiled .= '

<div class="block">
	<div class="block-outer">
		' . $__templater->callMacro('style_macros', 'style_change_menu', array(
		'styleTree' => $__vars['styleTree'],
		'currentStyle' => $__vars['style'],
		'route' => 'styles/style-properties',
	), $__vars) . '
	</div>
	<div class="block-container">
		<div class="block-body">
			';
	$__compilerTemp1 = '';
	if ($__templater->isTraversable($__vars['groups'])) {
		foreach ($__vars['groups'] AS $__vars['group']) {
			$__compilerTemp1 .= '
					';
			$__vars['canEdit'] = ($__templater->method($__vars['group'], 'canEdit', array()) AND ($__vars['group']['style_id'] == $__vars['style']['style_id']));
			$__compilerTemp1 .= '
					';
			$__compilerTemp2 = array();
			if ($__vars['canEdit']) {
				$__compilerTemp2[] = array(
					'href' => $__templater->func('link', array('style-properties/groups/edit', $__vars['group'], ), false),
					'_type' => 'action',
					'html' => 'Edit',
				);
			}
			$__compilerTemp1 .= $__templater->dataRow(array(
				'label' => $__templater->escape($__vars['group']['title']),
				'href' => $__templater->func('link', array('styles/style-properties/group', $__vars['style'], array('group' => $__vars['group']['group_name'], ), ), false),
				'explain' => $__templater->escape($__vars['group']['description']),
				'delete' => ($__vars['canEdit'] ? $__templater->func('link', array('style-properties/groups/delete', $__vars['group'], ), false) : false),
				'hash' => $__vars['group']['group_name'],
				'colspan' => ($__vars['canEdit'] ? 1 : 3),
			), $__compilerTemp2) . '
				';
		}
	}
	$__compilerTemp3 = '';
	if ($__vars['hasUngrouped']) {
		$__compilerTemp3 .= '
					' . $__templater->dataRow(array(
			'label' => 'Ungrouped properties',
			'href' => $__templater->func('link', array('styles/style-properties/group', $__vars['style'], array('ungrouped' => 1, ), ), false),
			'colspan' => '3',
		), array()) . '
				';
	}
	$__finalCompiled .= $__templater->dataList('
				' . $__compilerTemp1 . '

				' . $__compilerTemp3 . '
			', array(
	)) . '
		</div>
	</div>
</div>';
	return $__finalCompiled;
});