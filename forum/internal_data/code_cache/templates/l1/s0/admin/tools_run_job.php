<?php
// FROM HASH: 5d4ced1e686291b092a4f5bd5878613c
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Processing' . $__vars['xf']['language']['ellipsis']);
	$__finalCompiled .= '
';
	$__templater->setPageParam('template', 'PAGE_RUN_JOB');
	$__finalCompiled .= '

';
	if ($__vars['canCancel']) {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageAction'] = $__templater->preEscaped('
		<form action="' . $__templater->func('link', array('tools/run-job', null, array('cancel' => $__vars['jobId'], ), ), true) . '" method="post" onsubmit="window.stop()" class="u-pullRight">
			' . $__templater->button('Cancel', array(
			'type' => 'submit',
		), '', array(
		)) . '
			' . $__templater->func('redirect_input', array(null, null, false, ), true) . '
			' . $__templater->func('csrf_input') . '
		</form>
	');
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

<form action="' . $__templater->func('link', array('tools/run-job', ), true) . '" method="post" class="blockMessage" data-xf-init="auto-submit">

	<div>';
	if ($__vars['status']) {
		$__finalCompiled .= $__templater->escape($__vars['status']);
	} else {
		$__finalCompiled .= 'Processing' . $__vars['xf']['language']['ellipsis'];
	}
	$__finalCompiled .= '</div>

	<div class="u-noJsOnly">
		' . $__templater->button('Proceed' . $__vars['xf']['language']['ellipsis'], array(
		'type' => 'submit',
	), '', array(
	)) . '
	</div>
	' . $__templater->func('redirect_input', array(null, null, false, ), true) . '
	' . $__templater->func('csrf_input') . '
	' . $__templater->formHiddenVal('only_ids', $__templater->filter($__vars['onlyIds'], array(array('join', array(',', )),), false), array(
	)) . '
</form>';
	return $__finalCompiled;
});