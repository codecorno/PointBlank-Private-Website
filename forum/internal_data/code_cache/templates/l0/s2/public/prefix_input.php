<?php
// FROM HASH: 99156deb37df745be9c8c11b13e50cc0
return array('macros' => array('template' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(), $__arguments, $__vars);
	$__finalCompiled .= '
	<script type="text/template">
		' . $__templater->func('mustache', array('#groups', '
			' . $__templater->func('mustache', array('#title', '
				<h3 class="menu-header">' . $__templater->func('mustache', array('title', ), true) . '</h3>
			')) . '
			' . $__templater->func('mustache', array('#prefixes', '
				<div class="menu-row">
					<a class="menuPrefix ' . $__templater->func('mustache', array('css_class', ), true) . '"
						data-prefix-id="' . $__templater->func('mustache', array('prefix_id', ), true) . '"
						data-prefix-class="' . $__templater->func('mustache', array('css_class', ), true) . '"
						role="option">' . $__templater->func('mustache', array('title', ), true) . '</a>
				</div>
			')) . '
			<hr class="menu-separator" />
		')) . '
		<div class="menu-row">
			<a class="menuPrefix menuPrefix--none"
				data-prefix-id="0"
				data-prefix-class=""
				role="option">' . $__vars['xf']['language']['parenthesis_open'] . 'No prefix' . $__vars['xf']['language']['parenthesis_close'] . '</a>
		</div>
	</script>
';
	return $__finalCompiled;
},
'menu_prefixes' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'prefixes' => '!',
		'prefixType' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	$__compilerTemp1 = $__templater->func('array_keys', array($__vars['prefixes'], ), false);
	if ($__templater->isTraversable($__compilerTemp1)) {
		foreach ($__compilerTemp1 AS $__vars['groupId']) {
			$__finalCompiled .= '
		';
			if ($__vars['groupId'] > 0) {
				$__finalCompiled .= '
			<h3 class="menu-header">' . $__templater->func('prefix_group', array($__vars['prefixType'], $__vars['groupId'], ), true) . '</h3>
			<div class="menu-row">
				';
				if ($__templater->isTraversable($__vars['prefixes'][$__vars['groupId']])) {
					foreach ($__vars['prefixes'][$__vars['groupId']] AS $__vars['prefixId'] => $__vars['prefix']) {
						$__finalCompiled .= '
					<a class="menuPrefix ' . $__templater->escape($__vars['prefix']['css_class']) . '"
						data-prefix-id="' . $__templater->escape($__vars['prefixId']) . '"
						data-prefix-class="' . $__templater->escape($__vars['prefix']['css_class']) . '"
						role="option">' . $__templater->func('prefix_title', array($__vars['prefixType'], $__vars['prefixId'], ), true) . '</a>
				';
					}
				}
				$__finalCompiled .= '
			</div>
			<hr class="menu-separator" />
		';
			} else {
				$__finalCompiled .= '
			<div class="menu-row">
				';
				if ($__templater->isTraversable($__vars['prefixes'][$__vars['groupId']])) {
					foreach ($__vars['prefixes'][$__vars['groupId']] AS $__vars['prefixId'] => $__vars['prefix']) {
						$__finalCompiled .= '
					<a class="menuPrefix ' . $__templater->escape($__vars['prefix']['css_class']) . '"
						data-prefix-id="' . $__templater->escape($__vars['prefixId']) . '"
						data-prefix-class="' . $__templater->escape($__vars['prefix']['css_class']) . '"
						role="option">' . $__templater->func('prefix_title', array($__vars['prefixType'], $__vars['prefixId'], ), true) . '</a>
				';
					}
				}
				$__finalCompiled .= '
			</div>
			<hr class="menu-separator" />
		';
			}
			$__finalCompiled .= '
	';
		}
	}
	$__finalCompiled .= '
	<div class="menu-row">
		<a class="menuPrefix menuPrefix--none"
			data-prefix-id="0"
			data-prefix-class=""
			role="option">' . $__vars['xf']['language']['parenthesis_open'] . 'No prefix' . $__vars['xf']['language']['parenthesis_close'] . '</a>
	</div>
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->includeJs(array(
		'src' => 'xf/prefix_menu.js',
		'min' => '1',
	));
	$__finalCompiled .= '
';
	$__templater->includeCss('prefix_menu.less');
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	if ($__vars['rows']) {
		$__compilerTemp1 .= '
		<textarea rows="' . $__templater->escape($__vars['rows']) . '" name="' . $__templater->escape($__vars['textboxName']) . '"
			data-xf-init="textarea-handler ' . $__templater->escape($__vars['xfInit']) . '" data-single-line="true"
			class="input js-titleInput ' . $__templater->escape($__vars['textboxClass']) . '"
			autocomplete="off"
			' . $__templater->filter($__vars['attrsHtml'], array(array('raw', array()),), true) . '>' . $__templater->escape($__vars['textboxValue']) . '</textarea>
	';
	} else {
		$__compilerTemp1 .= '
		<input type="text" name="' . $__templater->escape($__vars['textboxName']) . '"
			data-xf-init="' . $__templater->escape($__vars['xfInit']) . '"
			class="input js-titleInput ' . $__templater->escape($__vars['textboxClass']) . '"
			value="' . $__templater->escape($__vars['textboxValue']) . '"
			autocomplete="off"
			' . $__templater->filter($__vars['attrsHtml'], array(array('raw', array()),), true) . ' />
	';
	}
	$__vars['textbox'] = $__templater->preEscaped('
	' . $__compilerTemp1 . '
');
	$__finalCompiled .= '
';
	$__vars['selectbox'] = $__templater->preEscaped('
	' . $__templater->callMacro('prefix_macros', 'select', array(
		'name' => $__vars['prefixName'],
		'prefixes' => $__vars['prefixes'],
		'selected' => $__vars['prefixValue'],
		'type' => $__vars['prefixType'],
		'class' => 'js-prefixSelect u-noJsOnly',
	), $__vars) . '
');
	$__finalCompiled .= '

';
	if ($__vars['prefixes'] OR $__vars['href']) {
		$__finalCompiled .= '
	<div class="js-prefixContainer">
		<div class="inputGroup inputGroup--joined u-jsOnly">
			<div class="inputGroup-text">
				<div class="' . $__templater->escape($__vars['prefixClass']) . '" data-xf-init="prefix-menu"' . ($__vars['href'] ? ((((' data-href="' . $__templater->escape($__vars['href'])) . '" data-listen-to="') . $__templater->escape($__vars['listenTo'])) . '"') : '') . ' >
					<a class="menuTrigger menuTrigger--prefix" data-xf-click="menu" role="button" tabindex="0" aria-expanded="false" aria-haspopup="true">
						<span class="js-activePrefix">' . $__vars['xf']['language']['parenthesis_open'] . 'No prefix' . $__vars['xf']['language']['parenthesis_close'] . '</span>
					</a>
					<div class="menu" data-menu="menu" aria-hidden="true">
						<div class="menu-content">
							<div class="menu-scroller js-prefixMenuContent" role="listbox">
								' . $__templater->callMacro(null, 'template', array(), $__vars) . '
							</div>
						</div>
					</div>
				</div>
				' . $__templater->filter($__vars['selectbox'], array(array('raw', array()),), true) . '
			</div>
			' . $__templater->filter($__vars['textbox'], array(array('raw', array()),), true) . '
		</div>
		<noscript>
			<div class="inputGroup">
				' . $__templater->filter($__vars['selectbox'], array(array('raw', array()),), true) . '
				<span class="inputGroup-splitter"></span>
				' . $__templater->filter($__vars['textbox'], array(array('raw', array()),), true) . '
			</div>
		</noscript>
	</div>
';
	} else {
		$__finalCompiled .= '
	' . $__templater->filter($__vars['textbox'], array(array('raw', array()),), true) . '
';
	}
	$__finalCompiled .= '

' . '

';
	return $__finalCompiled;
});