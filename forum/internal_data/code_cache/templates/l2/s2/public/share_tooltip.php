<?php
// FROM HASH: c445af5db4bcf7253e67af1b08b9b346
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<div class="tooltip-content-inner">
	<h3 class="block-minorHeader">' . $__templater->escape($__vars['tooltipTitle']) . '</h3>
	';
	$__compilerTemp1 = '';
	$__compilerTemp1 .= '
				' . $__templater->callMacro('share_page_macros', 'buttons', array(
		'iconic' => true,
		'hideLink' => true,
		'pageUrl' => $__vars['contentUrl'],
		'pageTitle' => $__vars['contentTitle'],
		'pageDesc' => $__vars['contentDesc'],
	), $__vars) . '
			';
	if (strlen(trim($__compilerTemp1)) > 0) {
		$__finalCompiled .= '
		<div class="block-body block-row block-row--separated block-row--separated--mergePrev">
			' . $__compilerTemp1 . '
		</div>
	';
	}
	$__finalCompiled .= '
	<div class="block-body block-row block-row--separated">
		' . $__templater->callMacro('share_page_macros', 'share_clipboard_input', array(
		'label' => '',
		'text' => $__vars['contentUrl'],
		'successText' => 'Link copied to clipboard.',
	), $__vars) . '
	</div>
</div>';
	return $__finalCompiled;
});