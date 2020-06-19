<?php
// FROM HASH: 1fc8fea0966b2e08df0abbc491377cbe
return array('macros' => array('language_change_menu' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'languageTree' => '!',
		'route' => '!',
		'routeParams' => array(),
		'currentLanguage' => null,
		'linkClass' => 'button button--link',
	), $__arguments, $__vars);
	$__finalCompiled .= '

	<a class="' . $__templater->escape($__vars['linkClass']) . ' menuTrigger"
		data-xf-click="menu"
		role="button"
		tabindex="0"
		aria-expanded="false"
		aria-haspopup="true">' . 'Language' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['currentLanguage']['title']) . '</a>

	<div class="menu" data-menu="menu" aria-hidden="true">
		<div class="menu-content">
			<h3 class="menu-header">' . 'Languages' . '</h3>
			';
	$__compilerTemp1 = $__templater->method($__vars['languageTree'], 'getFlattened', array());
	if ($__templater->isTraversable($__compilerTemp1)) {
		foreach ($__compilerTemp1 AS $__vars['treeEntry']) {
			$__finalCompiled .= '
				<a href="' . $__templater->func('link', array($__vars['route'], $__vars['treeEntry']['record'], $__vars['routeParams'], ), true) . '"
				   class="menu-linkRow ' . (($__vars['currentLanguage'] AND ($__vars['currentLanguage']['language_id'] == $__vars['treeEntry']['record']['language_id'])) ? 'is-selected' : '') . '">
					<span class="u-depth' . $__templater->escape($__vars['treeEntry']['depth']) . '">' . $__templater->escape($__vars['treeEntry']['record']['title']) . '</span>
				</a>
			';
		}
	}
	$__finalCompiled .= '
		</div>
	</div>
';
	return $__finalCompiled;
},
'language_select' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'languageTree' => '!',
		'languageId' => '!',
		'name' => 'language_id',
		'row' => true,
		'class' => '',
	), $__arguments, $__vars);
	$__finalCompiled .= '

	';
	$__compilerTemp1 = array();
	$__compilerTemp2 = $__templater->method($__vars['languageTree'], 'getFlattened', array());
	if ($__templater->isTraversable($__compilerTemp2)) {
		foreach ($__compilerTemp2 AS $__vars['treeEntry']) {
			$__compilerTemp1[] = array(
				'value' => $__vars['treeEntry']['record']['language_id'],
				'label' => $__templater->func('repeat', array('--', $__vars['treeEntry']['depth'], ), true) . ' ' . $__templater->escape($__vars['treeEntry']['record']['title']),
				'_type' => 'option',
			);
		}
	}
	$__vars['select'] = $__templater->preEscaped('
		' . $__templater->formSelect(array(
		'name' => $__vars['name'],
		'value' => $__vars['languageId'],
		'class' => $__vars['class'],
	), $__compilerTemp1) . '
	');
	$__finalCompiled .= '

	';
	if ($__vars['row']) {
		$__finalCompiled .= '
		' . $__templater->formRow('
			' . $__templater->filter($__vars['select'], array(array('raw', array()),), true) . '
		', array(
			'label' => 'Language',
			'rowtype' => 'input',
		)) . '
	';
	} else {
		$__finalCompiled .= '
		' . $__templater->filter($__vars['select'], array(array('raw', array()),), true) . '
	';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '

';
	return $__finalCompiled;
});