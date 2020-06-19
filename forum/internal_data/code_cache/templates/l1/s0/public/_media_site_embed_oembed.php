<?php
// FROM HASH: d4ffb63bdf9603f1a20ffa789f72a461
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->includeJs(array(
		'src' => 'xf/embed.js',
		'min' => '1',
	));
	$__finalCompiled .= '

';
	$__templater->setPageParam('jsState.' . $__vars['jsState'], true);
	$__finalCompiled .= '

<span class="bbOembed bbMediaJustifier"
	  data-xf-init="oembed"
	  data-provider="' . $__templater->escape($__vars['provider']) . '"
	  data-id="' . $__templater->escape($__vars['id']) . '">
	<a href="' . $__templater->escape($__vars['url']) . '" rel="external" target="_blank">' . $__templater->fontAwesome('fab fa-' . $__templater->escape($__vars['provider']) . ' fa-' . $__templater->escape($__vars['provider']) . '-square', array(
	)) . '
		' . $__templater->escape($__vars['url']) . '</a>
</span>';
	return $__finalCompiled;
});