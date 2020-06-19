<?php
// FROM HASH: 2684ba82fd475acefb6c4baaf511f5cd
return array('macros' => array('category_page_options' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'category' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	$__templater->setPageParam('searchConstraints', array('Threads' => array('search_type' => 'post', ), 'This category' => array('search_type' => 'post', 'c' => array('nodes' => array($__vars['category']['node_id'], ), 'child_nodes' => 1, ), ), ));
	$__finalCompiled .= '
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';

	return $__finalCompiled;
});