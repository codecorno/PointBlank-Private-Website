<?php
// FROM HASH: b7d925e766fb3ffcda05e02b8a3c634b
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= 'Your comment on ' . (((('<a href="' . $__templater->func('base_url', array($__vars['extra']['profilePostLink'], ), true)) . '" class="fauxBlockLink-blockLink">') . $__templater->escape($__vars['extra']['postUser'])) . '</a>') . '\'s profile post was deleted.' . '
';
	if ($__vars['extra']['reason']) {
		$__finalCompiled .= 'Reason' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['extra']['reason']);
	}
	return $__finalCompiled;
});