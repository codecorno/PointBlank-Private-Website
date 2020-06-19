<?php
// FROM HASH: a15896a3afa70ac38a7e6bef7deafed6
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped(($__vars['group'] ? $__templater->escape($__vars['group']['title']) : 'Ungrouped properties'));
	$__finalCompiled .= '
';
	if ($__vars['group']) {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageDescription'] = $__templater->preEscaped($__templater->escape($__vars['group']['description']));
		$__templater->pageParams['pageDescriptionMeta'] = true;
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	$__templater->setPageParam('breadcrumbPath', 'styles');
	$__finalCompiled .= '
';
	$__templater->setPageParam('section', 'styleProperties');
	$__finalCompiled .= '

';
	$__templater->breadcrumb($__templater->preEscaped($__templater->escape($__vars['style']['title']) . ' - ' . 'Style properties'), $__templater->func('link', array('styles/style-properties', $__vars['style'], ), false), array(
	));
	$__finalCompiled .= '

';
	if ($__templater->method($__vars['style'], 'canEditStylePropertyDefinitions', array()) AND $__vars['group']) {
		$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('
		' . 'Add property' . '
	', array(
			'href' => $__templater->func('link', array('style-properties/add', null, array('style_id' => $__vars['style']['style_id'], 'group' => $__vars['group']['group_name'], ), ), false),
			'icon' => 'add',
		), '', array(
		)) . '

	' . $__templater->button('
		' . 'Edit' . '
	', array(
			'href' => $__templater->func('link', array('style-properties/groups/edit', $__vars['group'], ), false),
			'data-xf-click' => 'overlay',
		), '', array(
		)) . '
');
	}
	$__finalCompiled .= '

';
	if (!$__templater->test($__vars['propertyMap'], 'empty', array())) {
		$__finalCompiled .= '
	';
		$__compilerTemp1 = '';
		if ($__templater->isTraversable($__vars['groups'])) {
			foreach ($__vars['groups'] AS $__vars['_group']) {
				$__compilerTemp1 .= '
								<a href="' . $__templater->func('link', array('styles/style-properties/group', $__vars['style'], array('group' => $__vars['_group']['group_name'], ), ), true) . '"
									class="menu-linkRow ' . (($__vars['group'] AND ($__vars['group']['group_name'] == $__vars['_group']['group_name'])) ? 'is-selected' : '') . '">' . $__templater->escape($__vars['_group']['title']) . '</a>
							';
			}
		}
		$__compilerTemp2 = '';
		if ($__vars['hasUngrouped']) {
			$__compilerTemp2 .= '
								<a href="' . $__templater->func('link', array('styles/style-properties/group', $__vars['style'], array('ungrouped' => 1, ), ), true) . '"
									class="menu-linkRow ' . ((!$__vars['group']) ? 'is-selected' : '') . '">
									' . 'Ungrouped properties' . '</a>
							';
		}
		$__vars['hasActive'] = false;
		$__compilerTemp3 = '';
		$__compilerTemp4 = '';
		$__compilerTemp4 .= '
						';
		$__vars['propValueGroup'] = null;
		$__compilerTemp4 .= '
						';
		if ($__templater->isTraversable($__vars['propertyMap'])) {
			foreach ($__vars['propertyMap'] AS $__vars['map']) {
				if ($__vars['map']['Property']['property_type'] == 'value') {
					$__compilerTemp4 .= '
							';
					if ($__vars['propValueGroup'] != $__vars['map']['Property']['value_group']) {
						$__compilerTemp4 .= '
								';
						if ($__vars['map']['Property']['value_group_title']) {
							$__compilerTemp4 .= '
									<div class="block-formSectionHeader">
										<span class="block-formSectionHeader-aligner">
											' . $__templater->escape($__vars['map']['Property']['value_group_title']) . '
										</span>
									</div>
								';
						} else {
							$__compilerTemp4 .= '
									<hr class="formRowSep" />
								';
						}
						$__compilerTemp4 .= '
							';
					}
					$__compilerTemp4 .= '
							' . $__templater->callMacro('style_property_macros', 'property_edit', array(
						'property' => $__vars['map']['Property'],
						'definitionEditable' => $__templater->method($__vars['map'], 'isDefinitionEditable', array()),
						'customizationState' => $__templater->method($__vars['map'], 'getCustomizationState', array()),
					), $__vars) . '
							';
					$__vars['hasActive'] = true;
					$__compilerTemp4 .= '
							';
					$__vars['propValueGroup'] = $__vars['map']['Property']['value_group'];
					$__compilerTemp4 .= '
						';
				}
			}
		}
		$__compilerTemp4 .= '
					';
		if (strlen(trim($__compilerTemp4)) > 0) {
			$__compilerTemp3 .= '
				<div data-toggle-wrapper="1">
					<h3 class="block-header">
						<span class="collapseTrigger collapseTrigger--block is-active"
							data-xf-click="toggle"
							data-target="< :up :next"
							data-xf-init="toggle-storage"
							data-storage-key="sp-settings-' . $__templater->escape($__vars['group']['group_name']) . '">
							' . 'Settings' . '
						</span>
					</h3>
					<div class="block-body block-body--collapsible is-active">
					' . $__compilerTemp4 . '
					</div>
				</div>
			';
		}
		$__compilerTemp5 = '';
		if ($__templater->isTraversable($__vars['propertyMap'])) {
			foreach ($__vars['propertyMap'] AS $__vars['map']) {
				if ($__vars['map']['Property']['property_type'] == 'css') {
					$__compilerTemp5 .= '
				' . $__templater->callMacro('style_property_macros', 'property_edit', array(
						'property' => $__vars['map']['Property'],
						'definitionEditable' => $__templater->method($__vars['map'], 'isDefinitionEditable', array()),
						'customizationState' => $__templater->method($__vars['map'], 'getCustomizationState', array()),
						'isActive' => ($__vars['hasActive'] ? false : true),
					), $__vars) . '
				';
					$__vars['hasActive'] = true;
					$__compilerTemp5 .= '
			';
				}
			}
		}
		$__finalCompiled .= $__templater->form('

		<div class="block-outer">
			<div class="block-outer-main">
				' . $__templater->callMacro('style_macros', 'style_change_menu', array(
			'styleTree' => $__vars['styleTree'],
			'currentStyle' => $__vars['style'],
			'route' => 'styles/style-properties/group',
			'routeParams' => array('group' => $__vars['group']['group_name'], ),
		), $__vars) . '

				' . $__templater->button('Group' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['group']['title']), array(
			'class' => 'button--link menuTrigger',
			'data-xf-click' => 'menu',
			'aria-expanded' => 'false',
			'aria-haspopup' => 'true',
		), '', array(
		)) . '
				<div class="menu" data-menu="menu" aria-hidden="true">
					<div class="menu-content">
						<h3 class="menu-header">' . 'Groups' . '</h3>
						<div class="menu-scroller">
							' . $__compilerTemp1 . '
							' . $__compilerTemp2 . '
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="block-container">
			' . '' . '
			' . $__compilerTemp3 . '

			' . $__compilerTemp5 . '

			' . $__templater->formSubmitRow(array(
			'icon' => 'save',
			'sticky' => 'true',
		), array(
			'rowtype' => 'simple',
		)) . '
		</div>
	', array(
			'action' => $__templater->func('link', array('styles/style-properties/group', $__vars['style'], $__vars['urlParams'], ), false),
			'class' => 'block p-styleProperties',
			'ajax' => 'true',
			'data-json-name' => 'json',
			'data-json-opt-in' => 'properties,properties_listed',
			'autocomplete' => 'off',
		)) . '
';
	} else {
		$__finalCompiled .= '
	<div class="blockMessage">' . 'This group does not have any style properties.' . '</div>
';
	}
	$__finalCompiled .= '

' . $__templater->callMacro('public:color_picker_macros', 'color_picker_scripts', array(
		'colorData' => $__vars['colorData'],
	), $__vars);
	return $__finalCompiled;
});