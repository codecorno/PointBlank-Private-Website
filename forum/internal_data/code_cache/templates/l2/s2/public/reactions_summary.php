<?php
// FROM HASH: 315ca88108e9c6dd3cbf9d04af985858
return array('macros' => array('summary' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'reactionIds' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	$__compilerTemp1 = '';
	$__compilerTemp1 .= '
			';
	if ($__templater->isTraversable($__vars['reactionIds'])) {
		foreach ($__vars['reactionIds'] AS $__vars['reactionId']) {
			$__compilerTemp1 .= trim('
				<li>' . $__templater->func('reaction', array(array(
				'id' => $__vars['reactionId'],
				'small' => 'true',
			))) . '</li>
			');
		}
	}
	$__compilerTemp1 .= '
		';
	if (strlen(trim($__compilerTemp1)) > 0) {
		$__finalCompiled .= '
		<ul class="reactionSummary">
		' . $__compilerTemp1 . '
		</ul>
	';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '

' . $__templater->callMacro(null, 'summary', array(
		'reactionIds' => $__vars['reactionIds'],
	), $__vars);
	return $__finalCompiled;
});