<?php
// FROM HASH: 779f56f6b0c1c7e520540b2eb27d7607
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Confirm action');
	$__finalCompiled .= '

';
	if ($__vars['profileUsed']) {
		$__finalCompiled .= '
	<div class="block">
		<div class="block-container">
			<div class="block-body">
				';
		$__compilerTemp1 = '';
		if ($__templater->isTraversable($__vars['profileUsed'])) {
			foreach ($__vars['profileUsed'] AS $__vars['purchasable']) {
				$__compilerTemp1 .= trim('
							<li><a href="' . $__templater->escape($__vars['purchasable']['link']) . '">' . $__templater->escape($__vars['purchasable']['title']) . '</a></li>
						');
			}
		}
		$__finalCompiled .= $__templater->formInfoRow('
					' . 'This payment profile cannot be deleted because it is in use by the following purchasables' . $__vars['xf']['language']['label_separator'] . '
					<ul class="listInline listInline--comma">
						' . $__compilerTemp1 . '
					</ul>
				', array(
			'rowtype' => 'confirm',
		)) . '
			</div>
		</div>
	</div>
';
	} else {
		$__finalCompiled .= '
	' . $__templater->form('
		<div class="block-container">
			<div class="block-body">
				' . $__templater->formInfoRow('
					<div class="blockMessage blockMessage--important blockMessage--iconic">' . 'Note: Deleting payment profiles will not affect any existing purchases (or subscriptions) that have been made using this profile.' . '</div>

					' . 'Please confirm that you want to delete the following' . $__vars['xf']['language']['label_separator'] . '
					<strong><a href="' . $__templater->func('link', array('payment-profiles/edit', $__vars['profile'], ), true) . '">' . $__templater->escape($__vars['profile']['title']) . '</a></strong>
				', array(
			'rowtype' => 'confirm',
		)) . '
			</div>
			' . $__templater->formSubmitRow(array(
			'icon' => 'delete',
		), array(
			'rowtype' => 'simple',
		)) . '
		</div>
	', array(
			'action' => $__templater->func('link', array('payment-profiles/delete', $__vars['profile'], ), false),
			'ajax' => 'true',
			'class' => 'block',
		)) . '
';
	}
	return $__finalCompiled;
});