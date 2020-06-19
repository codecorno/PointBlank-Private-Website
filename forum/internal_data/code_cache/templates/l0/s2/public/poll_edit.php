<?php
// FROM HASH: 9638e0443701b111c752db5a5c39a2fa
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Edit poll');
	$__finalCompiled .= '

';
	$__templater->breadcrumbs($__vars['breadcrumbs']);
	$__finalCompiled .= '

' . $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->callMacro('poll_macros', 'add_edit_inputs', array(
		'poll' => $__vars['poll'],
	), $__vars) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'icon' => 'save',
		'sticky' => 'true',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->method($__vars['poll'], 'getLink', array('edit', )),
		'class' => 'block',
		'ajax' => 'true',
	)) . '

';
	if ($__vars['poll'] AND $__templater->method($__vars['poll'], 'canDelete', array())) {
		$__finalCompiled .= '
	' . $__templater->callMacro('poll_macros', 'delete_block', array(
			'poll' => $__vars['poll'],
		), $__vars) . '
';
	}
	return $__finalCompiled;
});