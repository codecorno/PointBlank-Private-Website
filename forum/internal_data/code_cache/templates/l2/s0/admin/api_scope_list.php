<?php
// FROM HASH: 2beec240ad724d1045e132afbcb70e1d
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('API scopes');
	$__finalCompiled .= '

';
	$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('Add API scope', array(
		'href' => $__templater->func('link', array('api-scopes/add', ), false),
		'icon' => 'add',
	), '', array(
	)) . '
');
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	if ($__templater->isTraversable($__vars['scopes'])) {
		foreach ($__vars['scopes'] AS $__vars['scope']) {
			$__compilerTemp1 .= '
					' . $__templater->dataRow(array(
				'href' => $__templater->func('link', array('api-scopes/edit', $__vars['scope'], ), false),
				'label' => $__templater->escape($__vars['scope']['api_scope_id']),
				'hint' => $__templater->escape($__vars['scope']['description']),
				'hash' => $__vars['scope']['api_scope_id_url'],
				'delete' => $__templater->func('link', array('api-scopes/delete', $__vars['scope'], ), false),
			), array()) . '
				';
		}
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-outer">
		' . $__templater->callMacro('filter_macros', 'quick_filter', array(
		'key' => 'api-scopes',
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
			<span class="block-footer-counter">' . $__templater->func('display_totals', array($__vars['scopes'], ), true) . '</span>
		</div>
	</div>
', array(
		'action' => '',
		'class' => 'block',
	));
	return $__finalCompiled;
});