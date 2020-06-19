<?php
// FROM HASH: 553d902a699a524cab24c02d0dd0e0d8
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->method($__vars['userGroupPromotion'], 'isInsert', array())) {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Add promotion');
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Edit promotion' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['userGroupPromotion']['title']));
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	if ($__templater->method($__vars['userGroupPromotion'], 'isUpdate', array())) {
		$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('', array(
			'href' => $__templater->func('link', array('user-group-promotions/delete', $__vars['userGroupPromotion'], ), false),
			'icon' => 'delete',
			'overlay' => 'true',
		), '', array(
		)) . '
');
	}
	$__finalCompiled .= '

';
	$__compilerTemp1 = $__templater->mergeChoiceOptions(array(), $__vars['userGroups']);
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<h2 class="block-tabHeader tabs hScroller" data-xf-init="tabs h-scroller" role="tablist">
			<span class="hScroller-scroll">
				<a class="tabs-tab is-active" role="tab" tabindex="0" aria-controls="promotion-options">' . 'Promotion options' . '</a>
				' . $__templater->callMacro('helper_criteria', 'user_tabs', array(
		'userTabTitle' => 'Apply this promotion while...',
	), $__vars) . '
			</span>
		</h2>

		<ul class="tabPanes block-body">
			<li class="is-active" role="tabpanel" id="promotion-options">
				' . $__templater->formTextBoxRow(array(
		'name' => 'title',
		'value' => $__vars['userGroupPromotion']['title'],
		'maxlength' => $__templater->func('max_length', array($__vars['userGroupPromotion'], 'title', ), false),
	), array(
		'label' => 'Title',
	)) . '

				' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'active',
		'selected' => $__vars['userGroupPromotion']['active'],
		'label' => 'Promotion is active',
		'_type' => 'option',
	)), array(
	)) . '

				<hr class="formRowSep" />

				' . $__templater->formCheckBoxRow(array(
		'name' => 'extra_user_group_ids[]',
		'value' => $__vars['userGroupPromotion']['extra_user_group_ids'],
		'listclass' => 'listColumns',
	), $__compilerTemp1, array(
		'rowid' => 'addUserGroups',
		'label' => 'Add user to user groups',
		'hint' => '
						' . $__templater->formCheckBox(array(
		'standalone' => 'true',
	), array(array(
		'check-all' => '#addUserGroups',
		'label' => 'Select all',
		'_type' => 'option',
	))) . '
					',
	)) . '
			</li>

			' . $__templater->callMacro('helper_criteria', 'user_panes', array(
		'criteria' => $__templater->method($__vars['userCriteria'], 'getCriteriaForTemplate', array()),
		'data' => $__templater->method($__vars['userCriteria'], 'getExtraTemplateData', array()),
	), $__vars) . '
		</ul>

		' . $__templater->formSubmitRow(array(
		'sticky' => 'true',
		'icon' => 'save',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('user-group-promotions/save', $__vars['userGroupPromotion'], ), false),
		'ajax' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});