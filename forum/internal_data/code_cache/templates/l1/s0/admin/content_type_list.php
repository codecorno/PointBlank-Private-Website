<?php
// FROM HASH: a05f70598ed6d914cb5a750c2116e8c0
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Content types');
	$__finalCompiled .= '

';
	$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('Add content type field', array(
		'href' => $__templater->func('link', array('content-types/add', ), false),
		'icon' => 'add',
	), '', array(
	)) . '
');
	$__finalCompiled .= '

';
	if ($__templater->isTraversable($__vars['fieldsGrouped'])) {
		foreach ($__vars['fieldsGrouped'] AS $__vars['contentType'] => $__vars['fields']) {
			$__finalCompiled .= '
	<div class="block">
		<div class="block-container">
			<h2 class="block-header">' . $__templater->escape($__vars['contentType']) . '</h2>
			<div class="block-body">
				';
			$__compilerTemp1 = '';
			if ($__templater->isTraversable($__vars['fields'])) {
				foreach ($__vars['fields'] AS $__vars['field']) {
					$__compilerTemp1 .= '
					' . $__templater->dataRow(array(
						'href' => $__templater->func('link', array('content-types/edit', $__vars['field'], ), false),
						'label' => $__templater->escape($__vars['field']['field_name']),
						'hint' => $__templater->escape($__vars['field']['field_value']),
						'hash' => $__vars['field']['content_type'] . '_' . $__vars['field']['field_name'],
						'dir' => 'auto',
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
		}
	}
	return $__finalCompiled;
});