<?php
// FROM HASH: 295349da33185498fb7f3a11c1d1990b
return array('macros' => array('delete' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'warning' => '!',
		'redirect' => '',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	' . $__templater->form('
		' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'confirm',
		'value' => '1',
		'label' => 'Confirm deletion',
		'_type' => 'option',
	)), array(
	)) . '
		' . $__templater->formSubmitRow(array(
		'icon' => 'delete',
	), array(
	)) . '
		' . $__templater->func('redirect_input', array($__vars['redirect'], null, true)) . '
	', array(
		'action' => $__templater->func('link', array('warnings/delete', $__vars['warning'], ), false),
	)) . '
';
	return $__finalCompiled;
},
'expire' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'warning' => '!',
		'redirect' => '',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	' . $__templater->form('
		' . $__templater->formRadioRow(array(
		'name' => 'expire',
		'value' => 'now',
	), array(array(
		'value' => 'now',
		'label' => 'Expire now',
		'_type' => 'option',
	),
	array(
		'value' => 'future',
		'label' => 'Expire in' . $__vars['xf']['language']['label_separator'],
		'_dependent' => array('
					<div class="inputGroup">
						' . $__templater->formNumberBox(array(
		'name' => 'expiry_value',
		'value' => '1',
		'min' => '0',
	)) . '
						<span class="inputGroup-splitter"></span>
						' . $__templater->formSelect(array(
		'name' => 'expiry_unit',
		'value' => 'days',
		'class' => 'input--inline',
	), array(array(
		'value' => 'days',
		'label' => 'Days',
		'_type' => 'option',
	),
	array(
		'value' => 'weeks',
		'label' => 'Weeks',
		'_type' => 'option',
	),
	array(
		'value' => 'months',
		'label' => 'Months',
		'_type' => 'option',
	),
	array(
		'value' => 'years',
		'label' => 'Years',
		'_type' => 'option',
	))) . '
					</div>
				'),
		'_type' => 'option',
	)), array(
	)) . '
		' . $__templater->formSubmitRow(array(
		'icon' => 'save',
	), array(
	)) . '
		' . $__templater->func('redirect_input', array($__vars['redirect'], null, true)) . '
	', array(
		'action' => $__templater->func('link', array('warnings/expire', $__vars['warning'], ), false),
	)) . '
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Warning for ' . $__templater->escape($__vars['user']['username']) . '');
	$__finalCompiled .= '

<div class="form block">
	<div class="block-container">
		<div class="block-body">
			';
	if ($__vars['canViewContent']) {
		$__finalCompiled .= '
				';
		$__compilerTemp1 = '';
		if ($__vars['contentUrl']) {
			$__compilerTemp1 .= '
						<a href="' . $__templater->escape($__vars['contentUrl']) . '">' . $__templater->escape($__vars['warning']['display_title']) . '</a>
					';
		} else {
			$__compilerTemp1 .= '
						' . $__templater->escape($__vars['warning']['display_title']) . '
					';
		}
		$__finalCompiled .= $__templater->formRow('
					' . $__compilerTemp1 . '
				', array(
			'label' => 'Content',
		)) . '
			';
	}
	$__finalCompiled .= '

			' . $__templater->formRow('
				<ul class="listInline listInline--bullet">
					<li>' . $__templater->func('username_link', array($__vars['warning']['WarnedBy'], false, array(
	))) . '</li>
					<li>' . $__templater->func('date_dynamic', array($__vars['warning']['warning_date'], array(
	))) . '</li>
				</ul>
			', array(
		'label' => 'Given by',
	)) . '

			';
	$__compilerTemp2 = '';
	if ($__vars['warning']['notes']) {
		$__compilerTemp2 .= '<div class="u-muted">' . $__templater->func('structured_text', array($__vars['warning']['notes'], ), true) . '</div>';
	}
	$__finalCompiled .= $__templater->formRow('
				' . $__templater->escape($__vars['warning']['title']) . '
				' . $__compilerTemp2 . '
			', array(
		'label' => 'Details of warning',
	)) . '

			';
	$__compilerTemp3 = '';
	if ($__vars['warning']['is_expired']) {
		$__compilerTemp3 .= '
					<span class="u-muted">' . $__vars['xf']['language']['parenthesis_open'] . 'Expired' . $__vars['xf']['language']['parenthesis_close'] . '</span>
				';
	} else if ($__vars['warning']['expiry_date']) {
		$__compilerTemp3 .= '
					<span class="u-muted">' . $__vars['xf']['language']['parenthesis_open'] . 'Expires ' . $__templater->func('date', array($__vars['warning']['expiry_date'], ), true) . '' . $__vars['xf']['language']['parenthesis_close'] . '</span>
				';
	}
	$__finalCompiled .= $__templater->formRow('
				' . $__templater->filter($__vars['warning']['points'], array(array('number', array()),), true) . '
				' . $__compilerTemp3 . '
			', array(
		'label' => 'Warning points',
	)) . '
		</div>
		';
	if ($__templater->method($__vars['warning'], 'canDelete', array()) AND $__templater->method($__vars['warning'], 'canEditExpiry', array())) {
		$__finalCompiled .= '
			<h2 class="block-tabHeader tabs" data-xf-init="tabs" role="tablist">
				<a class="tabs-tab is-active" role="tab" tabindex="0" aria-controls="' . $__templater->func('unique_id', array('warningDelete', ), true) . '">' . 'Delete warning' . '</a>
				<a class="tabs-tab is-active" role="tab" tabindex="0" aria-controls="' . $__templater->func('unique_id', array('warningUpdate', ), true) . '">' . 'Update warning' . '</a>
			</h2>
			<ul class="tabPanes">
				<li class="is-active" role="tabpanel" id="' . $__templater->func('unique_id', array('warningDelete', ), true) . '">
					' . $__templater->callMacro(null, 'delete', array(
			'warning' => $__vars['warning'],
		), $__vars) . '
				</li>
				<li role="tabpanel" id="' . $__templater->func('unique_id', array('warningUpdate', ), true) . '">
					' . $__templater->callMacro(null, 'expire', array(
			'warning' => $__vars['warning'],
		), $__vars) . '
				</li>
			</ul>
		';
	} else if ($__templater->method($__vars['warning'], 'canDelete', array())) {
		$__finalCompiled .= '
			' . $__templater->callMacro(null, 'delete', array(
			'warning' => $__vars['warning'],
		), $__vars) . '
		';
	} else if ($__templater->method($__vars['warning'], 'canEditExpiry', array())) {
		$__finalCompiled .= '
			' . $__templater->callMacro(null, 'expire', array(
			'warning' => $__vars['warning'],
		), $__vars) . '
		';
	}
	$__finalCompiled .= '
	</div>
</div>

' . '

';
	return $__finalCompiled;
});