<?php
// FROM HASH: 8c554dd22c721fcdbe97cdc6b1377ba9
return array('macros' => array('ip_block' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'ip' => '!',
		'user' => null,
		'heading' => '',
	), $__arguments, $__vars);
	$__finalCompiled .= '
<div class="block">
	<div class="block-container">
		';
	if (!$__templater->test($__vars['heading'], 'empty', array())) {
		$__finalCompiled .= '
			<div class="block-header">
				' . $__templater->escape($__vars['heading']) . '
			</div>
		';
	}
	$__finalCompiled .= '
		<div class="block-body">
			' . $__templater->formRow('
				<strong><a class="ip" href="' . $__templater->func('link', array('misc/ip-info', null, array('ip' => $__templater->filter($__vars['ip'], array(array('ip', array()),), false), ), ), true) . '" target="_blank">' . $__templater->filter($__vars['ip'], array(array('ip', array()),), true) . '</a></strong> ' . $__templater->filter($__vars['ip'], array(array('host', array()),array('parens', array()),), true) . '
			', array(
		'label' => 'Content IP',
	)) . '

			';
	if ($__vars['user']) {
		$__finalCompiled .= '
				';
		$__vars['registerIp'] = $__templater->method($__vars['user'], 'getIp', array('register', ));
		$__finalCompiled .= '
				';
		if ($__vars['registerIp']) {
			$__finalCompiled .= '
					' . $__templater->formRow('
						<strong><a class="ip" href="' . $__templater->func('link', array('misc/ip-info', null, array('ip' => $__templater->filter($__vars['registerIp'], array(array('ip', array()),), false), ), ), true) . '" target="_blank">' . $__templater->filter($__vars['registerIp'], array(array('ip', array()),), true) . '</a></strong> ' . $__templater->filter($__vars['registerIp'], array(array('host', array()),array('parens', array()),), true) . '
					', array(
				'label' => 'User registration IP',
			)) . '
				';
		}
		$__finalCompiled .= '
			';
	}
	$__finalCompiled .= '
		</div>
	</div>
</div>
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';

	return $__finalCompiled;
});