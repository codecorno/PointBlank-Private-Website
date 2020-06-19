<?php
// FROM HASH: 1867b6c79d3e5959d9ec7a98175bb53d
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Options');
	$__finalCompiled .= '

';
	if ($__vars['canAdd']) {
		$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('Add option group', array(
			'href' => $__templater->func('link', array('options/groups/add', ), false),
			'icon' => 'add',
		), '', array(
		)) . '
');
	}
	$__finalCompiled .= '

<div class="block">
	<div class="block-outer">
		' . $__templater->callMacro('filter_macros', 'quick_filter', array(
		'key' => 'options',
		'class' => 'block-outer-opposite',
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
			$__compilerTemp2 = array();
			if ($__templater->method($__vars['group'], 'canEdit', array())) {
				$__compilerTemp2[] = array(
					'href' => $__templater->func('link', array('options/groups/edit', $__vars['group'], ), false),
					'_type' => 'action',
					'html' => 'Edit',
				);
			}
			$__compilerTemp1 .= $__templater->dataRow(array(
				'icon' => ($__vars['group']['icon'] ?: 'fa-cogs'),
				'label' => $__templater->escape($__vars['group']['title']),
				'href' => $__templater->func('link', array('options/groups', $__vars['group'], ), false),
				'explain' => $__templater->escape($__vars['group']['description']),
				'delete' => ($__templater->method($__vars['group'], 'canEdit', array()) ? $__templater->func('link', array('options/groups/delete', $__vars['group'], ), false) : false),
				'hash' => $__vars['group']['group_id'],
			), $__compilerTemp2) . '
				';
		}
	}
	$__finalCompiled .= $__templater->dataList('
				' . $__compilerTemp1 . '
			', array(
	)) . '
		</div>
		<div class="block-footer">
			<span class="block-footer-counter">' . $__templater->func('display_totals', array($__vars['groups'], ), true) . '</span>
		</div>
	</div>
</div>';
	return $__finalCompiled;
});