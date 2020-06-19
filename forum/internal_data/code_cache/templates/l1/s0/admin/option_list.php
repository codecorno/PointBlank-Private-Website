<?php
// FROM HASH: 0c5bb37eef1ef622f0100146d3a3f78f
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped($__templater->escape($__vars['group']['title']));
	$__finalCompiled .= '
';
	$__compilerTemp1 = '';
	if ($__templater->isTraversable($__vars['groups'])) {
		foreach ($__vars['groups'] AS $__vars['_group']) {
			$__compilerTemp1 .= '
					<a href="' . $__templater->func('link', array('options/groups/', $__vars['_group'], ), true) . '" class="menu-linkRow ' . (($__vars['_group']['group_id'] == $__vars['group']['group_id']) ? 'is-selected' : '') . '">
						';
			if ($__vars['_group']['icon']) {
				$__compilerTemp1 .= '
							' . $__templater->fontAwesome($__templater->escape($__vars['_group']['icon']) . ' fa-fw', array(
				)) . '
						';
			} else {
				$__compilerTemp1 .= '
							' . $__templater->fontAwesome('fa-cogs fa-fw', array(
				)) . '
						';
			}
			$__compilerTemp1 .= '
						' . $__templater->escape($__vars['_group']['title']) . '
					</a>
				';
		}
	}
	$__templater->pageParams['pageH1'] = $__templater->preEscaped('
	<span class="menuTrigger" data-xf-click="menu" role="button" tabindex="0" aria-expanded="false" aria-haspopup="true">' . $__templater->escape($__vars['group']['title']) . '</span>
	<div class="menu menu--medium" data-menu="menu" aria-hidden="true">
		<div class="menu-content">
			<h3 class="menu-header">' . 'Options groups' . '</h3>
			<div class="menu-scroller">
				' . $__compilerTemp1 . '
			</div>
		</div>
	</div>
');
	$__finalCompiled .= '
';
	$__templater->pageParams['pageDescription'] = $__templater->preEscaped($__templater->escape($__vars['group']['description']));
	$__templater->pageParams['pageDescriptionMeta'] = true;
	$__finalCompiled .= '

';
	$__compilerTemp2 = '';
	if ($__vars['canAdd']) {
		$__compilerTemp2 .= '
		' . $__templater->button('Add option', array(
			'href' => $__templater->func('link', array('options/add', null, array('group_id' => $__vars['group']['group_id'], ), ), false),
			'icon' => 'add',
		), '', array(
		)) . '
	';
	}
	$__compilerTemp3 = '';
	if ($__templater->method($__vars['group'], 'canEdit', array())) {
		$__compilerTemp3 .= '
		' . $__templater->button('Edit group', array(
			'href' => $__templater->func('link', array('options/groups/edit', $__vars['group'], ), false),
			'icon' => 'edit',
		), '', array(
		)) . '
	';
	}
	$__templater->pageParams['pageAction'] = $__templater->preEscaped(trim('
	' . $__compilerTemp2 . '
	' . $__compilerTemp3 . '
'));
	$__finalCompiled .= '

' . $__templater->callMacro('option_macros', 'option_form_block', array(
		'group' => $__vars['group'],
		'options' => $__vars['group']['Options'],
	), $__vars);
	return $__finalCompiled;
});