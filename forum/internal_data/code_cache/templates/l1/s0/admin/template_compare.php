<?php
// FROM HASH: e47876ecf9f4b770ecd76ba60b8bc82a
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Custom changes' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['template']['title']));
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