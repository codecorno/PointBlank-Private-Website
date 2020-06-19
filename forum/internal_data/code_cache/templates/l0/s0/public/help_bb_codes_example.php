<?php
// FROM HASH: 0caf0a311335c8093ce4dca005e35a4f
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<dl class="example">
	<dt>' . 'Example' . $__vars['xf']['language']['label_separator'] . '</dt>
	<dd>' . ($__vars['bbCodeExampleHtml'] ? $__templater->filter($__vars['bbCodeExampleHtml'], array(array('raw', array()),array('nl2br', array()),), true) : $__templater->filter($__vars['bbCodeEval'], array(array('nl2br', array()),), true)) . '</dd>
</dl>
<dl class="output">
	<dt>' . 'Output' . $__vars['xf']['language']['label_separator'] . '</dt>
	<dd>' . $__templater->func('bb_code', array($__vars['bbCodeEval'], 'help', null, ), true) . '</dd>
</dl>';
	return $__finalCompiled;
});