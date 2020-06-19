<?php
// FROM HASH: d634c250d3e1ca80417a7c1ad6ccf83b
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__vars['tooltip']) {
		$__finalCompiled .= '
	<div class="tooltip-content-inner">
		';
		$__compilerTemp1 = '';
		if ($__vars['added']) {
			$__compilerTemp1 .= '
					' . 'Bookmark added' . '
				';
		} else if ($__templater->method($__vars['bookmark'], 'isInsert', array())) {
			$__compilerTemp1 .= '
					' . 'Add bookmark' . '
				';
		} else {
			$__compilerTemp1 .= '
					' . 'Edit bookmark' . '
				';
		}
		$__finalCompiled .= $__templater->form('
			<h3 class="block-minorHeader">
				' . $__compilerTemp1 . '
			</h3>
			<div class="block-body">
				<div class="block-row">
					' . $__templater->formTextAreaRow(array(
			'name' => 'message',
			'value' => $__vars['bookmark']['message'],
			'autosize' => 'true',
			'rows' => '1',
			'maxlength' => $__templater->func('max_length', array($__vars['bookmark'], 'message', ), false),
		), array(
			'label' => 'Mensagem',
			'rowtype' => 'fullWidth noPadding',
			'hint' => 'Opcional',
		)) . '
				</div>
				<div class="block-row">
					' . $__templater->formRow('
						' . $__templater->callMacro('bookmark_macros', 'filter', array(
			'label' => $__templater->filter($__vars['bookmark']['labels'], array(array('pluck', array('label', )),array('join', array(', ', )),), false),
			'allLabels' => $__vars['allLabels'],
			'maxTokens' => '0',
			'placeholder' => '',
		), $__vars) . '
					', array(
			'label' => 'Labels',
			'hint' => 'Opcional',
			'rowtype' => 'fullWidth noPadding',
			'explain' => 'Multiple labels may be separated by commas.',
		)) . '
				</div>
				<div class="block-row">
					<div class="formButtonGroup formButtonGroup--simple formButtonGroup--close">
						<div class="formButtonGroup-primary">
							' . $__templater->button('', array(
			'type' => 'submit',
			'class' => 'button--primary',
			'icon' => 'save',
		), '', array(
		)) . '
							' . $__templater->button('', array(
			'type' => 'submit',
			'name' => 'delete',
			'icon' => 'delete',
		), '', array(
		)) . '
						</div>
					</div>
				</div>
			</div>
		', array(
			'action' => $__vars['confirmUrl'],
			'class' => '',
			'ajax' => 'true',
		)) . '
	</div>
';
	} else {
		$__finalCompiled .= '
	';
		if ($__templater->method($__vars['bookmark'], 'isInsert', array())) {
			$__finalCompiled .= '
		';
			$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Add bookmark');
			$__finalCompiled .= '
	';
		} else {
			$__finalCompiled .= '
		';
			$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Edit bookmark');
			$__finalCompiled .= '
	';
		}
		$__finalCompiled .= '


	' . $__templater->form('
		<div class="block-container">
			<div class="block-body">
				' . $__templater->formTextAreaRow(array(
			'name' => 'message',
			'value' => $__vars['bookmark']['message'],
			'autosize' => 'true',
			'maxlength' => $__templater->func('max_length', array($__vars['bookmark'], 'message', ), false),
		), array(
			'label' => 'Mensagem',
			'hint' => 'Opcional',
		)) . '

				' . $__templater->formRow('
					' . $__templater->callMacro('bookmark_macros', 'filter', array(
			'label' => $__templater->filter($__vars['bookmark']['labels'], array(array('pluck', array('label', )),array('join', array(', ', )),), false),
			'allLabels' => $__vars['allLabels'],
			'maxTokens' => '0',
			'placeholder' => '',
		), $__vars) . '
					<div class="formRow-explain">
						' . 'Multiple labels may be separated by commas.' . '
					</div>
				', array(
			'label' => 'Labels',
			'rowtype' => 'input',
			'hint' => 'Opcional',
		)) . '
			</div>
			' . $__templater->formSubmitRow(array(
			'icon' => 'save',
		), array(
			'html' => '
				' . $__templater->button('', array(
			'type' => 'submit',
			'name' => 'delete',
			'icon' => 'delete',
		), '', array(
		)) . '
			',
		)) . '
		</div>
	', array(
			'action' => $__vars['confirmUrl'],
			'class' => 'block',
			'ajax' => 'true',
		)) . '
';
	}
	return $__finalCompiled;
});