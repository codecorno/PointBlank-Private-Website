<?php
// FROM HASH: 2b7add513c9020d2bd9420c503c57855
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->filter($__vars['extra']['alert_text'], array(array('strip_tags', array()),), true) . '
';
	if ($__vars['extra']['link_url']) {
		$__finalCompiled .= '
	<push:url>' . $__templater->escape($__vars['extra']['link_url']) . '</push:url>
';
	}
	return $__finalCompiled;
});