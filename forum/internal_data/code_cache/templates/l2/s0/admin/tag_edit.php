<?php
// FROM HASH: 7099a2b6eabf5e663c1f1c5635ef1aa5
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->method($__vars['tag'], 'isInsert', array())) {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Adicionar tag');
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Edit tag' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['tag']['tag']));
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	if ($__templater->method($__vars['tag'], 'isUpdate', array())) {
		$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('', array(
			'href' => $__templater->func('link', array('tags/delete', $__vars['tag'], ), false),
			'icon' => 'delete',
			'overlay' => 'true',
		), '', array(
		)) . '
');
	}
	$__finalCompiled .= '

' . $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formTextBoxRow(array(
		'name' => 'tag',
		'value' => $__vars['tag']['tag'],
		'maxlength' => $__templater->func('max_length', array($__vars['tag'], 'tag', ), false),
	), array(
		'label' => 'Tag',
	)) . '

			' . $__templater->formTextBoxRow(array(
		'name' => 'tag_url',
		'value' => $__vars['tag']['tag_url'],
		'maxlength' => $__templater->func('max_length', array($__vars['tag'], 'tag_url', ), false),
		'dir' => 'ltr',
	), array(
		'label' => 'Versão do URL',
		'explain' => 'Isso será usado para identificar exclusivamente essa tag em um URL. Ele pode conter apenas a-z, 0-9, - e _. Deixe isso em branco para gerá-lo automaticamente.',
	)) . '

			' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'permanent',
		'selected' => $__vars['tag']['permanent'],
		'label' => 'Permanente',
		'hint' => 'Fazer uma tag permanente impede que ele seja removido quando ele não está mais em uso.',
		'_type' => 'option',
	)), array(
	)) . '
		</div>

		' . $__templater->formSubmitRow(array(
		'sticky' => 'true',
		'icon' => 'save',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('tags/save', $__vars['tag'], ), false),
		'ajax' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});