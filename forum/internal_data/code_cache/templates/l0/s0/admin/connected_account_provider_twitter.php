<?php
// FROM HASH: 72ba94f72626a9f59de533269bc28c45
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->formTextBoxRow(array(
		'name' => 'options[consumer_key]',
		'value' => $__vars['options']['consumer_key'],
	), array(
		'label' => 'Consumer key',
		'hint' => 'Required',
		'explain' => 'To allow users to sign in with their Twitter accounts, you must create a <a href="https://developer.twitter.com/" target="_blank">Twitter application</a> and enter the consumer key and secret.',
	)) . '

' . $__templater->formTextBoxRow(array(
		'name' => 'options[consumer_secret]',
		'value' => $__vars['options']['consumer_secret'],
	), array(
		'label' => 'Consumer secret',
		'hint' => 'Required',
		'explain' => 'If you have created a Twitter application to allow Twitter sign in, set the application\'s consumer secret here.',
	));
	return $__finalCompiled;
});