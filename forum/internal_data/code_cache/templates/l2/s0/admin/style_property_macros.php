<?php
// FROM HASH: fb548551d0a495783726d86536c1ec90
return array('macros' => array('property_edit' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'property' => '!',
		'definitionEditable' => false,
		'isActive' => true,
		'customizationState' => '',
		'submitName' => 'properties',
	), $__arguments, $__vars);
	$__finalCompiled .= '

	';
	if ($__vars['property']['property_type'] == 'css') {
		$__finalCompiled .= '
		' . $__templater->callMacro(null, 'property_edit_css', array(
			'property' => $__vars['property'],
			'definitionEditable' => $__vars['definitionEditable'],
			'isActive' => $__vars['isActive'],
			'customizationState' => $__vars['customizationState'],
			'submitName' => $__vars['submitName'],
		), $__vars) . '
	';
	} else {
		$__finalCompiled .= '
		' . $__templater->callMacro(null, 'property_edit_value', array(
			'property' => $__vars['property'],
			'definitionEditable' => $__vars['definitionEditable'],
			'customizationState' => $__vars['customizationState'],
			'submitName' => $__vars['submitName'],
		), $__vars) . '
	';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},
'property_edit_value' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'property' => '!',
		'definitionEditable' => '!',
		'customizationState' => '!',
		'submitName' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '

	';
	$__templater->includeCss('style_properties.less');
	$__finalCompiled .= '

	';
	$__vars['titleHtml'] = $__templater->preEscaped('<span class="u-anchorTarget" id="sp-' . $__templater->escape($__vars['property']['property_name']) . '"></span>' . $__templater->escape($__vars['property']['title']));
	$__finalCompiled .= '

	';
	$__vars['formBaseKey'] = $__vars['submitName'] . '[' . $__vars['property']['property_name'] . ']';
	$__finalCompiled .= '

	';
	$__compilerTemp1 = '';
	if ($__vars['xf']['development']) {
		$__compilerTemp1 .= $__templater->escape($__vars['property']['property_name']) . ' ' . $__templater->filter($__vars['property']['display_order'], array(array('parens', array()),), true);
	}
	$__compilerTemp2 = '';
	if ($__vars['definitionEditable']) {
		$__compilerTemp2 .= '
			<a href="' . $__templater->func('link', array('style-properties/edit', $__vars['property'], ), true) . '">' . 'Editar' . '</a>
		';
	}
	$__compilerTemp3 = '';
	if ($__vars['property']['addon_id'] AND ($__vars['property']['Group'] AND (($__vars['property']['addon_id'] != $__vars['property']['Group']['addon_id']) AND ($__vars['property']['addon_id'] != 'XF')))) {
		$__compilerTemp3 .= '
			<span class="formRow-hint-featured">
				' . $__templater->escape($__vars['property']['AddOn']['title']) . '
			</span>
		';
	}
	$__vars['hintHtml'] = $__templater->preEscaped(trim('
		' . $__compilerTemp1 . '
		' . $__compilerTemp2 . '

		' . $__compilerTemp3 . '

		' . $__templater->callMacro(null, 'customization_hint', array(
		'state' => $__vars['customizationState'],
		'submitName' => $__vars['submitName'],
		'property' => $__vars['property'],
		'checkbox' => true,
	), $__vars) . '

		' . $__templater->formHiddenVal($__vars['submitName'] . '_listed[]', $__vars['property']['property_name'], array(
	)) . '
	'));
	$__finalCompiled .= '

	';
	$__vars['valueOptions'] = $__templater->method($__vars['property'], 'getValueOptions', array());
	$__finalCompiled .= '

	';
	$__vars['rowClass'] = ($__vars['property']['depends_on'] ? ('js-stylePropDependsOn-' . $__vars['property']['depends_on']) : '') . ' xf-' . $__vars['property']['property_name'];
	$__finalCompiled .= '

	';
	if ($__vars['property']['value_type'] == 'boolean') {
		$__finalCompiled .= '

		<!--boolean-->
		' . $__templater->formCheckBoxRow(array(
		), array(array(
			'name' => $__vars['formBaseKey'],
			'value' => '1',
			'selected' => $__vars['property']['property_value'] == 1,
			'data-xf-init' => 'disabler',
			'data-container' => '.js-stylePropDependsOn-' . $__vars['property']['property_name'],
			'data-optional' => 'true',
			'data-hide' => ($__vars['valueOptions']['hideDependent'] ?: 'false'),
			'label' => $__templater->escape($__vars['titleHtml']),
			'hint' => $__templater->escape($__vars['property']['description']),
			'_type' => 'option',
		)), array(
			'rowclass' => $__vars['rowClass'],
			'hint' => $__templater->escape($__vars['hintHtml']),
		)) . '


	';
	} else if ($__vars['property']['value_type'] == 'radio') {
		$__finalCompiled .= '

		';
		$__compilerTemp4 = $__templater->mergeChoiceOptions(array(), $__vars['valueOptions']);
		$__finalCompiled .= $__templater->formRadioRow(array(
			'name' => $__vars['formBaseKey'],
			'value' => $__vars['property']['property_value'],
		), $__compilerTemp4, array(
			'rowclass' => $__vars['rowClass'],
			'label' => $__templater->escape($__vars['titleHtml']),
			'hint' => $__templater->escape($__vars['hintHtml']),
			'explain' => $__templater->escape($__vars['property']['description']),
		)) . '


	';
	} else if ($__vars['property']['value_type'] == 'select') {
		$__finalCompiled .= '

		';
		$__compilerTemp5 = $__templater->mergeChoiceOptions(array(), $__vars['valueOptions']);
		$__finalCompiled .= $__templater->formSelectRow(array(
			'name' => $__vars['formBaseKey'],
			'value' => $__vars['property']['property_value'],
		), $__compilerTemp5, array(
			'rowclass' => $__vars['rowClass'],
			'label' => $__templater->escape($__vars['titleHtml']),
			'hint' => $__templater->escape($__vars['hintHtml']),
			'explain' => $__templater->escape($__vars['property']['description']),
		)) . '

	';
	} else if ($__vars['property']['value_type'] == 'number') {
		$__finalCompiled .= '

		' . $__templater->formNumberBoxRow(array(
			'name' => $__vars['formBaseKey'],
			'value' => $__vars['property']['property_value'],
			'min' => $__vars['valueOptions']['min'],
			'max' => $__vars['valueOptions']['max'],
			'step' => $__vars['valueOptions']['step'],
			'units' => $__vars['valueOptions']['units'],
		), array(
			'rowclass' => $__vars['rowClass'],
			'rowid' => 'propRow_' . $__vars['property']['property_name'],
			'label' => $__templater->escape($__vars['titleHtml']),
			'hint' => $__templater->escape($__vars['hintHtml']),
			'explain' => $__templater->escape($__vars['property']['description']),
		)) . '

	';
	} else if ($__vars['property']['value_type'] == 'color') {
		$__finalCompiled .= '

		' . $__templater->callMacro('public:color_picker_macros', 'color_picker', array(
			'name' => $__vars['formBaseKey'],
			'value' => $__vars['property']['property_value'],
			'mapName' => '@xf-' . $__vars['property']['property_name'],
			'allowPalette' => ($__vars['valueOptions']['hidePalette'] ? 'false' : 'true'),
			'label' => $__vars['titleHtml'],
			'hint' => $__vars['hintHtml'],
			'explain' => $__vars['property']['description'],
			'rowClass' => 'formRow-styleProperty ' . $__vars['rowClass'],
			'includeScripts' => false,
			'showTxt' => true,
		), $__vars) . '

	';
	} else if ($__vars['property']['value_type'] == 'unit') {
		$__finalCompiled .= '

		' . $__templater->formTextBoxRow(array(
			'name' => $__vars['formBaseKey'],
			'value' => $__vars['property']['property_value'],
			'class' => 'input--number',
			'dir' => 'ltr',
		), array(
			'rowclass' => $__vars['rowClass'],
			'label' => $__templater->escape($__vars['titleHtml']),
			'hint' => $__templater->escape($__vars['hintHtml']),
			'explain' => $__templater->escape($__vars['property']['description']),
		)) . '

	';
	} else if ($__vars['property']['value_type'] == 'template') {
		$__finalCompiled .= '

		';
		if ($__vars['valueOptions']['template']) {
			$__finalCompiled .= '
			';
			$__vars['includeHtml'] = $__templater->preEscaped($__templater->includeTemplate($__vars['valueOptions']['template'], $__vars));
			$__finalCompiled .= '
			';
			if ($__templater->test($__vars['includeHtml'], 'empty', array())) {
				$__finalCompiled .= '
				' . $__templater->formRow('
					<div class="blockMessage blockMessage--error blockMessage--iconic">' . 'Modelo ' . $__templater->escape($__vars['valueOptions']['template']) . ' não encontrado.' . '</div>
				', array(
					'label' => $__templater->escape($__vars['titleHtml']),
					'hint' => $__templater->escape($__vars['hintHtml']),
				)) . '
			';
			} else {
				$__finalCompiled .= '
				' . $__templater->escape($__vars['includeHtml']) . '
			';
			}
			$__finalCompiled .= '
		';
		} else {
			$__finalCompiled .= '
			' . $__templater->formRow('
				<div class="blockMessage blockMessage--error blockMessage--iconic">' . 'No template specified.' . '</div>
			', array(
				'label' => $__templater->escape($__vars['titleHtml']),
				'hint' => $__templater->escape($__vars['hintHtml']),
			)) . '
		';
		}
		$__finalCompiled .= '

	';
	} else if (($__vars['property']['value_type'] == 'string') AND ($__vars['valueOptions']['rows'] > 1)) {
		$__finalCompiled .= '


		' . $__templater->formTextAreaRow(array(
			'name' => $__vars['formBaseKey'],
			'value' => $__vars['property']['property_value'],
			'rows' => $__vars['valueOptions']['rows'],
			'autosize' => 'true',
			'class' => $__vars['valueOptions']['class'],
			'code' => $__vars['valueOptions']['code'],
			'dir' => 'auto',
		), array(
			'rowclass' => $__vars['rowClass'],
			'label' => $__templater->escape($__vars['titleHtml']),
			'hint' => $__templater->escape($__vars['hintHtml']),
			'explain' => $__templater->escape($__vars['property']['description']),
		)) . '

	';
	} else {
		$__finalCompiled .= '

		' . $__templater->formTextBoxRow(array(
			'name' => $__vars['formBaseKey'],
			'value' => $__vars['property']['property_value'],
			'type' => $__vars['valueOptions']['type'],
			'class' => $__vars['valueOptions']['class'],
			'code' => $__vars['valueOptions']['code'],
			'dir' => 'auto',
		), array(
			'rowclass' => $__vars['rowClass'],
			'label' => $__templater->escape($__vars['titleHtml']),
			'hint' => $__templater->escape($__vars['hintHtml']),
			'explain' => $__templater->escape($__vars['property']['description']),
		)) . '

	';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},
'property_edit_css' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'property' => '!',
		'definitionEditable' => '!',
		'isActive' => true,
		'customizationState' => '!',
		'submitName' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '

	';
	$__templater->includeCss('style_properties.less');
	$__finalCompiled .= '
	';
	$__templater->includeCss('public:color_picker.less');
	$__finalCompiled .= '
	';
	$__templater->includeJs(array(
		'src' => 'xf/color_picker.js',
		'min' => '1',
	));
	$__finalCompiled .= '

	';
	$__vars['formBaseKey'] = $__vars['submitName'] . '[' . $__vars['property']['property_name'] . ']';
	$__finalCompiled .= '

	<div class="cssPropertyWrapper" data-toggle-wrapper="1">
		<h3 class="block-header block-header--separated">
			<span class="collapseTrigger collapseTrigger--block ' . ($__vars['isActive'] ? 'is-active' : '') . '"
				data-xf-click="toggle"
				data-target="< :up :next"
				data-xf-init="toggle-storage"
				data-storage-key="sp-' . $__templater->escape($__vars['property']['property_name']) . '">

				' . $__templater->callMacro(null, 'customization_hint', array(
		'state' => $__vars['customizationState'],
		'submitName' => $__vars['submitName'],
		'property' => $__vars['property'],
	), $__vars) . '

				<span class="u-anchorTarget" id="sp-' . $__templater->escape($__vars['property']['property_name']) . '"></span><span>' . $__templater->escape($__vars['property']['title']) . '</span>
				';
	if ($__vars['property']['description']) {
		$__finalCompiled .= '<span class="block-desc">' . $__templater->escape($__vars['property']['description']) . '</span>';
	}
	$__finalCompiled .= '
			</span>
			' . $__templater->formHiddenVal($__vars['submitName'] . '_listed[]', $__vars['property']['property_name'], array(
	)) . '
		</h3>
		<div class="block-body block-body--collapsible ' . ($__vars['isActive'] ? 'is-active' : '') . '">
			<div class="block-row">
				';
	$__compilerTemp1 = '';
	$__compilerTemp1 .= '
						';
	if ($__vars['customizationState'] == 'custom') {
		$__compilerTemp1 .= '
							<span class="cssPropertyRevert" data-xf-init="tooltip" title="' . $__templater->filter('Reverter customizações', array(array('for_attr', array()),), true) . '">
								' . $__templater->callMacro(null, 'revert_code', array(
			'submitName' => $__vars['submitName'],
			'property' => $__vars['property'],
			'label' => 'Revert customized value',
			'container' => '< .block-row',
		), $__vars) . '
							</span>
						';
	}
	$__compilerTemp1 .= '
						';
	if ($__vars['definitionEditable']) {
		$__compilerTemp1 .= '
							<span class="u-pullRight">
								' . $__templater->button('', array(
			'href' => $__templater->func('link', array('style-properties/edit', $__vars['property'], ), false),
			'class' => 'button--link button--small',
			'icon' => 'edit',
		), '', array(
		)) . '
							</span>
						';
	}
	$__compilerTemp1 .= '
						';
	if ($__vars['xf']['development']) {
		$__compilerTemp1 .= '<div class="u-muted">' . $__templater->escape($__vars['property']['property_name']) . ' ' . $__templater->filter($__vars['property']['display_order'], array(array('parens', array()),), true) . '</div>';
	}
	$__compilerTemp1 .= '
					';
	if (strlen(trim($__compilerTemp1)) > 0) {
		$__finalCompiled .= '
					<div class="cssPropertyDescription">
					' . $__compilerTemp1 . '
					</div>
				';
	}
	$__finalCompiled .= '

				<ul class="cssProperty">
					';
	if ($__templater->method($__vars['property'], 'isValidCssComponent', array('text', ))) {
		$__finalCompiled .= '
						<li>
							<h4 class="cssPropertyHeader">' . 'Texto' . '</h4>
							<table class="cssPropertySet">
							<tr class="cssPropertySet-headerRow">
								<th>' . 'Size' . '</th>
								<th>' . 'Cor' . '</th>
								<th>' . 'Peso' . '</th>
							</tr>
							<tr>
								<td>' . $__templater->formTextBox(array(
			'name' => $__vars['formBaseKey'] . '[font-size]',
			'value' => $__templater->method($__vars['property'], 'getCssPropertyValue', array('font-size', )),
			'class' => 'input--cssProp input--cssLength',
			'dir' => 'ltr',
		)) . '
								</td>

								<td><div class="inputGroup inputGroup--joined inputGroup--colorSmall" data-xf-init="color-picker">
									' . $__templater->formTextBox(array(
			'name' => $__vars['formBaseKey'] . '[color]',
			'value' => $__templater->method($__vars['property'], 'getCssPropertyValue', array('color', )),
			'class' => 'input--cssProp',
			'dir' => 'ltr',
		)) . '
									<div class="inputGroup-text"><span class="colorPickerBox js-colorPickerTrigger"></span></div>
								</div></td>

								<td>' . $__templater->formSelect(array(
			'name' => $__vars['formBaseKey'] . '[font-weight]',
			'value' => $__templater->method($__vars['property'], 'getCssPropertyValue', array('font-weight', )),
			'class' => 'input--cssProp',
		), array(array(
			'value' => '',
			'label' => '&nbsp;',
			'_type' => 'option',
		),
		array(
			'value' => '@xf-fontWeightHeavy',
			'label' => 'Bold',
			'_type' => 'option',
		),
		array(
			'value' => '@xf-fontWeightNormal',
			'label' => 'Normal',
			'_type' => 'option',
		),
		array(
			'value' => '@xf-fontWeightLight',
			'label' => 'Light',
			'_type' => 'option',
		))) . '</td>
							</tr>
							<tr>
								<td colspan="3">
									' . $__templater->formCheckBox(array(
			'listclass' => 'inputChoices--cssTextOptions',
		), array(array(
			'name' => $__vars['formBaseKey'] . '[font-style]',
			'value' => 'italic',
			'selected' => ($__templater->method($__vars['property'], 'getCssPropertyValue', array('font-style', )) == 'italic'),
			'label' => '
											' . 'Itálico' . '
										',
			'_type' => 'option',
		),
		array(
			'name' => $__vars['formBaseKey'] . '[text-decoration]',
			'value' => 'underline',
			'selected' => ($__templater->method($__vars['property'], 'getCssPropertyValue', array('text-decoration', )) == 'underline'),
			'label' => '
											' . 'Sublinhado' . '
										',
			'_type' => 'option',
		),
		array(
			'name' => $__vars['formBaseKey'] . '[text-decoration]',
			'value' => 'none',
			'selected' => ($__templater->method($__vars['property'], 'getCssPropertyValue', array('text-decoration', )) == 'none'),
			'label' => '
											' . 'No text decoration' . '
										',
			'_type' => 'option',
		))) . '
								</td>
							</tr>
							</table>
						</li>
					';
	}
	$__finalCompiled .= '

					';
	if ($__templater->method($__vars['property'], 'isValidCssComponent', array('background', ))) {
		$__finalCompiled .= '
						<li>
							<h4 class="cssPropertyHeader">' . 'Background' . '</h4>
							<table class="cssPropertySet">
							<tr class="cssPropertySet-headerRow">
								<th>' . 'Cor' . '</th>
							</tr>
							<tr>
								<td><div class="inputGroup inputGroup--joined inputGroup--color" data-xf-init="color-picker">
									' . $__templater->formTextBox(array(
			'name' => $__vars['formBaseKey'] . '[background-color]',
			'value' => $__templater->method($__vars['property'], 'getCssPropertyValue', array('background-color', )),
			'class' => 'input--cssProp',
			'dir' => 'ltr',
		)) . '
									<div class="inputGroup-text"><span class="colorPickerBox js-colorPickerTrigger"></span></div>
								</div></td>
							</tr>
							<tr class="cssPropertySet-headerRow">
								<th>' . 'Imagem' . '</th>
							</tr>
							<tr>
								<td>' . $__templater->formTextBox(array(
			'name' => $__vars['formBaseKey'] . '[background-image]',
			'value' => $__templater->method($__vars['property'], 'getCssPropertyValue', array('background-image', )),
			'class' => 'input--cssProp input--colorWidthMatched',
			'dir' => 'ltr',
		)) . '</td>
							</tr>
							</table>
						</li>
					';
	}
	$__finalCompiled .= '

					';
	if ($__templater->method($__vars['property'], 'isValidCssComponent', array('border', )) OR $__templater->method($__vars['property'], 'isValidCssComponent', array('border_radius', ))) {
		$__finalCompiled .= '
						<li>
							<table class="cssPropertySet">
							<tr class="cssPropertySet-headerRow cssPropertySet-headerRow--separated">
								<th class="cssPropertySet-headerSetLabel">' . 'Borda' . '</th>
								';
		if ($__templater->method($__vars['property'], 'isValidCssComponent', array('border', ))) {
			$__finalCompiled .= '
									<th>' . 'Largura' . '</th>
									<th>' . 'Cor' . '</th>
								';
		}
		$__finalCompiled .= '
								';
		if ($__templater->method($__vars['property'], 'isValidCssComponent', array('border_radius', ))) {
			$__finalCompiled .= '
									<th>' . 'Raio' . '</th>
								';
		}
		$__finalCompiled .= '
							</tr>
							<tr>
								<td class="cssPropertySet-rowLabel">' . 'Todos' . '</td>

								';
		if ($__templater->method($__vars['property'], 'isValidCssComponent', array('border', ))) {
			$__finalCompiled .= '
									<td>' . $__templater->formTextBox(array(
				'name' => $__vars['formBaseKey'] . '[border-width]',
				'value' => $__templater->method($__vars['property'], 'getCssPropertyValue', array('border-width', )),
				'class' => 'input--cssProp input--cssLength',
				'dir' => 'ltr',
			)) . '</td>

									<td><div class="inputGroup inputGroup--joined inputGroup--colorSmall" data-xf-init="color-picker">
										' . $__templater->formTextBox(array(
				'name' => $__vars['formBaseKey'] . '[border-color]',
				'value' => $__templater->method($__vars['property'], 'getCssPropertyValue', array('border-color', )),
				'class' => 'input--cssProp',
				'dir' => 'ltr',
			)) . '
										<div class="inputGroup-text"><span class="colorPickerBox js-colorPickerTrigger"></span></div>
									</div></td>
								';
		}
		$__finalCompiled .= '

								';
		if ($__templater->method($__vars['property'], 'isValidCssComponent', array('border_radius', ))) {
			$__finalCompiled .= '
									<td>' . $__templater->formTextBox(array(
				'name' => $__vars['formBaseKey'] . '[border-radius]',
				'value' => $__templater->method($__vars['property'], 'getCssPropertyValue', array('border-radius', )),
				'class' => 'input--cssProp input--cssLength',
				'dir' => 'ltr',
			)) . '
									</td>
								';
		}
		$__finalCompiled .= '
							</tr>
							<tr>
								<td class="cssPropertySet-rowLabel">' . 'Topo' . '</td>

								';
		if ($__templater->method($__vars['property'], 'isValidCssComponent', array('border', ))) {
			$__finalCompiled .= '
									<td>' . $__templater->formTextBox(array(
				'name' => $__vars['formBaseKey'] . '[border-top-width]',
				'value' => $__templater->method($__vars['property'], 'getCssPropertyValue', array('border-top-width', )),
				'class' => 'input--cssProp input--cssLength',
				'dir' => 'ltr',
			)) . '
									</td>

									<td><div class="inputGroup inputGroup--joined inputGroup--colorSmall" data-xf-init="color-picker">
										' . $__templater->formTextBox(array(
				'name' => $__vars['formBaseKey'] . '[border-top-color]',
				'value' => $__templater->method($__vars['property'], 'getCssPropertyValue', array('border-top-color', )),
				'class' => 'input--cssProp',
				'dir' => 'ltr',
			)) . '
										<div class="inputGroup-text"><span class="colorPickerBox js-colorPickerTrigger"></span></div>
									</div></td>
								';
		}
		$__finalCompiled .= '

								';
		if ($__templater->method($__vars['property'], 'isValidCssComponent', array('border_radius', ))) {
			$__finalCompiled .= '
									<td>' . $__templater->formTextBox(array(
				'name' => $__vars['formBaseKey'] . '[border-top-left-radius]',
				'value' => $__templater->method($__vars['property'], 'getCssPropertyValue', array('border-top-left-radius', )),
				'class' => 'input--cssProp input--cssLength',
				'dir' => 'ltr',
			)) . '
									</td>
								';
		}
		$__finalCompiled .= '
							</tr>
							<tr>
								<td class="cssPropertySet-rowLabel">' . 'Direita' . '</td>

								';
		if ($__templater->method($__vars['property'], 'isValidCssComponent', array('border', ))) {
			$__finalCompiled .= '
									<td>' . $__templater->formTextBox(array(
				'name' => $__vars['formBaseKey'] . '[border-right-width]',
				'value' => $__templater->method($__vars['property'], 'getCssPropertyValue', array('border-right-width', )),
				'class' => 'input--cssProp input--cssLength',
				'dir' => 'ltr',
			)) . '
									</td>

									<td><div class="inputGroup inputGroup--joined inputGroup--colorSmall" data-xf-init="color-picker">
										' . $__templater->formTextBox(array(
				'name' => $__vars['formBaseKey'] . '[border-right-color]',
				'value' => $__templater->method($__vars['property'], 'getCssPropertyValue', array('border-right-color', )),
				'class' => 'input--cssProp',
				'dir' => 'ltr',
			)) . '
										<div class="inputGroup-text"><span class="colorPickerBox js-colorPickerTrigger"></span></div>
									</div></td>
								';
		}
		$__finalCompiled .= '

								';
		if ($__templater->method($__vars['property'], 'isValidCssComponent', array('border_radius', ))) {
			$__finalCompiled .= '
									<td>' . $__templater->formTextBox(array(
				'name' => $__vars['formBaseKey'] . '[border-top-right-radius]',
				'value' => $__templater->method($__vars['property'], 'getCssPropertyValue', array('border-top-right-radius', )),
				'class' => 'input--cssProp input--cssLength',
				'dir' => 'ltr',
			)) . '
									</td>
								';
		}
		$__finalCompiled .= '
							</tr>
							<tr>
								<td class="cssPropertySet-rowLabel">' . 'Inferior' . '</td>

								';
		if ($__templater->method($__vars['property'], 'isValidCssComponent', array('border', ))) {
			$__finalCompiled .= '
									<td>' . $__templater->formTextBox(array(
				'name' => $__vars['formBaseKey'] . '[border-bottom-width]',
				'value' => $__templater->method($__vars['property'], 'getCssPropertyValue', array('border-bottom-width', )),
				'class' => 'input--cssProp input--cssLength',
				'dir' => 'ltr',
			)) . '
									</td>

									<td><div class="inputGroup inputGroup--joined inputGroup--colorSmall" data-xf-init="color-picker">
										' . $__templater->formTextBox(array(
				'name' => $__vars['formBaseKey'] . '[border-bottom-color]',
				'value' => $__templater->method($__vars['property'], 'getCssPropertyValue', array('border-bottom-color', )),
				'class' => 'input--cssProp',
				'dir' => 'ltr',
			)) . '
										<div class="inputGroup-text"><span class="colorPickerBox js-colorPickerTrigger"></span></div>
									</div></td>
								';
		}
		$__finalCompiled .= '

								';
		if ($__templater->method($__vars['property'], 'isValidCssComponent', array('border_radius', ))) {
			$__finalCompiled .= '
									<td>' . $__templater->formTextBox(array(
				'name' => $__vars['formBaseKey'] . '[border-bottom-right-radius]',
				'value' => $__templater->method($__vars['property'], 'getCssPropertyValue', array('border-bottom-right-radius', )),
				'class' => 'input--cssProp input--cssLength',
				'dir' => 'ltr',
			)) . '
									</td>
								';
		}
		$__finalCompiled .= '
							</tr>
							<tr>
								<td class="cssPropertySet-rowLabel">' . 'Esquerda' . '</td>

								';
		if ($__templater->method($__vars['property'], 'isValidCssComponent', array('border', ))) {
			$__finalCompiled .= '
									<td>' . $__templater->formTextBox(array(
				'name' => $__vars['formBaseKey'] . '[border-left-width]',
				'value' => $__templater->method($__vars['property'], 'getCssPropertyValue', array('border-left-width', )),
				'class' => 'input--cssProp input--cssLength',
				'dir' => 'ltr',
			)) . '
									</td>

									<td><div class="inputGroup inputGroup--joined inputGroup--colorSmall" data-xf-init="color-picker">
										' . $__templater->formTextBox(array(
				'name' => $__vars['formBaseKey'] . '[border-left-color]',
				'value' => $__templater->method($__vars['property'], 'getCssPropertyValue', array('border-left-color', )),
				'class' => 'input--cssProp',
				'dir' => 'ltr',
			)) . '
										<div class="inputGroup-text"><span class="colorPickerBox js-colorPickerTrigger"></span></div>
									</div></td>
								';
		}
		$__finalCompiled .= '

								';
		if ($__templater->method($__vars['property'], 'isValidCssComponent', array('border_radius', ))) {
			$__finalCompiled .= '
									<td>' . $__templater->formTextBox(array(
				'name' => $__vars['formBaseKey'] . '[border-bottom-left-radius]',
				'value' => $__templater->method($__vars['property'], 'getCssPropertyValue', array('border-bottom-left-radius', )),
				'class' => 'input--cssProp input--cssLength',
				'dir' => 'ltr',
			)) . '
									</td>
								';
		}
		$__finalCompiled .= '
							</tr>
							</table>
						</li>
					';
	} else if ($__templater->method($__vars['property'], 'isValidCssComponent', array(array('border_color_simple', 'border_width_simple', 'border_radius_simple', ), ))) {
		$__finalCompiled .= '
						<li>
							<h4 class="cssPropertyHeader">' . 'Borda' . '</h4>
							<table class="cssPropertySet">
							';
		if ($__templater->method($__vars['property'], 'isValidCssComponent', array('border_width_simple', ))) {
			$__finalCompiled .= '
								<tr class="cssPropertySet-headerRow">
									<th>' . 'Largura' . '</th>
								</tr>
								<tr>
									<td>' . $__templater->formTextBox(array(
				'name' => $__vars['formBaseKey'] . '[border-width]',
				'value' => $__templater->method($__vars['property'], 'getCssPropertyValue', array('border-width', )),
				'class' => 'input--cssProp input--colorWidthMatched',
				'dir' => 'ltr',
			)) . '</td>
								</tr>
							';
		}
		$__finalCompiled .= '

							';
		if ($__templater->method($__vars['property'], 'isValidCssComponent', array('border_color_simple', ))) {
			$__finalCompiled .= '
								<tr class="cssPropertySet-headerRow">
									<th>' . 'Cor' . '</th>
								</tr>
								<tr>
									<td><div class="inputGroup inputGroup--joined inputGroup--color" data-xf-init="color-picker">
										' . $__templater->formTextBox(array(
				'name' => $__vars['formBaseKey'] . '[border-color]',
				'value' => $__templater->method($__vars['property'], 'getCssPropertyValue', array('border-color', )),
				'class' => 'input--cssProp',
				'dir' => 'ltr',
			)) . '
										<div class="inputGroup-text"><span class="colorPickerBox js-colorPickerTrigger"></span></div>
									</div></td>
								</tr>
							';
		}
		$__finalCompiled .= '

							';
		if ($__templater->method($__vars['property'], 'isValidCssComponent', array('border_radius_simple', ))) {
			$__finalCompiled .= '
								<tr class="cssPropertySet-headerRow">
									<th>' . 'Raio' . '</th>
								</tr>
								<tr>
									<td>' . $__templater->formTextBox(array(
				'name' => $__vars['formBaseKey'] . '[border-radius]',
				'value' => $__templater->method($__vars['property'], 'getCssPropertyValue', array('border-radius', )),
				'class' => 'input--cssProp input--colorWidthMatched',
				'dir' => 'ltr',
			)) . '</td>
								</tr>
							';
		}
		$__finalCompiled .= '
							</table>
						</li>
					';
	}
	$__finalCompiled .= '

					';
	if ($__templater->method($__vars['property'], 'isValidCssComponent', array('padding', ))) {
		$__finalCompiled .= '
						<li>
							<h4 class="cssPropertyHeader">' . 'Padding' . '</h4>
							<table class="cssPropertySet">
							<tr>
								<td class="cssPropertySet-rowLabel">' . 'Todos' . '</td>

								<td>' . $__templater->formTextBox(array(
			'name' => $__vars['formBaseKey'] . '[padding]',
			'value' => $__templater->method($__vars['property'], 'getCssPropertyValue', array('padding', )),
			'class' => 'input--cssProp input--cssLength',
			'dir' => 'ltr',
		)) . '
								</td>
							</tr>
							<tr>
								<td class="cssPropertySet-rowLabel">' . 'Topo' . '</td>

								<td>' . $__templater->formTextBox(array(
			'name' => $__vars['formBaseKey'] . '[padding-top]',
			'value' => $__templater->method($__vars['property'], 'getCssPropertyValue', array('padding-top', )),
			'class' => 'input--cssProp input--cssLength',
			'dir' => 'ltr',
		)) . '
								</td>
							</tr>
							<tr>
								<td class="cssPropertySet-rowLabel">' . 'Direita' . '</td>

								<td>' . $__templater->formTextBox(array(
			'name' => $__vars['formBaseKey'] . '[padding-right]',
			'value' => $__templater->method($__vars['property'], 'getCssPropertyValue', array('padding-right', )),
			'class' => 'input--cssProp input--cssLength',
			'dir' => 'ltr',
		)) . '
								</td>
							</tr>
							<tr>
								<td class="cssPropertySet-rowLabel">' . 'Inferior' . '</td>

								<td>' . $__templater->formTextBox(array(
			'name' => $__vars['formBaseKey'] . '[padding-bottom]',
			'value' => $__templater->method($__vars['property'], 'getCssPropertyValue', array('padding-bottom', )),
			'class' => 'input--cssProp input--cssLength',
			'dir' => 'ltr',
		)) . '
								</td>
							</tr>
							<tr>
								<td class="cssPropertySet-rowLabel">' . 'Esquerda' . '</td>

								<td>' . $__templater->formTextBox(array(
			'name' => $__vars['formBaseKey'] . '[padding-left]',
			'value' => $__templater->method($__vars['property'], 'getCssPropertyValue', array('padding-left', )),
			'class' => 'input--cssProp input--cssLength',
			'dir' => 'ltr',
		)) . '
								</td>
							</tr>
							</table>
						</li>
					';
	}
	$__finalCompiled .= '

					';
	if ($__templater->method($__vars['property'], 'isValidCssComponent', array('extra', ))) {
		$__finalCompiled .= '
						<li class="cssPropertyExtra">
							<h4 class="cssPropertyHeader">' . 'Extra' . '</h4>
							<table class="cssPropertySet">
								<tr class="cssPropertySet-headerRow">
									<th>' . 'Freeform CSS/LESS code' . '</th>
								</tr>
								<tr>
									<td>' . $__templater->formCodeEditor(array(
			'name' => $__vars['formBaseKey'] . '[extra]',
			'value' => $__templater->method($__vars['property'], 'getCssPropertyValue', array('extra', )),
			'mode' => 'less',
			'class' => 'codeEditor--autoSize',
		)) . '</td>
								</tr>
							</table>
						</li>
					';
	}
	$__finalCompiled .= '
				</ul>
			</div>
		</div>
	</div>
';
	return $__finalCompiled;
},
'customization_hint' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'state' => '!',
		'submitName' => '!',
		'property' => '!',
		'checkbox' => false,
	), $__arguments, $__vars);
	$__finalCompiled .= '

	';
	$__compilerTemp1 = '';
	$__compilerTemp1 .= '
			';
	if ($__vars['state'] == 'custom') {
		$__compilerTemp1 .= '

				<span data-xf-init="tooltip" title="' . $__templater->filter('The value for this property has been customized in this style', array(array('for_attr', array()),), true) . '">
					';
		if ($__vars['checkbox']) {
			$__compilerTemp1 .= '
						' . $__templater->callMacro(null, 'revert_code', array(
				'submitName' => $__vars['submitName'],
				'property' => $__vars['property'],
				'label' => 'Revert customized value',
				'container' => 'dl.xf-' . $__vars['property']['property_name'] . ' > dd',
			), $__vars) . '
					';
		} else {
			$__compilerTemp1 .= '
						' . 'Value customized' . '
					';
		}
		$__compilerTemp1 .= '
				</span>

			';
	} else if ($__vars['state'] == 'inherited') {
		$__compilerTemp1 .= '

				<span  data-xf-init="tooltip" title="' . $__templater->filter('The customized value for this property is inherited from a parent style', array(array('for_attr', array()),), true) . '">
					' . 'Value inherited' . '</span>

			';
	} else if ($__vars['state'] == 'added') {
		$__compilerTemp1 .= '

				<span data-xf-init="tooltip" title="' . $__templater->filter('This property was created in this style, and does not exist in parent styles', array(array('for_attr', array()),), true) . '">
					' . 'Custom property' . '</span>

			';
	}
	$__compilerTemp1 .= '
		';
	if (strlen(trim($__compilerTemp1)) > 0) {
		$__finalCompiled .= '
		<span class="formRow-hint--customState cssCustomHighlight cssCustomHighlight--' . $__templater->escape($__vars['state']) . '">
		' . $__compilerTemp1 . '
		</span>
	';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},
'revert_code' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'submitName' => '!',
		'property' => '!',
		'label' => '!',
		'container' => '< dl',
	), $__arguments, $__vars);
	$__finalCompiled .= '

	' . $__templater->formCheckBox(array(
		'standalone' => 'true',
	), array(array(
		'name' => $__vars['submitName'] . '_revert[]',
		'value' => $__vars['property']['property_name'],
		'labelclass' => 'formRow-revert',
		'class' => 'js-disablerExemption',
		'data-xf-init' => 'disabler',
		'data-container' => $__vars['container'],
		'data-invert' => 'true',
		'label' => $__templater->escape($__vars['label']),
		'_type' => 'option',
	))) . '
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '

' . '


' . '





' . '

';
	return $__finalCompiled;
});