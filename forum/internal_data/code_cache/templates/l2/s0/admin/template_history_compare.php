<?php
// FROM HASH: 9d76940318b5c157f44e264f8d060771
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Version comparison');
	$__finalCompiled .= '

';
	$__templater->includeCss('public:diff.less');
	$__finalCompiled .= '

<div class="block">
	<div class="block-container">
		<div class="block-body block-row block-body--contained" dir="ltr">
			<ol class="diffList diffList--code">
				';
	if ($__templater->isTraversable($__vars['diffs'])) {
		foreach ($__vars['diffs'] AS $__vars['diff']) {
			$__finalCompiled .= '
					';
			$__vars['diffHtml'] = $__templater->preEscaped($__templater->filter($__vars['diff']['1'], array(array('join', array('<br />', )),), true));
			$__finalCompiled .= '
					<li class="diffList-line diffList-line--' . $__templater->escape($__vars['diff']['0']) . '">' . (($__templater->func('trim', array($__vars['diffHtml'], ), false) !== '') ? $__templater->escape($__vars['diffHtml']) : '&nbsp;') . '</li>
				';
		}
	}
	$__finalCompiled .= '
			</ol>
		</div>
	</div>
</div>';
	return $__finalCompiled;
});