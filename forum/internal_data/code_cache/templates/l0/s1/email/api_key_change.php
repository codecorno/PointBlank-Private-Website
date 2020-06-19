<?php
// FROM HASH: 4bd389eac824b17b34ecbd325c5f2b70
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<mail:subject>
	' . '' . $__templater->escape($__vars['xf']['options']['boardTitle']) . ' - API key changed' . '
</mail:subject>

' . '<p>' . $__templater->escape($__vars['user']['username']) . ',</p>

<p>' . $__templater->escape($__vars['changer']['username']) . ' just updated or created the API key "' . $__templater->escape($__vars['apiKey']['title']) . '". API keys can be used to access and make changes to your site programmatically. Only super administrators may manage API keys.</p>

<p>If this change was not legitimate, the API key should be deleted immediately and steps should be taken to ensure that all administrator accounts are secure. The current API keys can be managed in your <a href="' . $__templater->func('link_type', array('admin', 'canonical:api-keys', ), true) . '">admin control panel</a>.</p>

<p>All super administrators have been automatically notified of this change.</p>';
	return $__finalCompiled;
});