<?php
// FROM HASH: 47c95b853a857e8203693bdb271296eb
return array('macros' => array('select' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'prefixes' => '!',
		'selected' => '0',
		'withRow' => '1',
	), $__arguments, $__vars);
	$__finalCompiled .= '

	';
	$__vars['prefixesGrouped'] = $__vars['prefixes']['prefixesGrouped'];
	$__finalCompiled .= '
	';
	$__vars['prefixGroups'] = $__vars['prefixes']['prefixGroups'];
	$__finalCompiled .= '

	';
	if (!$__templater->test($__vars['prefixesGrouped'], 'empty', array())) {
		$__finalCompiled .= '
		';
		$__compilerTemp1 = array(array(
			'value' => '0',
			'label' => $__vars['xf']['language']['parenthesis_open'] . 'No prefix' . $__vars['xf']['language']['parenthesis_close'],
			'_type' => 'option',
		));
		if ($__templater->isTraversable($__vars['prefixGroups'])) {
			foreach ($__vars['prefixGroups'] AS $__vars['prefixGroupId'] => $__vars['prefixGroup']) {
				if (($__templater->func('count', array($__vars['prefixesGrouped'][$__vars['prefixGroupId']], ), false) > 0)) {
					if (($__vars['prefixGroupId'] > 0)) {
						$__compilerTemp1[] = array(
							'value' => '',
							'disabled' => 'disabled',
							'label' => $__templater->escape($__vars['prefixGroup']['title']),
							'_type' => 'option',
						);
					} else {
						$__compilerTemp1[] = array(
							'value' => '',
							'disabled' => 'disabled',
							'label' => $__vars['xf']['language']['parenthesis_open'] . 'Ungrouped' . $__vars['xf']['language']['parenthesis_close'],
							'_type' => 'option',
						);
					}
					if ($__templater->isTraversable($__vars['prefixesGrouped'][$__vars['prefixGroupId']])) {
						foreach ($__vars['prefixesGrouped'][$__vars['prefixGroupId']] AS $__vars['prefixId'] => $__vars['prefix']) {
							$__compilerTemp1[] = array(
								'value' => $__vars['prefixId'],
								'label' => '&nbsp;&nbsp; ' . $__templater->escape($__vars['prefix']['title']),
								'_type' => 'option',
							);
						}
					}
				}
			}
		}
		$__vars['inner'] = $__templater->preEscaped('
			' . $__templater->formSelect(array(
			'name' => 'prefix_id',
			'value' => $__vars['selected'],
		), $__compilerTemp1) . '
		');
		$__finalCompiled .= '

		';
		if ($__vars['withRow']) {
			$__finalCompiled .= '
			' . $__templater->formRow('
				' . $__templater->filter($__vars['inner'], array(array('raw', array()),), true) . '
			', array(
				'label' => 'Prefix',
				'rowtype' => 'input',
			)) . '
		';
		} else {
			$__finalCompiled .= '
			' . $__templater->filter($__vars['inner'], array(array('raw', array()),), true) . '
		';
		}
		$__finalCompiled .= '

	';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';

	return $__finalCompiled;
});