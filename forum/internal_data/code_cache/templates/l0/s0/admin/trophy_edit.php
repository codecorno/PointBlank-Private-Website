<?php
// FROM HASH: a139484bc0649f0953688fb555d41f22
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->method($__vars['trophy'], 'isInsert', array())) {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Add trophy');
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Edit trophy' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['trophy']['title']));
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	if ($__templater->method($__vars['trophy'], 'isUpdate', array())) {
		$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('', array(
			'href' => $__templater->func('link', array('trophies/delete', $__vars['trophy'], ), false),
			'icon' => 'delete',
			'overlay' => 'true',
		), '', array(
		)) . '
');
	}
	$__finalCompiled .= '

' . $__templater->form('
	<div class="block-container">
		<h2 class="block-tabHeader tabs hScroller" data-xf-init="h-scroller tabs" role="tablist">
			<span class="hScroller-scroll">
				<a class="tabs-tab is-active" role="tab" tabindex="0" aria-controls="trophy-options">' . 'Trophy options' . '</a>
				' . $__templater->callMacro('helper_criteria', 'user_tabs', array(
		'userTabTitle' => 'Award this trophy if...',
	), $__vars) . '
			</span>
		</h2>

		<ul class="block-body tabPanes">
			<li class="is-active" role="tabpanel" id="trophy-options">
				' . $__templater->formTextBoxRow(array(
		'name' => 'title',
		'value' => ($__vars['trophy']['trophy_id'] ? $__vars['trophy']['MasterTitle']['phrase_text'] : ''),
	), array(
		'label' => 'Title',
	)) . '

				' . $__templater->formNumberBoxRow(array(
		'name' => 'trophy_points',
		'value' => $__vars['trophy']['trophy_points'],
		'min' => '0',
	), array(
		'label' => 'Trophy points',
		'explain' => 'These points can be used to track progression and change a user\'s title.',
	)) . '

				' . $__templater->formTextAreaRow(array(
		'name' => 'description',
		'value' => ($__vars['trophy']['trophy_id'] ? $__vars['trophy']['MasterDescription']['phrase_text'] : ''),
		'autosize' => 'true',
	), array(
		'label' => 'Description',
		'hint' => 'You may use HTML',
		'explain' => 'Optionally describe the trophy and the criteria the user needs to meet to be awarded it.',
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
		'action' => $__templater->func('link', array('trophies/save', $__vars['trophy'], ), false),
		'ajax' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});