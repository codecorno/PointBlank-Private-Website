<?php
// FROM HASH: a462648eb7cdd4a3aa338af9770eed8a
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__vars['shortcodes'] = ':slight_smile:&nbsp;:bread:&nbsp;:heart_eyes:&nbsp;:coffee:&nbsp;:thumbsup:&nbsp;:grinning:&nbsp;:hamburger:&nbsp;:zany_face:';
	$__finalCompiled .= '

' . $__templater->formRadioRow(array(
		'name' => $__vars['inputName'],
		'value' => $__vars['option']['option_value'],
	), array(array(
		'value' => 'emojione',
		'label' => 'Emoji <a href="https://www.joypixels.com/" target="_blank">by JoyPixels</a>',
		'hint' => '<span style="font-size: 15px">' . $__templater->filter($__templater->func('short_to_emoji', array($__vars['shortcodes'], 'emojione', true, ), false), array(array('raw', array()),), true) . '</span>',
		'_type' => 'option',
	),
	array(
		'value' => 'twemoji',
		'label' => 'Twemoji <a href="https://twitter.github.io/twemoji/" target="_blank">by Twitter</a>',
		'hint' => '<span style="font-size: 15px">' . $__templater->filter($__templater->func('short_to_emoji', array($__vars['shortcodes'], 'twemoji', true, ), false), array(array('raw', array()),), true) . '</span>',
		'_type' => 'option',
	),
	array(
		'value' => 'native',
		'label' => 'Native device emoji (where supported)',
		'hint' => '<span style="font-size: 18px">' . $__templater->filter($__templater->func('short_to_emoji', array($__vars['shortcodes'], 'native', ), false), array(array('raw', array()),), true) . '</span>',
		'_type' => 'option',
	)), array(
		'label' => $__templater->escape($__vars['option']['title']),
		'hint' => $__templater->escape($__vars['hintHtml']),
		'explain' => $__templater->escape($__vars['explainHtml']),
		'html' => $__templater->escape($__vars['listedHtml']),
	));
	return $__finalCompiled;
});