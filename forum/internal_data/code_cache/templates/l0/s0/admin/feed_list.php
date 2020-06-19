<?php
// FROM HASH: 4b2d4705febc5794cc88cc425a0fc274
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('RSS feed importer');
	$__finalCompiled .= '

';
	$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('Add feed', array(
		'href' => $__templater->func('link', array('feeds/add', ), false),
		'icon' => 'add',
	), '', array(
	)) . '
');
	$__finalCompiled .= '

';
	if (!$__templater->test($__vars['feeds'], 'empty', array())) {
		$__finalCompiled .= '
	';
		$__compilerTemp1 = '';
		if ($__templater->isTraversable($__vars['feeds'])) {
			foreach ($__vars['feeds'] AS $__vars['feed']) {
				$__compilerTemp1 .= '
						' . $__templater->dataRow(array(
				), array(array(
					'hash' => $__vars['feed']['feed_id'],
					'href' => $__templater->func('link', array('feeds/edit', $__vars['feed'], ), false),
					'label' => $__templater->escape($__vars['feed']['title']),
					'explain' => $__templater->escape($__vars['feed']['url']),
					'_type' => 'main',
					'html' => '',
				),
				array(
					'name' => 'active[' . $__vars['feed']['feed_id'] . ']',
					'selected' => $__vars['feed']['active'],
					'class' => 'dataList-cell--separated',
					'submit' => 'true',
					'tooltip' => 'Enable / disable \'' . $__vars['feed']['title'] . '\'',
					'_type' => 'toggle',
					'html' => '',
				),
				array(
					'href' => $__templater->func('link', array('feeds/import', $__vars['feed'], ), false),
					'_type' => 'action',
					'html' => 'Import now',
				),
				array(
					'href' => $__templater->func('link', array('feeds/delete', $__vars['feed'], ), false),
					'_type' => 'delete',
					'html' => '',
				))) . '
					';
			}
		}
		$__finalCompiled .= $__templater->form('
		<div class="block-outer">
			' . $__templater->callMacro('filter_macros', 'quick_filter', array(
			'key' => 'feeds',
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
				<span class="block-footer-counter">' . $__templater->func('display_totals', array($__vars['feeds'], ), true) . '</span>
			</div>
		</div>
	', array(
			'action' => $__templater->func('link', array('feeds/toggle', ), false),
			'class' => 'block',
			'ajax' => 'true',
		)) . '
';
	} else {
		$__finalCompiled .= '
	<div class="blockMessage">' . 'No feeds have been registered yet.' . '</div>
';
	}
	return $__finalCompiled;
});