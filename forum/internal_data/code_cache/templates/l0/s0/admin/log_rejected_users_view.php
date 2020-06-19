<?php
// FROM HASH: 5fa1ba2977168fb644f00ecf64b90603
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Rejected user log entry');
	$__finalCompiled .= '

<div class="block">
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formRow('
				' . $__templater->func('username_link', array($__vars['entry']['User'], false, array(
		'href' => $__templater->func('link', array('users/edit', $__vars['entry']['User'], ), false),
	))) . '
			', array(
		'label' => 'Rejected user',
	)) . '
			' . $__templater->formRow('
				' . $__templater->func('date_dynamic', array($__vars['entry']['reject_date'], array(
	))) . '
			', array(
		'label' => 'Date',
	)) . '
			';
	$__compilerTemp1 = '';
	if ($__vars['entry']['reject_user_id']) {
		$__compilerTemp1 .= '
					' . $__templater->func('username_link', array($__vars['entry']['RejectUser'], false, array(
			'href' => $__templater->func('link_type', array('users/edit', $__vars['entry']['RejectUser'], ), false),
		))) . '
				';
	} else {
		$__compilerTemp1 .= '
					' . 'N/A' . '
				';
	}
	$__finalCompiled .= $__templater->formRow('
				' . $__compilerTemp1 . '
			', array(
		'label' => 'Rejected by',
	)) . '
			';
	$__compilerTemp2 = '';
	if ($__vars['entry']['reject_reason']) {
		$__compilerTemp2 .= '
					' . $__templater->escape($__vars['entry']['reject_reason']) . '
				';
	} else {
		$__compilerTemp2 .= '
					' . 'N/A' . '
				';
	}
	$__finalCompiled .= $__templater->formRow('
				' . $__compilerTemp2 . '
			', array(
		'label' => 'Reason',
	)) . '
		</div>
	</div>
</div>';
	return $__finalCompiled;
});