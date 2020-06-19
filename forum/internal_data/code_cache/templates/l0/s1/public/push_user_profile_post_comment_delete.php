<?php
// FROM HASH: e21c137ef4ee55c0d2c5849138c7cc92
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= 'Your comment on ' . $__templater->escape($__vars['extra']['postUser']) . '\'s profile post was deleted.' . '
';
	if ($__vars['extra']['reason']) {
		$__finalCompiled .= 'Reason' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['extra']['reason']);
	}
	$__finalCompiled .= '
<push:url>' . $__templater->func('base_url', array($__vars['extra']['profilePostLink'], 'canonical', ), true) . '</push:url>';
	return $__finalCompiled;
});