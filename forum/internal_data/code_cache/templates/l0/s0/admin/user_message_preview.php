<?php
// FROM HASH: 85e979e6af6a1a5a3c298190e2ef1609
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped($__templater->escape($__vars['title']));
	$__finalCompiled .= '

<div class="blockMessage">' . $__templater->func('bb_code', array($__vars['content'], 'conversation_message', $__vars['xf']['visitor'], ), true) . '</div>';
	return $__finalCompiled;
});