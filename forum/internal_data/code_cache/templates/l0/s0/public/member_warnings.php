<?php
// FROM HASH: 822e3a0b07b9b2878659443ba310b7dc
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Warnings' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['user']['username']));
	$__finalCompiled .= '

';
	$__templater->breadcrumb($__templater->preEscaped($__templater->escape($__vars['user']['username'])), $__templater->func('link', array('members', $__vars['user'], ), false), array(
	));
	$__finalCompiled .= '

<div class="block">
	<div class="block-container">
		<div class="block-body">
			';
	$__compilerTemp1 = '';
	if ($__templater->isTraversable($__vars['warnings'])) {
		foreach ($__vars['warnings'] AS $__vars['warning']) {
			$__compilerTemp1 .= '
					';
			$__compilerTemp2 = '';
			if ($__vars['warning']['expiry_date']) {
				$__compilerTemp2 .= '
								' . $__templater->func('date_dynamic', array($__vars['warning']['expiry_date'], array(
				))) . '
							';
			} else {
				$__compilerTemp2 .= '
								' . 'N/A' . '
							';
			}
			$__compilerTemp1 .= $__templater->dataRow(array(
				'rowclass' => ($__vars['warning']['is_expired'] ? 'dataList-row--disabled' : ''),
			), array(array(
				'href' => $__templater->func('link', array('warnings', $__vars['warning'], ), false),
				'overlay' => 'true',
				'_type' => 'cell',
				'html' => $__templater->escape($__vars['warning']['title']),
			),
			array(
				'href' => $__templater->func('link', array('warnings', $__vars['warning'], ), false),
				'overlay' => 'true',
				'_type' => 'cell',
				'html' => $__templater->func('date_dynamic', array($__vars['warning']['warning_date'], array(
			))),
			),
			array(
				'href' => $__templater->func('link', array('warnings', $__vars['warning'], ), false),
				'overlay' => 'true',
				'_type' => 'cell',
				'html' => $__templater->filter($__vars['warning']['points'], array(array('number', array()),), true),
			),
			array(
				'href' => $__templater->func('link', array('warnings', $__vars['warning'], ), false),
				'overlay' => 'true',
				'_type' => 'cell',
				'html' => '
							' . $__compilerTemp2 . '
						',
			))) . '
				';
		}
	}
	$__finalCompiled .= $__templater->dataList('
				' . $__templater->dataRow(array(
		'rowtype' => 'header',
	), array(array(
		'_type' => 'cell',
		'html' => 'Warning',
	),
	array(
		'_type' => 'cell',
		'html' => 'Date',
	),
	array(
		'_type' => 'cell',
		'html' => 'Points',
	),
	array(
		'_type' => 'cell',
		'html' => 'Expiry',
	))) . '
				' . $__compilerTemp1 . '
			', array(
		'data-xf-init' => 'responsive-data-list',
	)) . '
		</div>
		<div class="block-footer">
			<ul class="listInline listInline--bullet">
				<li>' . 'Warning points' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->filter($__vars['user']['warning_points'], array(array('number', array()),), true) . '</li>
				<li>' . 'Total warnings' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->filter($__templater->func('count', array($__vars['warnings'], ), false), array(array('number', array()),), true) . '</li>
			</ul>
		</div>
	</div>
</div>';
	return $__finalCompiled;
});