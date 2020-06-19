<?php
// FROM HASH: 88d4731704743cfce97b0a5bc44a41ce
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => $__vars['inputName'],
		'selected' => $__vars['option']['option_value'],
		'label' => $__templater->escape($__vars['option']['title']),
		'_type' => 'option',
	)), array(
		'hint' => $__templater->escape($__vars['hintHtml']),
		'explain' => 'You may prevent any visitors browsing from <a href="' . $__templater->func('link', array('banning/discouraged-ips', ), true) . '">discouraged IP addresses</a> from registering new accounts. They will be informed that registration is currently disabled.',
		'html' => $__templater->escape($__vars['listedHtml']),
	));
	return $__finalCompiled;
});