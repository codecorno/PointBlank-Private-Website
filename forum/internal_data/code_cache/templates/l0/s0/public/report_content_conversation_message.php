<?php
// FROM HASH: 78e393615d559ad327965cde05f288ed
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<div class="block-row block-row--separated">
	' . $__templater->func('bb_code', array($__vars['report']['content_info']['message'], 'conversation_message', ($__vars['content'] ?: $__vars['report']['User']), ), true) . '
</div>';
	return $__finalCompiled;
});