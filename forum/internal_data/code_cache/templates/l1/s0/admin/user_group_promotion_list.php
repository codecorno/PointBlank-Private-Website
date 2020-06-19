<?php
// FROM HASH: 3fb4d90c169fa85bbeef557612c789a4
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('User group promotions');
	$__finalCompiled .= '

';
	$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('Manage promoted users', array(
		'href' => $__templater->func('link', array('user-group-promotions/manage', ), false),
	), '', array(
	)) . '
	' . $__templater->button('Add promotion', array(
		'href' => $__templater->func('link', array('user-group-promotions/add', ), false),
		'icon' => 'add',
	), '', array(
	)) . '
');
	$__finalCompiled .= '

';
	if (!$__templater->test($__vars['userGroupPromotions'], 'empty', array())) {
		$__finalCompiled .= '
	';
		$__compilerTemp1 = '';
		if ($__templater->isTraversable($__vars['userGroupPromotions'])) {
			foreach ($__vars['userGroupPromotions'] AS $__vars['userGroupPromotion']) {
				$__compilerTemp1 .= '
						' . $__templater->dataRow(array(
					'label' => $__templater->escape($__vars['userGroupPromotion']['title']),
					'href' => $__templater->func('link', array('user-group-promotions/edit', $__vars['userGroupPromotion'], ), false),
					'delete' => $__templater->func('link', array('user-group-promotions/delete', $__vars['userGroupPromotion'], ), false),
				), array(array(
					'name' => 'active[' . $__vars['userGroupPromotion']['promotion_id'] . ']',
					'selected' => $__vars['userGroupPromotion']['active'],
					'class' => 'dataList-cell--separated',
					'submit' => 'true',
					'tooltip' => 'Enable / disable \'' . $__vars['userGroupPromotion']['title'] . '\'',
					'_type' => 'toggle',
					'html' => '',
				))) . '
					';
			}
		}
		$__finalCompiled .= $__templater->form('
		<div class="block-outer">
			' . $__templater->callMacro('filter_macros', 'quick_filter', array(
			'key' => 'promotions',
			'class' => 'block-outer-opposite',
		), $__vars) . '
		</div>
		<div class="block-container">
			<div class="block-body">
				' . $__templater->dataList('
					' . $__compilerTemp1 . '
				', array(
		)) . '
			</div>
			<div class="block-footer">
				<span class="block-footer-counter">' . $__templater->func('display_totals', array($__vars['userGroupPromotions'], ), true) . '</span>
			</div>
		</div>
	', array(
			'action' => $__templater->func('link', array('user-group-promotions/toggle', ), false),
			'ajax' => 'true',
			'class' => 'block',
		)) . '
';
	} else {
		$__finalCompiled .= '
	<div class="blockMessage">' . 'No promotions have been defined.' . '</div>
';
	}
	return $__finalCompiled;
});