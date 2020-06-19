<?php
// FROM HASH: e66fdf2e034d06b9df7ae2bf74717733
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Search tags');
	$__finalCompiled .= '

';
	$__templater->breadcrumb($__templater->preEscaped('Tags'), $__templater->func('link', array('tags', ), false), array(
	));
	$__finalCompiled .= '

';
	$__templater->setPageParam('head.' . 'robots', $__templater->preEscaped('<meta name="robots" content="noindex" />'));
	$__finalCompiled .= '

';
	$__templater->includeCss('tag.less');
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
					<a href="' . $__templater->func('link', array('tags', ), true) . '" class="tabs-tab is-active">' . 'Search tags' . '</a>
				';
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<h2 class="block-tabHeader tabs hScroller" data-xf-init="h-scroller">
			<span class="hScroller-scroll">
				<a href="' . $__templater->func('link', array('search', ), true) . '" class="tabs-tab">' . 'Search everything' . '</a>
				' . $__compilerTemp1 . '
				' . $__compilerTemp2 . '
			</span>
		</h2>
		<div class="block-body">
			' . $__templater->formTokenInputRow(array(
		'name' => 'tags',
		'value' => $__vars['tags'],
		'href' => $__templater->func('link', array('misc/tag-auto-complete', ), false),
	), array(
		'label' => 'Tags',
	)) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'icon' => 'search',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('tags', ), false),
		'class' => 'block',
		'ajax' => 'true',
	)) . '

';
	if ($__vars['tagCloud']) {
		$__finalCompiled .= '
	<div class="block">
		<div class="block-container">
			<h3 class="block-header">' . 'Popular tags' . '</h3>
			<div class="block-body block-row tagCloud">
			';
		if ($__templater->isTraversable($__vars['tagCloud'])) {
			foreach ($__vars['tagCloud'] AS $__vars['cloudEntry']) {
				$__finalCompiled .= '
				<a href="' . $__templater->func('link', array('tags', $__vars['cloudEntry']['tag'], ), true) . '" class="tagCloud-tag tagCloud-tagLevel' . $__templater->escape($__vars['cloudEntry']['level']) . '">' . $__templater->escape($__vars['cloudEntry']['tag']['tag']) . '</a>
			';
			}
		}
		$__finalCompiled .= '
			</div>
		</div>
	</div>
';
	}
	return $__finalCompiled;
});