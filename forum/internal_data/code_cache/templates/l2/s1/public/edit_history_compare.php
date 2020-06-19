<?php
// FROM HASH: 9b3512540be96cf36bb117ac6e276040
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped($__templater->escape($__vars['title']) . ' - ' . 'Version comparison');
	$__finalCompiled .= '

';
	$__templater->breadcrumbs($__vars['breadcrumbs']);
	$__finalCompiled .= '

';
	$__templater->includeCss('public:diff.less');
	$__finalCompiled .= '

<div class="block">
	<div class="block-container">
		<div class="block-body block-row">
			<ul class="diffList">
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
			</ul>
		</div>
	</div>
</div>';
	return $__finalCompiled;
});