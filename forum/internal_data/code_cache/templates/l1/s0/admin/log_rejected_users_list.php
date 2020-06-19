<?php
// FROM HASH: cc5d8b4f28d6fec9057fe002c639a7df
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Rejected user log');
	$__finalCompiled .= '

<div class="block">
	<div class="block-container">
		<div class="block-body">
			';
	$__compilerTemp1 = '';
	$__compilerTemp2 = true;
	if ($__templater->isTraversable($__vars['rejections'])) {
		foreach ($__vars['rejections'] AS $__vars['rejection']) {
			$__compilerTemp2 = false;
			$__compilerTemp1 .= '
					';
			$__compilerTemp3 = '';
			if ($__vars['rejection']['reject_user_id']) {
				$__compilerTemp3 .= '
								' . $__templater->func('username_link', array($__vars['rejection']['RejectUser'], false, array(
					'href' => $__templater->func('link', array('users/edit', $__vars['rejection']['RejectUser'], ), false),
				))) . '
							';
			} else {
				$__compilerTemp3 .= '
								' . 'N/A' . '
							';
			}
			$__compilerTemp1 .= $__templater->dataRow(array(
			), array(array(
				'class' => 'dataList-cell--min dataList-cell--image dataList-cell--imageSmall',
				'href' => $__templater->func('link', array('users/edit', $__vars['rejection']['User'], ), false),
				'_type' => 'cell',
				'html' => '
							' . $__templater->func('avatar', array($__vars['rejection']['User'], 's', false, array(
				'href' => '',
			))) . '
						',
			),
			array(
				'_type' => 'cell',
				'html' => '
							<div>
								' . $__templater->func('username_link', array($__vars['rejection']['User'], false, array(
				'href' => $__templater->func('link', array('users/edit', $__vars['rejection']['User'], ), false),
			))) . '
							</div>
						',
			),
			array(
				'_type' => 'cell',
				'html' => '
							' . $__templater->func('date_dynamic', array($__vars['rejection']['reject_date'], array(
			))) . '
						',
			),
			array(
				'_type' => 'cell',
				'html' => '
							' . $__compilerTemp3 . '
						',
			),
			array(
				'href' => $__templater->func('link', array('logs/rejected-users', $__vars['rejection']['User'], ), false),
				'overlay' => 'true',
				'_type' => 'action',
				'html' => '
							' . 'View' . '
						',
			))) . '
					';
		}
	}
	if ($__compilerTemp2) {
		$__compilerTemp1 .= '
					' . $__templater->dataRow(array(
		), array(array(
			'colspan' => '5',
			'_type' => 'cell',
			'html' => 'No entries have been logged.',
		))) . '
				';
	}
	$__finalCompiled .= $__templater->dataList('
				' . $__templater->dataRow(array(
		'rowtype' => 'header',
	), array(array(
		'colspan' => '2',
		'_type' => 'cell',
		'html' => 'Rejected user',
	),
	array(
		'_type' => 'cell',
		'html' => 'Date',
	),
	array(
		'_type' => 'cell',
		'html' => 'Rejected by',
	),
	array(
		'class' => 'dataList-cell--min',
		'_type' => 'cell',
		'html' => '&nbsp;',
	))) . '

				' . $__compilerTemp1 . '
			', array(
		'data-xf-init' => 'responsive-data-list',
	)) . '
		</div>
		<div class="block-footer">
			<span class="block-footer-counter">' . $__templater->func('display_totals', array($__vars['rejections'], $__vars['total'], ), true) . '</span>
		</div>
	</div>
	' . $__templater->func('page_nav', array(array(
		'page' => $__vars['page'],
		'total' => $__vars['total'],
		'link' => 'logs/rejected-users',
		'params' => $__vars['linkFilters'],
		'wrapperclass' => 'block-outer block-outer--after',
		'perPage' => $__vars['perPage'],
	))) . '
</div>
';
	return $__finalCompiled;
});