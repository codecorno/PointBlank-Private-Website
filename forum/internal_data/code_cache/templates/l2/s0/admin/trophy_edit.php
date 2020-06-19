<?php
// FROM HASH: a139484bc0649f0953688fb555d41f22
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->method($__vars['trophy'], 'isInsert', array())) {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Adicionar troféu');
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Editar troféu' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['trophy']['title']));
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
				<a class="tabs-tab is-active" role="tab" tabindex="0" aria-controls="trophy-options">' . 'Opções de troféu' . '</a>
				' . $__templater->callMacro('helper_criteria', 'user_tabs', array(
		'userTabTitle' => 'Atribuir este troféu se...',
	), $__vars) . '
			</span>
		</h2>

		<ul class="block-body tabPanes">
			<li class="is-active" role="tabpanel" id="trophy-options">
				' . $__templater->formTextBoxRow(array(
		'name' => 'title',
		'value' => ($__vars['trophy']['trophy_id'] ? $__vars['trophy']['MasterTitle']['phrase_text'] : ''),
	), array(
		'label' => 'Título',
	)) . '

				' . $__templater->formNumberBoxRow(array(
		'name' => 'trophy_points',
		'value' => $__vars['trophy']['trophy_points'],
		'min' => '0',
	), array(
		'label' => 'Pontos de troféu',
		'explain' => 'Esses pontos podem ser usados para acompanhar a progressão e alterar o título de um usuário.',
	)) . '

				' . $__templater->formTextAreaRow(array(
		'name' => 'description',
		'value' => ($__vars['trophy']['trophy_id'] ? $__vars['trophy']['MasterDescription']['phrase_text'] : ''),
		'autosize' => 'true',
	), array(
		'label' => 'Descrição',
		'hint' => 'Você pode usar HTML',
		'explain' => 'Opcionalmente, descreva o troféu e os critérios que o usuário precisa atender para ser premiado.',
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