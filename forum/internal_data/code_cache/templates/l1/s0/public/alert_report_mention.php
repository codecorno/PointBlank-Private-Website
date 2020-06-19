<?php
// FROM HASH: 8d873e2dece6b26bcac41749b892c8ae
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '' . $__templater->func('username_link', array($__vars['user'], false, array('defaultname' => $__vars['alert']['username'], ), ), true) . ' mentioned you in a comment in the report ' . (((((('<a href="' . $__templater->func('link', array('reports', $__vars['content'], ), true)) . '#report-comment-') . $__templater->escape($__vars['alert']['extra_data']['comment']['report_comment_id'])) . '" class="fauxBlockLink-blockLink">') . $__templater->escape($__vars['content']['title'])) . '</a>') . '.';
	return $__finalCompiled;
});