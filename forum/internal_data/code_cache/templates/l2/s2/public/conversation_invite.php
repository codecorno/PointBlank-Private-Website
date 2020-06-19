<?php
// FROM HASH: e2a0d6d455d410143c51ee248e258b46
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Convidar membros para conversar');
	$__finalCompiled .= '

';
	$__templater->breadcrumb($__templater->preEscaped('Conversas'), $__templater->func('link', array('conversations', ), false), array(
	));
	$__finalCompiled .= '
';
	$__templater->breadcrumb($__templater->preEscaped($__templater->escape($__vars['conversation']['title'])), $__templater->func('link', array('conversations', $__vars['conversation'], ), false), array(
	));
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	if ($__vars['remainingRecipients'] > 1) {
		$__compilerTemp1 .= 'Separe os nomes com uma vírgula.';
	}
	$__compilerTemp2 = '';
	if ($__vars['remainingRecipients'] > 0) {
		$__compilerTemp2 .= 'Você pode convidar até ' . $__templater->filter($__vars['remainingRecipients'], array(array('number', array()),), true) . ' membro(s).';
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formTokenInputRow(array(
		'name' => 'recipients',
		'href' => $__templater->func('link', array('members/find', ), false),
		'max-tokens' => (($__vars['remainingRecipients'] > -1) ? $__vars['remainingRecipients'] : null),
	), array(
		'label' => 'Convidar membros',
		'explain' => '
					' . $__compilerTemp1 . ' ' . 'Os membros convidados poderão ver toda a conversa desde o início.' . '
					' . $__compilerTemp2 . '
				',
	)) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'submit' => 'Convidar',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('conversations/invite', $__vars['conversation'], ), false),
		'class' => 'block',
		'ajax' => 'true',
	));
	return $__finalCompiled;
});