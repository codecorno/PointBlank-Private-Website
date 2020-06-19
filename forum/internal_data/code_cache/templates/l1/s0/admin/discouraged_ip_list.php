<?php
// FROM HASH: 2c241b3e4e478db703ca065ccfc16ba0
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Discouraged IP addresses');
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	if (!$__templater->test($__vars['discouragedIps'], 'empty', array())) {
		$__compilerTemp1 .= '
			' . $__templater->button('', array(
			'href' => $__templater->func('link', array('banning/discouraged-ips/export', null, array('t' => $__templater->func('csrf_token', array(), false), ), ), false),
			'icon' => 'export',
		), '', array(
		)) . '
		';
	}
	$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	<div class="buttonGroup">
		' . $__templater->button('', array(
		'href' => $__templater->func('link', array('banning/discouraged-ips/import', ), false),
		'icon' => 'import',
		'overlay' => 'true',
	), '', array(
	)) . '
		' . $__compilerTemp1 . '
	</div>
');
	$__finalCompiled .= '

';
	if (!$__templater->test($__vars['discouragedIps'], 'empty', array())) {
		$__finalCompiled .= '
	';
		$__compilerTemp2 = '';
		if ($__templater->isTraversable($__vars['discouragedIps'])) {
			foreach ($__vars['discouragedIps'] AS $__vars['discouragedIp']) {
				$__compilerTemp2 .= '
						' . $__templater->dataRow(array(
				), array(array(
					'name' => 'delete[]',
					'value' => $__vars['discouragedIp']['ip'],
					'_type' => 'toggle',
					'html' => '',
				),
				array(
					'class' => 'u-ltr',
					'_type' => 'cell',
					'html' => $__templater->escape($__vars['discouragedIp']['ip']),
				),
				array(
					'_type' => 'cell',
					'html' => ($__vars['discouragedIp']['create_date'] ? $__templater->func('date', array($__vars['discouragedIp']['create_date'], ), true) : 'N/A'),
				),
				array(
					'_type' => 'cell',
					'html' => '
								' . ($__vars['discouragedIp']['last_triggered_date'] ? $__templater->func('date_dynamic', array($__vars['discouragedIp']['last_triggered_date'], ), true) : 'Never') . '
							',
				),
				array(
					'_type' => 'cell',
					'html' => ($__vars['discouragedIp']['User'] ? $__templater->escape($__vars['discouragedIp']['User']['username']) : 'N/A'),
				),
				array(
					'_type' => 'cell',
					'html' => ($__vars['discouragedIp']['reason'] ? $__templater->escape($__vars['discouragedIp']['reason']) : 'N/A'),
				))) . '
					';
			}
		}
		$__finalCompiled .= $__templater->form('
		<div class="block-container">
			<div class="block-body">
				' . $__templater->dataList('
					<colgroup>
						<col style="width: 1%">
						<col style="width: 15%">
						<col style="width: 15%">
						<col style="width: 15%">
						<col style="width: 15%">
						<col>
					</colgroup>
					' . $__templater->dataRow(array(
			'rowtype' => 'header',
		), array(array(
			'_type' => 'cell',
			'html' => $__templater->formCheckBox(array(
			'standalone' => 'true',
		), array(array(
			'check-all' => '< .block-container',
			'data-xf-init' => 'tooltip',
			'title' => 'Select all',
			'_type' => 'option',
		))),
		),
		array(
			'href' => $__templater->func('link', array('banning/discouraged-ips', '', array('order' => 'start_range', 'direction' => ((($__vars['direction'] == 'desc') OR (!$__vars['direction'])) ? 'asc' : ''), ), ), false),
			'_type' => 'cell',
			'html' => '
							' . 'IP' . '
						',
		),
		array(
			'href' => $__templater->func('link', array('banning/discouraged-ips', '', array('order' => '', 'direction' => ((($__vars['direction'] == 'desc') OR (!$__vars['direction'])) ? 'asc' : ''), ), ), false),
			'_type' => 'cell',
			'html' => '
							' . 'Date' . '
						',
		),
		array(
			'href' => $__templater->func('link', array('banning/discouraged-ips', '', array('order' => 'last_triggered_date', 'direction' => ((($__vars['direction'] == 'desc') OR (!$__vars['direction'])) ? 'asc' : ''), ), ), false),
			'_type' => 'cell',
			'html' => '
							' . 'Last triggered' . '
						',
		),
		array(
			'_type' => 'cell',
			'html' => 'By',
		),
		array(
			'_type' => 'cell',
			'html' => 'Reason',
		))) . '
					' . $__compilerTemp2 . '
				', array(
			'data-xf-init' => 'responsive-data-list',
		)) . '
			</div>
			<div class="block-footer block-footer--split">
				<span class="block-footer-counter">' . $__templater->func('display_totals', array($__vars['discouragedIps'], ), true) . '</span>
				<span class="block-footer-select">' . $__templater->formCheckBox(array(
			'standalone' => 'true',
		), array(array(
			'check-all' => '< .block-container',
			'label' => 'Select all',
			'_type' => 'option',
		))) . '</span>
				<span class="block-footer-controls">' . $__templater->button('', array(
			'type' => 'submit',
			'icon' => 'delete',
		), '', array(
		)) . '</span>
			</div>
			' . $__templater->func('page_nav', array(array(
			'page' => $__vars['page'],
			'total' => $__vars['total'],
			'link' => 'discouraged/ips',
			'params' => array('order' => (($__vars['order'] != 'create_date') ? $__vars['order'] : ''), 'direction' => (($__vars['direction'] != 'desc') ? $__vars['direction'] : ''), ),
			'wrapperclass' => 'block-outer block-outer--after',
			'perPage' => $__vars['perPage'],
		))) . '
		</div>
	', array(
			'action' => $__templater->func('link', array('banning/discouraged-ips/delete', ), false),
			'ajax' => 'true',
			'class' => 'block',
		)) . '
';
	} else {
		$__finalCompiled .= '
	<div class="blockMessage">' . 'No IPs have been discouraged.' . '</div>
';
	}
	$__finalCompiled .= '

' . $__templater->form('
	<div class="block-container">
		<h3 class="block-header">' . 'Add discouraged IP' . '</h3>
		<div class="block-body">
			' . $__templater->formTextBoxRow(array(
		'name' => 'ip',
		'value' => $__vars['ip'],
		'maxlength' => $__templater->func('max_length', array($__vars['newIp'], 'ip', ), false),
		'dir' => 'ltr',
	), array(
		'label' => 'IP to discourage',
		'explain' => 'You may enter a partial IP address (v4 or v6 format). Partial IPv4 addresses can be entered in the form of 192.168.* or 192.168.1.1/16. Partial IPv6 addresses may be entered in the form of 2001:db8::/32.',
	)) . '
			' . $__templater->formTextBoxRow(array(
		'name' => 'reason',
		'maxlength' => $__templater->func('max_length', array($__vars['newIp'], 'reason', ), false),
	), array(
		'label' => 'Reason',
		'hint' => 'Optional',
	)) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'icon' => 'save',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('banning/discouraged-ips/add', ), false),
		'ajax' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});