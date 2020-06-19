<?php
// FROM HASH: cb5c154d3b99b5d36beb64054d5d8d8e
return array('macros' => array('forum_page_options' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'forum' => '!',
		'thread' => '',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	$__templater->setPageParam('forum', $__vars['forum']);
	$__finalCompiled .= '

	';
	if ($__vars['thread']) {
		$__finalCompiled .= '
		';
		$__templater->setPageParam('searchConstraints', array('Threads' => array('search_type' => 'post', ), 'This forum' => array('search_type' => 'post', 'c' => array('nodes' => array($__vars['forum']['node_id'], ), 'child_nodes' => 1, ), ), 'This thread' => array('search_type' => 'post', 'c' => array('thread' => $__vars['thread']['thread_id'], ), ), ));
		$__finalCompiled .= '
	';
	} else {
		$__finalCompiled .= '
		';
		$__templater->setPageParam('searchConstraints', array('Threads' => array('search_type' => 'post', ), 'This forum' => array('search_type' => 'post', 'c' => array('nodes' => array($__vars['forum']['node_id'], ), 'child_nodes' => 1, ), ), ));
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