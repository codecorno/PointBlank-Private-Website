<?php
// FROM HASH: 409779a28a3b04fab602ca9dada5a848
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<div class="block-row block-row--separated">
	' . $__templater->func('bb_code', array($__vars['report']['content_info']['message'], 'post', ($__vars['content'] ?: $__vars['report']['User']), ), true) . '
</div>
<div class="block-row block-row--separated block-row--minor">
	<dl class="pairs pairs--inline">
		<dt>' . 'Forum' . '</dt>
		<dd><a href="' . $__templater->func('link', array('forums', $__vars['report']['content_info'], ), true) . '">' . $__templater->escape($__vars['report']['content_info']['node_title']) . '</a></dd>
	</dl>
</div>';
	return $__finalCompiled;
});