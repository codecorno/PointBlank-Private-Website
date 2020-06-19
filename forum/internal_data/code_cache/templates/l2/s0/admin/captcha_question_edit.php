<?php
// FROM HASH: 9760fedc1b20cbcfc9f6852856c12557
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->method($__vars['question'], 'isInsert', array())) {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Adicionar pergunta');
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Editar pergunta' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['question']['question']));
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	if ($__templater->method($__vars['question'], 'isUpdate', array())) {
		$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('', array(
			'href' => $__templater->func('link', array('captcha-questions/delete', $__vars['question'], ), false),
			'icon' => 'delete',
			'overlay' => 'true',
		), '', array(
		)) . '
');
	}
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	if ($__templater->isTraversable($__vars['question']['answers'])) {
		foreach ($__vars['question']['answers'] AS $__vars['answer']) {
			$__compilerTemp1 .= '
						<li class="u-inputSpacer">
							' . $__templater->formTextBox(array(
				'name' => 'answers[]',
				'value' => $__vars['answer'],
				'placeholder' => 'Resposta' . $__vars['xf']['language']['ellipsis'],
			)) . '
						</li>
					';
		}
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formTextBoxRow(array(
		'name' => 'question',
		'value' => ($__templater->method($__vars['question'], 'exists', array()) ? $__vars['question']['question'] : ''),
		'maxlength' => $__templater->func('max_length', array($__vars['question'], 'question', ), false),
	), array(
		'label' => 'Pergunta',
	)) . '

			' . $__templater->formRow('

				<ul class="inputList">
					' . $__compilerTemp1 . '
					<li data-xf-init="field-adder">
						' . $__templater->formTextBox(array(
		'name' => 'answers[]',
		'value' => '',
		'placeholder' => 'Resposta' . $__vars['xf']['language']['ellipsis'],
	)) . '
					</li>
				</ul>
			', array(
		'rowtype' => 'input',
		'label' => 'Resposta correta(s)',
		'explain' => 'Você pode adicionar quantas respostas corretas quiser. Os campos de resposta vazios serão ignorados. As respostas não diferenciam maiúsculas de minúsculas.',
	)) . '

			<hr class="formRowSep" />

			' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'active',
		'value' => '1',
		'selected' => $__vars['question']['active'],
		'label' => 'Pergunta está ativa',
		'hint' => 'As perguntas inativas não são apresentadas aos visitantes.',
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
		'action' => $__templater->func('link', array('captcha-questions/save', $__vars['question'], ), false),
		'ajax' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});