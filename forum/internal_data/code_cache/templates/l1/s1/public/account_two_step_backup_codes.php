<?php
// FROM HASH: d29ea3b61e91b66c1a3ace99664a486b
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Two-step verification backup codes');
	$__finalCompiled .= '

';
	$__templater->wrapTemplate('account_wrapper', $__vars);
	$__finalCompiled .= '

<div class="block">
	<div class="block-container">
		<div class="block-body">
			<div class="block-row block-row--separated">
				' . 'Two-step verification backup codes have automatically been generated. Each of these codes can be used once in case you don\'t have access to other means of verification. These codes should be saved in a secure location.' . '
			</div>
			<div class="block-row block-row--separated">
				<ul class="listColumns listColumns--spaced listPlain">
				';
	if ($__templater->isTraversable($__vars['codes'])) {
		foreach ($__vars['codes'] AS $__vars['code']) {
			$__finalCompiled .= '
					<li><div>' . $__templater->escape($__vars['code']) . '</div></li>
				';
		}
	}
	$__finalCompiled .= '
				</ul>
			</div>
		</div>
		<div class="block-footer">
			<span class="block-footer-controls">
				' . $__templater->button('I have saved the backup codes', array(
		'class' => 'button--primary js-overlayClose',
	), '', array(
	)) . '
			</span>
		</div>
	</div>
</div>';
	return $__finalCompiled;
});