<?php
// FROM HASH: 3469326038028b9e7481d15b743bf648
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Warnings');
	$__finalCompiled .= '

';
	$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('Add warning', array(
		'href' => $__templater->func('link', array('warnings/add', ), false),
		'icon' => 'add',
	), '', array(
	)) . '
	' . $__templater->button('Add warning action', array(
		'href' => $__templater->func('link', array('warnings/actions/add', ), false),
		'icon' => 'add',
	), '', array(
	)) . '
');
	$__finalCompiled .= '

<div class="block">
	<div class="block-outer">
		' . $__templater->callMacro('filter_macros', 'quick_filter', array(
		'key' => 'warnings',
		'class' => 'block-outer-opposite',
	), $__vars) . '
	</div>
	<div class="block-container">
		<div class="block-body">
			';
	$__compilerTemp1 = '';
	if ($__templater->isTraversable($__vars['warnings'])) {
		foreach ($__vars['warnings'] AS $__vars['warning']) {
			$__compilerTemp1 .= '
					' . $__templater->dataRow(array(
				'label' => $__templater->escape($__vars['warning']['title']),
				'href' => $__templater->func('link', array('warnings/edit', $__vars['warning'], ), false),
				'delete' => $__templater->func('link', array('warnings/delete', $__vars['warning'], ), false),
			), array()) . '
				';
		}
	}
	$__finalCompiled .= $__templater->dataList('
				' . $__compilerTemp1 . '
			', array(
	)) . '
		</div>
		<div class="block-footer">
			<span class="block-footer-counter">' . $__templater->func('display_totals', array($__vars['warnings'], ), true) . '</span>
		</div>
	</div>
</div>

';
	if (!$__templater->test($__vars['actions'], 'empty', array())) {
		$__finalCompiled .= '
	<div class="block">
		<div class="block-container">
			<h2 class="block-header">' . 'Warning actions' . '</h2>
			<div class="block-body">
				';
		$__compilerTemp2 = '';
		if ($__templater->isTraversable($__vars['actions'])) {
			foreach ($__vars['actions'] AS $__vars['action']) {
				$__compilerTemp2 .= '
						';
				$__compilerTemp3 = '';
				if ($__vars['action']['action'] == 'ban') {
					$__compilerTemp3 .= '
												<li>' . 'Ban' . '</li>
											';
				} else if ($__vars['action']['action'] == 'discourage') {
					$__compilerTemp3 .= '
												<li>' . 'Discourage' . '</li>
											';
				} else if ($__vars['action']['action'] == 'groups') {
					$__compilerTemp3 .= '
												<li>' . 'Add to selected groups' . '</li>
											';
				} else {
					$__compilerTemp3 .= '
												<li>' . 'Unknown action' . '</li>
											';
				}
				$__compilerTemp4 = '';
				if ($__vars['action']['action_length_type'] == 'permanent') {
					$__compilerTemp4 .= '
												<li>' . 'Permanent' . '</li>
											';
				} else if ($__vars['action']['action_length_type'] == 'points') {
					$__compilerTemp4 .= '
												<li>' . 'While at or above points threshold' . '</li>
											';
				} else {
					$__compilerTemp4 .= '
												<li>' . 'Temporary' . '</li>
											';
				}
				$__compilerTemp2 .= $__templater->dataRow(array(
				), array(array(
					'href' => $__templater->func('link', array('warnings/actions/edit', $__vars['action'], ), false),
					'_type' => 'cell',
					'html' => '
								<div class="dataList-mainRow">
									' . 'Points' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['action']['points']) . '
									<div class="dataList-hint" dir="auto">
										<ul class="listInline listInline--bullet listInline--selfInline">
											' . $__compilerTemp3 . '

											' . $__compilerTemp4 . '
										</ul>
									</div>
								</div>
							',
				),
				array(
					'href' => $__templater->func('link', array('warnings/actions/delete', $__vars['action'], ), false),
					'_type' => 'delete',
					'html' => '',
				))) . '
					';
			}
		}
		$__finalCompiled .= $__templater->dataList('
					' . $__compilerTemp2 . '
				', array(
		)) . '
			</div>
			<!--<div class="block-footer">
				<span class="block-footer-counter">' . $__templater->func('display_totals', array($__vars['actions'], ), true) . '</span>
			</div>-->
		</div>
	</div>
';
	}
	return $__finalCompiled;
});