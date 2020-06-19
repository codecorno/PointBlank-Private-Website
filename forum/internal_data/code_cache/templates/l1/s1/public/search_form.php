<?php
// FROM HASH: 6a67cee0757ae923a580055f72af5994
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Search');
	$__finalCompiled .= '

';
	$__templater->setPageParam('head.' . 'robots', $__templater->preEscaped('<meta name="robots" content="noindex" />'));
	$__finalCompiled .= '

';
	if ($__vars['type']) {
		$__finalCompiled .= '
	';
		$__templater->breadcrumb($__templater->preEscaped('Search'), $__templater->func('link', array('full:search', ), false), array(
		));
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	if ($__templater->isTraversable($__vars['tabs'])) {
		foreach ($__vars['tabs'] AS $__vars['tabType'] => $__vars['tab']) {
			$__compilerTemp1 .= '
					<a href="' . $__templater->func('link', array('search', null, array('type' => $__vars['tabType'], ), ), true) . '" class="tabs-tab' . (($__vars['type'] == $__vars['tabType']) ? ' is-active' : '') . '">' . $__templater->escape($__vars['tab']['title']) . '</a>
				';
		}
	}
	$__compilerTemp2 = '';
	if ($__vars['xf']['options']['enableTagging']) {
		$__compilerTemp2 .= '
					<a href="' . $__templater->func('link', array('tags', ), true) . '" class="tabs-tab">' . 'Search tags' . '</a>
				';
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<h2 class="block-tabHeader tabs hScroller" data-xf-init="h-scroller">
			<span class="hScroller-scroll">
				<a href="' . $__templater->func('link', array('search', ), true) . '" class="tabs-tab' . ((!$__vars['type']) ? ' is-active' : '') . '">' . 'Search everything' . '</a>
				' . $__compilerTemp1 . '
				' . $__compilerTemp2 . '
			</span>
		</h2>

		<div class="block-body">
			' . $__templater->includeTemplate($__vars['formTemplateName'], $__vars) . '
		</div>

		' . $__templater->formSubmitRow(array(
		'icon' => 'search',
		'sticky' => 'true',
	), array(
	)) . '
	</div>

	' . $__templater->formHiddenVal('search_type', $__vars['type'], array(
	)) . '
', array(
		'action' => $__templater->func('link', array('search/search', ), false),
		'class' => 'block',
		'ajax' => 'true',
	));
	return $__finalCompiled;
});