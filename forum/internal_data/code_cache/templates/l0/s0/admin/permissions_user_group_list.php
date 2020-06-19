<?php
// FROM HASH: dc97110f54c433ea0c5cf59eeac47c8a
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('User group permissions');
	$__finalCompiled .= '

';
	if (!$__templater->test($__vars['userGroups'], 'empty', array())) {
		$__finalCompiled .= '
	<div class="block">
		<div class="block-outer">
			' . $__templater->callMacro('filter_macros', 'quick_filter', array(
			'key' => 'user-groups',
			'class' => 'block-outer-opposite',
		), $__vars) . '
		</div>
		<div class="block-container">
			<div class="block-body">
				';
		$__compilerTemp1 = '';
		if ($__templater->isTraversable($__vars['userGroups'])) {
			foreach ($__vars['userGroups'] AS $__vars['userGroup']) {
				$__compilerTemp1 .= '
						' . $__templater->dataRow(array(
				), array(array(
					'hash' => $__vars['userGroup']['user_group_id'],
					'href' => $__templater->func('link', array('permissions/user-groups', $__vars['userGroup'], ), false),
					'label' => $__templater->escape($__vars['userGroup']['title']),
					'hint' => $__templater->escape($__vars['userGroup']['user_title']),
					'_type' => 'main',
					'html' => '',
				),
				array(
					'href' => $__templater->func('link', array('user-groups/edit', $__vars['userGroup'], ), false),
					'_type' => 'action',
					'html' => 'Group info' . '
							',
				))) . '
					';
			}
		}
		$__finalCompiled .= $__templater->dataList('
					' . $__compilerTemp1 . '
				', array(
		)) . '
			</div>
			<div class="block-footer">
				<span class="block-footer-counter">' . $__templater->func('display_totals', array($__vars['userGroups'], ), true) . '</span>
			</div>
		</div>
	</div>
';
	}
	return $__finalCompiled;
});