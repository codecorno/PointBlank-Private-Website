<?php
// FROM HASH: 4dd5511eb52a5f2bab8f236a6bdbcdbd
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Upgrade check');
	$__finalCompiled .= '

';
	if ($__vars['failed']) {
		$__finalCompiled .= '
	<div class="blockMessage blockMessage--error">
		' . 'The last upgrade check failed. See the server error log for more details.' . '
	</div>
';
	}
	$__finalCompiled .= '

';
	if ($__vars['upgradeCheck']) {
		$__finalCompiled .= '
	' . $__templater->callMacro('upgrade_check_macros', 'full_status', array(
			'upgradeCheck' => $__vars['upgradeCheck'],
			'showSuccess' => true,
		), $__vars) . '
';
	}
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	if ($__vars['upgradeCheck']['check_date']) {
		$__compilerTemp1 .= '
					' . 'Last upgrade check: ' . $__templater->func('date_dynamic', array($__vars['upgradeCheck']['check_date'], ), true) . '' . '
				';
	} else {
		$__compilerTemp1 .= '
					' . 'No upgrade check has been performed.' . '
				';
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<div class="block-body block-row">
			' . $__templater->formInfoRow('
				' . $__compilerTemp1 . '
			', array(
		'rowtype' => 'close',
	)) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'submit' => 'Check now' . $__vars['xf']['language']['ellipsis'],
	), array(
		'rowtype' => 'simple',
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('tools/upgrade-check', ), false),
		'class' => 'block',
	));
	return $__finalCompiled;
});