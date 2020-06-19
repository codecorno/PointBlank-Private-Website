<?php
// FROM HASH: 7362b53a64ef5a5917a6cce27ac3c3e9
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->setPageParam('jsState.imgur', true);
	$__finalCompiled .= '

<div class="bbMediaWrapper">
	<blockquote class="imgur-embed-pub" lang="en" data-id="' . $__templater->escape($__vars['idRaw']) . '">
		<a href="http://imgur.com/' . $__templater->escape($__vars['idSlash']) . '" rel="external" target="_blank">http://imgur.com/' . $__templater->escape($__vars['idSlash']) . '</a>
	</blockquote>
</div>';
	return $__finalCompiled;
});