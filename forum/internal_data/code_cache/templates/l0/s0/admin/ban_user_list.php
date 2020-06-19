<?php
// FROM HASH: 5443f04e25f3b948520b1b7bf577ddc2
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Banned users');
	$__finalCompiled .= '

';
	$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('Ban user', array(
		'href' => $__templater->func('link', array('banning/users/add', ), false),
		'icon' => 'add',
	), '', array(
	)) . '
');
	$__finalCompiled .= '

';
	if (!$__templater->test($__vars['userBans'], 'empty', array())) {
		$__finalCompiled .= '
	<div class="block">
		<div class="block-container">
			<div class="block-body">
				';
		$__compilerTemp1 = '';
		if ($__templater->isTraversable($__vars['userBans'])) {
			foreach ($__vars['userBans'] AS $__vars['userBan']) {
				$__compilerTemp1 .= '
						' . $__templater->dataRow(array(
				), array(array(
					'href' => $__templater->func('link', array('banning/users', $__vars['userBan']['User'], ), false),
					'_type' => 'cell',
					'html' => $__templater->escape($__vars['userBan']['User']['username']),
				),
				array(
					'href' => $__templater->func('link', array('banning/users', $__vars['userBan']['User'], ), false),
					'_type' => 'cell',
					'html' => ($__vars['userBan']['end_date'] ? $__templater->func('date', array($__vars['userBan']['end_date'], ), true) : 'Permanent'),
				),
				array(
					'href' => $__templater->func('link', array('banning/users', $__vars['userBan']['User'], ), false),
					'_type' => 'cell',
					'html' => ($__templater->escape($__vars['userBan']['user_reason']) ?: 'N/A'),
				),
				array(
					'href' => $__templater->func('link', array('users/edit', $__vars['userBan']['User'], ), false),
					'_type' => 'action',
					'html' => 'User',
				),
				array(
					'href' => $__templater->func('link', array('banning/users/lift', $__vars['userBan']['User'], ), false),
					'tooltip' => 'Lift ban',
					'_type' => 'delete',
					'html' => '',
				))) . '
					';
			}
		}
		$__finalCompiled .= $__templater->dataList('
					' . $__templater->dataRow(array(
			'rowtype' => 'header',
		), array(array(
			'_type' => 'cell',
			'html' => 'User name',
		),
		array(
			'_type' => 'cell',
			'html' => 'End date',
		),
		array(
			'_type' => 'cell',
			'html' => 'Reason',
		),
		array(
			'_type' => 'cell',
			'html' => '&nbsp;',
		),
		array(
			'_type' => 'cell',
			'html' => '&nbsp;',
		))) . '
					' . $__compilerTemp1 . '
				', array(
			'data-xf-init' => 'responsive-data-list',
		)) . '
			</div>
			<div class="block-footer">
				<span class="block-footer-counter">' . $__templater->func('display_totals', array($__vars['userBans'], $__vars['total'], ), true) . '</span>
			</div>
		</div>

		' . $__templater->func('page_nav', array(array(
			'page' => $__vars['page'],
			'total' => $__vars['total'],
			'link' => 'banning/users',
			'wrapperclass' => 'block-outer block-outer--after',
			'perPage' => $__vars['perPage'],
		))) . '
	</div>
';
	} else {
		$__finalCompiled .= '
	<div class="blockMessage">' . 'There are no banned users.' . '</div>
';
	}
	return $__finalCompiled;
});