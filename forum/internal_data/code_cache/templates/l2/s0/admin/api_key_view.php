<?php
// FROM HASH: 51d46983f9cd535e17c19cd53ca4daac
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('API key details' . ': ' . $__templater->escape($__vars['apiKey']['title']));
	$__finalCompiled .= '

<div class="block">
	<div class="block-container">
		<div class="block-body">
			' . $__templater->callMacro('api_key_macros', 'copy_key_row', array(
		'apiKey' => $__vars['apiKey'],
	), $__vars) . '
			' . $__templater->callMacro('api_key_macros', 'key_type_row', array(
		'apiKey' => $__vars['apiKey'],
	), $__vars) . '
		</div>
	</div>
</div>';
	return $__finalCompiled;
});