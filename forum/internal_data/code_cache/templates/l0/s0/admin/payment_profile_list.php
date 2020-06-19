<?php
// FROM HASH: c42ce7e1bd5b66c09e183214c3053054
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Payment profiles');
	$__finalCompiled .= '

';
	$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('Add payment profile', array(
		'href' => $__templater->func('link', array('payment-profiles/add', ), false),
		'icon' => 'add',
		'overlay' => 'true',
	), '', array(
	)) . '
');
	$__finalCompiled .= '

';
	if (!$__vars['xf']['livePayments']) {
		$__finalCompiled .= '
	<div class="blockMessage blockMessage--important blockMessage--iconic">
		' . 'Live payments have been disabled so, where available, purchase requests will be made to sandbox/test endpoints. To rectify this, please edit config.php.' . '
	</div>
';
	}
	$__finalCompiled .= '

';
	if (!$__templater->test($__vars['providers'], 'empty', array())) {
		$__finalCompiled .= '
	';
		$__compilerTemp1 = '';
		if ($__templater->isTraversable($__vars['providers'])) {
			foreach ($__vars['providers'] AS $__vars['providerId'] => $__vars['provider']) {
				$__compilerTemp1 .= '
						';
				if (!$__templater->test($__vars['groupedProfiles'][$__vars['providerId']], 'empty', array())) {
					$__compilerTemp1 .= '
							' . $__templater->dataRow(array(
						'rowtype' => 'subsection',
						'rowclass' => 'dataList-row--noHover',
					), array(array(
						'colspan' => '3',
						'_type' => 'cell',
						'html' => $__templater->escape($__vars['provider']['title']),
					))) . '
							';
					if ($__templater->isTraversable($__vars['groupedProfiles'][$__vars['providerId']])) {
						foreach ($__vars['groupedProfiles'][$__vars['providerId']] AS $__vars['profile']) {
							$__compilerTemp1 .= '
								' . $__templater->dataRow(array(
								'label' => $__templater->escape($__vars['profile']['title']),
								'hint' => ($__templater->escape($__vars['profile']['display_title']) ?: ''),
								'href' => $__templater->func('link', array('payment-profiles/edit', $__vars['profile'], ), false),
								'delete' => $__templater->func('link', array('payment-profiles/delete', $__vars['profile'], ), false),
							), array()) . '
							';
						}
					}
					$__compilerTemp1 .= '
						';
				}
				$__compilerTemp1 .= '
					';
			}
		}
		$__finalCompiled .= $__templater->form('
		<div class="block-outer">
			' . $__templater->callMacro('filter_macros', 'quick_filter', array(
			'key' => 'payment-profiles',
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
				<span class="block-footer-counter">' . $__templater->func('display_totals', array($__vars['totalProfiles'], ), true) . '</span>
			</div>
		</div>
	', array(
			'action' => $__templater->func('link', array('payment-profiles/toggle', ), false),
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