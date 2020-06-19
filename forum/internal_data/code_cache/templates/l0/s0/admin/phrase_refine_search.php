<?php
// FROM HASH: 5af23592c4797dad1b754fc133389ca5
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__compilerTemp1 = '';
	if ($__vars['translateOnly']) {
		$__compilerTemp1 .= '
				' . $__templater->button('', array(
			'type' => 'submit',
			'icon' => 'translate',
			'name' => 'translate',
			'class' => 'button--primary',
		), '', array(
		)) . '
			';
	} else {
		$__compilerTemp1 .= '
				' . $__templater->button('Refine search', array(
			'type' => 'submit',
			'icon' => 'search',
			'class' => 'button--primary',
		), '', array(
		)) . '
				' . $__templater->button('', array(
			'type' => 'submit',
			'icon' => 'translate',
			'name' => 'translate',
		), '', array(
		)) . '
			';
	}
	$__finalCompiled .= $__templater->form('
	<div class="menu-row">
		' . 'Language' . $__vars['xf']['language']['label_separator'] . '
		<div class="u-inputSpacer">
			' . $__templater->callMacro('language_macros', 'language_select', array(
		'languageTree' => $__vars['languageTree'],
		'languageId' => $__vars['language']['language_id'],
		'row' => false,
	), $__vars) . '
		</div>
	</div>

	<div class="menu-row menu-row--separated">
		' . 'Add-on' . $__vars['xf']['language']['label_separator'] . '
		<div class="u-inputSpacer">
			' . $__templater->callMacro('addon_macros', 'addon_select', array(
		'addOnId' => $__vars['conditions']['addon_id'],
		'includeAny' => true,
		'row' => false,
	), $__vars) . '
		</div>
	</div>

	<div class="menu-row">
		' . 'Title contains' . $__vars['xf']['language']['label_separator'] . '
		<div class="u-inputSpacer">
			' . $__templater->formTextBox(array(
		'name' => 'title',
		'type' => 'search',
		'value' => $__vars['conditions']['title'],
		'dir' => 'ltr',
	)) . '
		</div>
	</div>

	<div class="menu-row menu-row--separated">
		' . 'Text contains' . $__vars['xf']['language']['label_separator'] . '
		<div class="u-inputSpacer">
			<ul class="inputList">
				<li>
					' . $__templater->formTextArea(array(
		'name' => 'text',
		'value' => $__vars['conditions']['text'],
		'autosize' => 'true',
	)) . '
				</li>
				<li>' . $__templater->formCheckBox(array(
		'standalone' => 'true',
	), array(array(
		'name' => 'text_cs',
		'label' => 'Case sensitive',
		'selected' => $__vars['conditions']['text_cs'],
		'_type' => 'option',
	))) . '</li>
			</ul>
		</div>
	</div>

	<div class="menu-row menu-row--separated">
		' . 'Phrase status' . $__vars['xf']['language']['label_separator'] . '
		<div class="u-inputSpacer">
			' . $__templater->formCheckBox(array(
		'name' => 'state[]',
		'value' => $__vars['conditions']['state'],
	), array(array(
		'value' => 'default',
		'label' => 'Unmodified',
		'_type' => 'option',
	),
	array(
		'value' => 'inherited',
		'label' => 'Modified in a parent language',
		'_type' => 'option',
	),
	array(
		'value' => 'custom',
		'label' => 'Modified in this language',
		'_type' => 'option',
	))) . '
		</div>
	</div>

	<div class="menu-footer">
		<span class="menu-footer-controls">
			' . $__compilerTemp1 . '
		</span>
	</div>
	' . $__templater->formHiddenVal('search', '1', array(
	)) . '
', array(
		'action' => $__templater->func('link', array('phrases/search', ), false),
	));
	return $__finalCompiled;
});