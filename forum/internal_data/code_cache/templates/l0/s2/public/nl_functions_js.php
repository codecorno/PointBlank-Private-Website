<?php
// FROM HASH: 1289447612e77c3adb71c2f329179355
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__compilerTemp1 = '';
	if ($__templater->func('property', array('nlSeparateNodeDesc', ), false) == true) {
		$__compilerTemp1 .= '
	$(".block-desc").each(function() {
		$(this).closest( "h2.block-header" ).after(this);
    });
	';
	}
	$__templater->inlineJs('
    ' . $__compilerTemp1 . '
');
	return $__finalCompiled;
});