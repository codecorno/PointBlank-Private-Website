<?php
// FROM HASH: a3394bb6f73c60bdff1e0b84f925e211
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Users logged at ' . $__templater->escape($__vars['ipPrintable']) . '');
	$__finalCompiled .= '

<div class="block">
	<div class="block-container">
		<div class="block-body">
			';
	$__compilerTemp1 = '';
	if ($__templater->isTraversable($__vars['ips'])) {
		foreach ($__vars['ips'] AS $__vars['key'] => $__vars['ip']) {
			$__compilerTemp1 .= '
					';
			$__vars['user'] = $__vars['ip']['user'];
			$__compilerTemp1 .= '
					';
			$__compilerTemp2 = '';
			if ($__vars['ip']['ip_total'] > 1) {
				$__compilerTemp2 .= '
										<li>' . '' . $__templater->filter($__vars['ip']['ips']['0'], array(array('ip', array()),), true) . ' and ' . ($__vars['ip']['ip_total'] - 1) . ' more' . '</li>
									';
			} else {
				$__compilerTemp2 .= '
										<li>' . $__templater->filter($__vars['ip']['ips']['0'], array(array('ip', array()),), true) . '</li>
									';
			}
			$__compilerTemp3 = '';
			if ($__vars['ip']['total'] > 1) {
				$__compilerTemp3 .= '
										<li>' . '' . $__templater->filter($__vars['ip']['total'], array(array('number', array()),), true) . ' times' . '</li>
										<li>' . $__templater->func('date_dynamic', array($__vars['ip']['first_date'], array(
				))) . ' - ' . $__templater->func('date_dynamic', array($__vars['ip']['last_date'], array(
				))) . '</li>
									';
			} else {
				$__compilerTemp3 .= '
										<li>' . '1 time' . '</li>
										<li>' . $__templater->func('date_dynamic', array($__vars['ip']['first_date'], array(
				))) . '</li>
									';
			}
			$__compilerTemp1 .= $__templater->dataRow(array(
				'rowclass' => 'dataList-row--noHover',
			), array(array(
				'class' => 'dataList-cell--min dataList-cell--alt',
				'_type' => 'cell',
				'html' => '
							' . $__templater->func('avatar', array($__vars['user'], 's', false, array(
				'href' => $__templater->func('link', array('members', $__vars['user'], ), false),
			))) . '
						',
			),
			array(
				'href' => $__templater->func('link', array('members', $__vars['user'], ), false),
				'label' => $__templater->func('username_link', array($__vars['user'], true, array(
				'notooltip' => 'true',
				'href' => '',
			))),
				'hint' => $__templater->func('user_title', array($__vars['user'], false, array(
			))),
				'explain' => '
								<ul class="listInline listInline--bullet">
									' . $__compilerTemp2 . '
									' . $__compilerTemp3 . '
								</ul>
							',
				'_type' => 'main',
				'html' => '',
			))) . '
				';
		}
	}
	$__finalCompiled .= $__templater->dataList('
				' . $__compilerTemp1 . '
			', array(
	)) . '
		</div>
	</div>
</div>';
	return $__finalCompiled;
});