<?php
// FROM HASH: 88582772d2dbaa34f61fc92663ce29cb
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->method($__vars['modification'], 'isInsert', array())) {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Add template modification');
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Edit template modification' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['modification']['template']));
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	$__templater->includeCss('template_modification.less');
	$__finalCompiled .= '

';
	if (!$__templater->method($__vars['modification'], 'canEdit', array())) {
		$__finalCompiled .= '
	<div class="blockMessage blockMessage--important blockMessage--iconic">
		' . 'Only a limited number of fields in this item may be edited.' . '
	</div>
';
	}
	$__finalCompiled .= '

';
	if ($__templater->method($__vars['modification'], 'isUpdate', array()) AND $__templater->method($__vars['modification'], 'canEdit', array())) {
		$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('', array(
			'href' => $__templater->func('link', array('template-modifications/delete', $__vars['modification'], ), false),
			'icon' => 'delete',
			'overlay' => 'true',
		), '', array(
		)) . '
');
	}
	$__finalCompiled .= '

' . $__templater->form('
	<div class="block-container">
		<div class="block-body">

			' . $__templater->formRow('
				' . $__templater->escape($__vars['types'][$__vars['modification']['type']]) . '
				' . $__templater->formHiddenVal('type', $__vars['modification']['type'], array(
	)) . '
			', array(
		'label' => 'Template modification type',
	)) . '

			' . $__templater->formTextBoxRow(array(
		'name' => 'template',
		'value' => $__vars['modification']['template'],
		'id' => 'js-templateModificationTemplate',
		'maxlength' => $__templater->func('max_length', array($__vars['modification'], 'template', ), false),
		'readonly' => (!$__templater->method($__vars['modification'], 'canEdit', array())),
		'ac' => 'single',
		'data-acurl' => $__templater->func('link', array('template-modifications/auto-complete', null, array('type' => $__vars['modification']['type'], ), ), false),
		'dir' => 'ltr',
	), array(
		'label' => 'Template',
	)) . '

			' . $__templater->formTextBoxRow(array(
		'name' => 'modification_key',
		'value' => $__vars['modification']['modification_key'],
		'maxlength' => $__templater->func('max_length', array($__vars['modification'], 'modification_key', ), false),
		'readonly' => (!$__templater->method($__vars['modification'], 'canEdit', array())),
		'dir' => 'ltr',
	), array(
		'label' => 'Modification key',
		'explain' => 'This is used to uniquely identify a modification across add-on upgrades.',
	)) . '

			' . $__templater->formTextBoxRow(array(
		'name' => 'description',
		'value' => $__vars['modification']['description'],
		'readonly' => (!$__templater->method($__vars['modification'], 'canEdit', array())),
	), array(
		'label' => 'Description',
	)) . '

			<hr class="formRowSep" />

			<div class="js-templateContentsContainer u-hidden u-hidden--transition">
				' . $__templater->formRow('

					<pre class="templateModContents"
						id="js-templateContents"
						data-contents-url="' . $__templater->func('link', array('template-modifications/contents', '', array('type' => $__vars['modification']['type'], ), ), true) . '"
					><!--
						--><i>' . 'The requested template could not be found.' . '</i><!--
					--></pre>
				', array(
		'rowtype' => 'input',
		'label' => 'Template contents',
	)) . '
			</div>

			' . $__templater->formRadioRow(array(
		'name' => 'action',
		'value' => $__vars['modification']['action'],
		'readonly' => (!$__templater->method($__vars['modification'], 'canEdit', array())),
	), array(array(
		'value' => 'str_replace',
		'label' => 'Simple replacement',
		'_type' => 'option',
	),
	array(
		'value' => 'preg_replace',
		'label' => 'Regular expression',
		'hint' => 'The search text must contain the delimiters and modifiers to use in the regular expression.',
		'_type' => 'option',
	),
	array(
		'value' => 'callback',
		'label' => 'PHP callback',
		'hint' => 'The search text must be a full regular expression. The replace field must be in the form of className::methodName to call. The callback will receive one argument, an array of matches from the regular expression. It must return the updated template string.',
		'_type' => 'option',
	)), array(
		'label' => 'Search type',
	)) . '

			' . $__templater->formCodeEditorRow(array(
		'name' => 'find',
		'value' => $__vars['modification']['find'],
		'mode' => 'html',
		'readonly' => (!$__templater->method($__vars['modification'], 'canEdit', array())),
		'class' => 'codeEditor--short',
	), array(
		'label' => 'Find',
	)) . '

			' . $__templater->formCodeEditorRow(array(
		'name' => 'replace',
		'value' => $__vars['modification']['replace'],
		'mode' => 'html',
		'readonly' => (!$__templater->method($__vars['modification'], 'canEdit', array())),
		'class' => 'codeEditor--short',
	), array(
		'label' => 'Replace',
		'explain' => 'You may use $0 to insert the found text. For regular expressions, you may use $1 and so on to insert sub-matches. Regular expressions should also escape backslashes in the replacement content (using \\\\). You may use XenForo template syntax.',
	)) . '

			<hr class="formRowSep" />

			' . $__templater->formNumberBoxRow(array(
		'name' => 'execution_order',
		'value' => $__vars['modification']['execution_order'],
		'min' => '0',
		'readonly' => (!$__templater->method($__vars['modification'], 'canEdit', array())),
	), array(
		'label' => 'Execution order',
	)) . '

			' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'enabled',
		'value' => '1',
		'selected' => $__vars['modification']['enabled'],
		'hint' => (($__vars['xf']['development'] AND $__vars['modification']['addon_id']) ? 'The value of this field will not be changed when this add-on is upgraded.' : ''),
		'label' => '
					' . 'Automatically apply template modification' . '
				',
		'_type' => 'option',
	)), array(
	)) . '

			' . $__templater->callMacro('addon_macros', 'addon_edit', array(
		'addOnId' => $__vars['modification']['addon_id'],
	), $__vars) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'sticky' => 'true',
		'icon' => 'save',
	), array(
		'html' => '
			' . $__templater->button('Test', array(
		'type' => 'submit',
		'name' => 'test',
		'value' => '1',
	), '', array(
	)) . '
		',
	)) . '
	</div>

', array(
		'action' => $__templater->func('link', array('template-modifications/save', $__vars['modification'], ), false),
		'ajax' => 'true',
		'class' => 'block',
	)) . '

';
	$__templater->inlineJs('
	$(function() {
		var $templateInput = $(\'#js-templateModificationTemplate\'),
				$contents = $(\'#js-templateContents\'),
				$container = $contents.closest(\'.js-templateContentsContainer\'),
				invalidContents = $contents.html();

		var updateContents = function(templateName)
		{
			if (templateName === \'\')
			{
				$container.removeClassTransitioned(\'is-active\');
				return;
			}

			XF.ajax(
					\'get\',
					$contents.data(\'contents-url\'),
					{ template: templateName },
					function(data)
					{
						if (data.template !== false)
						{
							$contents.text(data.template);
						}
						else
						{
							$contents.html(invalidContents);
						}

						$container.addClassTransitioned(\'is-active\', function()
						{
							XF.layoutChange();
						});
					}
			);
		};

		if ($templateInput.val())
		{
			updateContents($templateInput.val());
		}

		var timer;

		$templateInput.on(\'change AutoComplete\', function() {
			if (timer)
			{
				clearTimeout(timer);
			}
			timer = setTimeout(function() {
				updateContents($templateInput.val());
			}, 200);
		});
	});
');
	return $__finalCompiled;
});