<?php
// FROM HASH: 9a1eea3e76a526ce314ece4fc9202283
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Code events');
	$__finalCompiled .= '

';
	$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('Add code event', array(
		'href' => $__templater->func('link', array('code-events/add', ), false),
		'icon' => 'add',
	), '', array(
	)) . '
');
	$__finalCompiled .= '

';
	if (!$__templater->test($__vars['events'], 'empty', array())) {
		$__finalCompiled .= '
	';
		$__compilerTemp1 = '';
		if ($__templater->isTraversable($__vars['events'])) {
			foreach ($__vars['events'] AS $__vars['event']) {
				$__compilerTemp1 .= '
						' . $__templater->dataRow(array(
					'hash' => $__vars['event']['event_id'],
					'href' => $__templater->func('link', array('code-events/edit', $__vars['event'], ), false),
					'label' => $__templater->escape($__vars['event']['event_id']),
					'delete' => $__templater->func('link', array('code-events/delete', $__vars['event'], ), false),
					'dir' => 'auto',
				), array()) . '
					';
			}
		}
		$__finalCompiled .= $__templater->form('
		<div class="block-outer">
			' . $__templater->callMacro('filter_macros', 'quick_filter', array(
			'key' => 'code-events',
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
				<span class="block-footer-counter">' . $__templater->func('display_totals', array($__vars['events'], ), true) . '</span>
			</div>
		</div>
	', array(
			'action' => 'code-events/toggle',
			'class' => 'block',
			'ajax' => 'true',
		)) . '
';
	} else {
		$__finalCompiled .= '
	<div class="blockMessage">' . 'No items have been created yet.' . '</div>
';
	}
	return $__finalCompiled;
});