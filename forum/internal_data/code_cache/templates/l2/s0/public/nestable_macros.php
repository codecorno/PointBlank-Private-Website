<?php
// FROM HASH: 8782caa624f0c17680b25645525fa4df
return array('macros' => array('setup' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'includeLess' => true,
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	if ($__vars['includeLess']) {
		$__finalCompiled .= '
		';
		$__templater->includeCss('public:nestable.less');
		$__finalCompiled .= '
	';
	}
	$__finalCompiled .= '
	';
	$__templater->includeJs(array(
		'prod' => 'xf/nestable-compiled.js',
		'dev' => 'vendor/nestable/jquery.nestable.js, xf/nestable.js',
	));
	$__finalCompiled .= '
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';

	return $__finalCompiled;
});