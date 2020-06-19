<?php
// FROM HASH: f6e013c7a7869eac495fabdf8f1f77ac
return array('macros' => array('toolbar_block' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'type' => '',
		'name' => '',
		'buttons' => '!',
		'buttonData' => '!',
		'toolbarType' => '!',
		'displayTooltips' => false,
		'includeDropdownControls' => false,
	), $__arguments, $__vars);
	$__finalCompiled .= '
	<div class="block-body">
		<div class="block-row">
			<div class="fr-box fr-box--editor fr-box--editor--' . $__templater->escape($__vars['type']) . ' fr-ltr fr-basic fr-top">
				<div class="js-dragList js-dragList-' . $__templater->escape($__vars['toolbarType']) . ' toolbar-' . $__templater->escape($__vars['toolbarType']) . ' fr-toolbar fr-ltr fr-desktop fr-top fr-basic">
					';
	if ($__templater->isTraversable($__vars['buttons'])) {
		foreach ($__vars['buttons'] AS $__vars['cmd']) {
			$__finalCompiled .= '
						';
			if ($__vars['buttonData'][$__vars['cmd']]) {
				$__finalCompiled .= '
							' . $__templater->callMacro(null, 'toolbar_button', array(
					'button' => $__vars['buttonData'][$__vars['cmd']],
					'cmd' => $__vars['cmd'],
					'displayTooltips' => $__vars['displayTooltips'],
					'includeDropdownControls' => $__vars['includeDropdownControls'],
				), $__vars) . '
						';
			}
			$__finalCompiled .= '
					';
		}
	}
	$__finalCompiled .= '
					';
	if ($__vars['name']) {
		$__finalCompiled .= '
						' . $__templater->formHiddenVal($__vars['name'], '', array(
			'class' => 'js-dragListValue',
		)) . '
					';
	}
	$__finalCompiled .= '
				</div>
			</div>
		</div>
	</div>
';
	return $__finalCompiled;
},
'toolbar_button' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'button' => '!',
		'cmd' => '!',
		'displayTooltips' => false,
		'includeDropdownControls' => false,
	), $__arguments, $__vars);
	$__finalCompiled .= '
	<div data-cmd="' . $__templater->escape($__vars['cmd']) . '" data-xf-init="' . ($__vars['displayTooltips'] ? 'tooltip' : '') . '" title="' . ($__vars['displayTooltips'] ? $__templater->escape($__vars['button']['title']) : '') . '"
		class="' . ($__vars['button']['type'] ? ('toolbar-' . $__templater->escape($__vars['button']['type'])) : '') . ' fr-command fr-btn ' . ((($__vars['button']['type'] == 'dropdown') OR ($__vars['button']['type'] == 'editable_dropdown')) ? 'fr-dropdown' : '') . ' ' . ($__vars['button']['fa'] ? 'fr-btn-font_awesome' : 'fr-btn-image') . '">
		' . $__templater->callMacro(null, 'toolbar_icon', array(
		'button' => $__vars['button'],
	), $__vars) . '
		<span class="fr-sr-only">' . $__templater->escape($__vars['button']['title']) . '</span>
	</div>
	';
	if ($__vars['button']['type'] == 'separator') {
		$__finalCompiled .= '
		<div class="fr-separator fr' . $__templater->escape($__vars['cmd']) . '"></div>
	';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},
'toolbar_icon' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'button' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	if ($__vars['button']['fa']) {
		$__finalCompiled .= '
		' . $__templater->fontAwesome($__templater->escape($__vars['button']['fa']), array(
		)) . '
	';
	} else if ($__vars['button']['image']) {
		$__finalCompiled .= '
		<img src="' . $__templater->escape($__vars['button']['image']) . '" alt="" />
	';
	} else if ($__vars['button']['text']) {
		$__finalCompiled .= '
		<span style="text-align: center;">' . $__templater->escape($__vars['button']['text']) . '</span>
	';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},
'setup' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	$__templater->includeCss('public:editor.less');
	$__finalCompiled .= '
	';
	$__templater->includeCss('button_manager.less');
	$__finalCompiled .= '
	';
	$__templater->includeJs(array(
		'dev' => 'vendor/dragula/dragula.js, xf/editor_manager.js',
		'prod' => 'xf/editor_manager-compiled.js',
	));
	$__finalCompiled .= '

	<script class="js-extraPhrases" type="application/json">
		{
			"buttons_menus_may_not_be_duplicated": "' . $__templater->filter('Buttons and menus may not be duplicated.', array(array('escape', array('json', )),), true) . '",
			"button_removed": "' . $__templater->filter('Button removed', array(array('escape', array('json', )),), true) . '"
		}
	</script>
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Edit toolbar' . ': ' . $__templater->escape($__vars['typeTitle']));
	$__finalCompiled .= '

' . $__templater->callMacro(null, 'setup', array(), $__vars) . '

';
	$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('
		' . 'Reset button configuration' . '
	', array(
		'href' => $__templater->func('link', array('button-manager/reset', null, array('type' => $__vars['type'], ), ), false),
		'data-xf-click' => 'overlay',
		'icon' => 'refresh',
	), '', array(
	)) . '
');
	$__finalCompiled .= '

' . $__templater->form('
	<div class="block">
		<div class="block-container">
			<h2 class="block-header">
				' . 'Available buttons and dropdown menus' . '
			</h2>

			' . $__templater->callMacro(null, 'toolbar_block', array(
		'buttons' => $__templater->func('array_keys', array($__vars['buttonData'], ), false),
		'buttonData' => $__vars['buttonData'],
		'toolbarType' => 'commandTray',
		'displayTooltips' => true,
		'includeDropdownControls' => true,
	), $__vars) . '

			<div class="block-row block-row--minor u-muted">
				' . 'This is a list of all editor buttons that can be added to the editor. The bottom row contains the available custom dropdown menus. Move buttons from here to the toolbar below.' . '
			</div>
		</div>
	</div>

	<div class="block">
		<div class="block-container">
			<h3 class="block-header">
				' . $__templater->escape($__vars['typeTitle']) . '
			</h3>

			' . $__templater->callMacro(null, 'toolbar_block', array(
		'type' => $__vars['type'],
		'name' => 'editor_toolbar_config[' . $__vars['type'] . ']',
		'buttons' => $__vars['toolbarButtons'],
		'buttonData' => $__vars['buttonData'],
		'toolbarType' => 'toolbar',
	), $__vars) . '

			<div class="block-row block-row--minor u-muted">
				' . $__templater->escape($__vars['typeDescription']) . '
			</div>

			' . $__templater->formSubmitRow(array(
		'icon' => 'save',
	), array(
		'rowtype' => 'simple',
	)) . '
		</div>
	</div>
', array(
		'action' => $__templater->func('link', array('button-manager/save', null, array('type' => $__vars['type'], ), ), false),
		'ajax' => 'true',
		'data-xf-init' => 'editor-manager',
		'data-force-flash-message' => 'on',
	)) . '

' . '

' . '

' . '

';
	return $__finalCompiled;
});