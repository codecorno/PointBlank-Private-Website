<?php
// FROM HASH: a245de1990a2211a58f16415b9c5936d
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('IP addresses logged for ' . $__templater->escape($__vars['user']['username']) . '');
	$__finalCompiled .= '

';
	if (!$__templater->test($__vars['ips'], 'empty', array())) {
		$__finalCompiled .= '
	<div class="block">
		<div class="block-container">
			<div class="block-body">
				';
		$__compilerTemp1 = '';
		if ($__templater->isTraversable($__vars['ips'])) {
			foreach ($__vars['ips'] AS $__vars['ip']) {
				$__compilerTemp1 .= '
						' . $__templater->dataRow(array(
					'rowclass' => 'dataList-row--noHover',
				), array(array(
					'href' => $__templater->func('link_type', array('public', 'misc/ip-info', null, array('ip' => $__templater->filter($__vars['ip']['ip'], array(array('ip', array()),), false), ), ), false),
					'target' => '_blank',
					'_type' => 'cell',
					'html' => $__templater->filter($__vars['ip']['ip'], array(array('ip', array()),), true),
				),
				array(
					'_type' => 'cell',
					'html' => $__templater->filter($__vars['ip']['total'], array(array('number', array()),), true),
				),
				array(
					'_type' => 'cell',
					'html' => $__templater->func('date_dynamic', array($__vars['ip']['first_date'], array(
				))),
				),
				array(
					'_type' => 'cell',
					'html' => $__templater->func('date_dynamic', array($__vars['ip']['last_date'], array(
				))),
				),
				array(
					'href' => $__templater->func('link', array('users/ip-users', null, array('ip' => $__templater->filter($__vars['ip']['ip'], array(array('ip', array()),), false), ), ), false),
					'overlay' => 'true',
					'_type' => 'action',
					'html' => 'More users',
				),
				array(
					'label' => '&#8226;&#8226;&#8226;',
					'class' => 'dataList-cell--separated',
					'_type' => 'popup',
					'html' => '
								<div class="menu" data-menu="menu" aria-hidden="true" data-menu-builder="dataList">
									<div class="menu-content">
										<h3 class="menu-header">' . 'More options' . '</h3>
										<a href="' . $__templater->func('link', array('banning/ips/add', null, array('ip' => $__templater->filter($__vars['ip']['ip'], array(array('ip', array()),), false), ), ), true) . '" class="menu-linkRow" data-xf-click="overlay">' . 'Ban' . '</a>
										<a href="' . $__templater->func('link', array('banning/discouraged-ips/add', null, array('ip' => $__templater->filter($__vars['ip']['ip'], array(array('ip', array()),), false), ), ), true) . '" class="menu-linkRow" data-xf-click="overlay">' . 'Discourage' . '</a>
										<div class="js-menuBuilderTarget u-showMediumBlock"></div>
									</div>
								</div>
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
			'html' => 'IP',
		),
		array(
			'_type' => 'cell',
			'html' => 'Total',
		),
		array(
			'_type' => 'cell',
			'html' => 'Earliest',
		),
		array(
			'_type' => 'cell',
			'html' => 'Latest',
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
		</div>
	</div>
';
	} else {
		$__finalCompiled .= '
	<div class="blockMessage">' . 'No IP logs were found for the requested user.' . '</div>
';
	}
	return $__finalCompiled;
});