<?php
// FROM HASH: 3d4f80ff67696fef10d604699864a978
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->formRow('

	<ul class="inputChoices inputChoices--noChoice">
		<li class="inputChoices-choice">
			<div>' . 'Texto desativado nos nomes de usuário' . $__vars['xf']['language']['label_separator'] . '</div>
			' . $__templater->formTextArea(array(
		'name' => $__vars['inputName'] . '[disallowedNames]',
		'value' => $__vars['option']['option_value']['disallowedNames'],
		'autosize' => 'true',
	)) . '
			<dfn class="inputChoices-explain">' . 'As palavras ou frases neste campo não serão permitidas em nenhuma parte dos nomes de usuário. Coloque cada palavra ou frase em sua própria linha. Digitar "alcatrão" não permitirá "estrela" e "manchar" etc.' . '</dfn>
		</li>
		<li class="inputChoices-choice">
			<div>' . 'Nome de usuário corresponde à expressão regular' . $__vars['xf']['language']['label_separator'] . '</div>
			' . $__templater->formTextBox(array(
		'name' => $__vars['inputName'] . '[matchRegex]',
		'value' => $__vars['option']['option_value']['matchRegex'],
	)) . '
			<dfn class="inputChoices-explain">' . 'Isso exige que os nomes de usuário de novos registros correspondam à expressão regular fornecida. <b>Nota</b>: use uma expressão completa, incluindo delimitadores e desvios.' . '</dfn>
		</li>
	</ul>
', array(
		'label' => $__templater->escape($__vars['option']['title']),
		'hint' => $__templater->escape($__vars['hintHtml']),
		'explain' => $__templater->escape($__vars['explainHtml']),
		'html' => $__templater->escape($__vars['listedHtml']),
	));
	return $__finalCompiled;
});