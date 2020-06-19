<?php
// FROM HASH: e841b9bf2b72188a2925f1013a5306da
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Confirm action');
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	if ($__vars['isDemotion']) {
		$__compilerTemp1 .= '
			<div class="block-body">
				' . $__templater->formInfoRow('
					' . 'Please confirm that you want remove this user from the selected promotion' . $__vars['xf']['language']['label_separator'] . '
					<strong>' . $__templater->escape($__vars['user']['username']) . ' - ' . $__templater->escape($__vars['promotion']['title']) . '</strong>
					' . 'This user will not receive this promotion even if he or she meets the requirements.' . '
				', array(
			'rowtype' => 'confirm',
		)) . '
			</div>
			' . $__templater->formSubmitRow(array(
			'submit' => 'Demote user',
		), array(
			'rowtype' => 'simple',
		)) . '
		';
	} else {
		$__compilerTemp1 .= '
			<div class="block-body">
				' . $__templater->formInfoRow('
					' . 'Please confirm that you want enable this promotion for the selected user' . $__vars['xf']['language']['label_separator'] . '
					<strong>' . $__templater->escape($__vars['user']['username']) . ' - ' . $__templater->escape($__vars['promotion']['title']) . '</strong>
					' . 'This user will receive this promotion when he or she meets the requirements.' . '
				', array(
			'rowtype' => 'confirm',
		)) . '
			</div>
			' . $__templater->formSubmitRow(array(
			'submit' => 'Enable promotion for user',
		), array(
		)) . '
		';
	}
	$__finalCompiled .= $__templater->form('

	<div class="block-container">
		' . $__compilerTemp1 . '
	</div>

	' . $__templater->func('redirect_input', array(null, null, true)) . '
', array(
		'action' => $__templater->func('link', array('user-group-promotions/demote', null, array('promotion_id' => $__vars['promotion']['promotion_id'], 'user_id' => $__vars['promotionLog']['user_id'], ), ), false),
		'ajax' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});