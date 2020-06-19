<?php
// FROM HASH: 010d33c67ba07720fe912bf503c8a3c3
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->formRow('

	<ul class="inputChoices inputChoices--noChoice">
		<li class="inputChoices-choice">
			<div>' . 'Disallowed words in tags' . $__vars['xf']['language']['label_separator'] . '</div>
			' . $__templater->formTextArea(array(
		'name' => $__vars['inputName'] . '[disallowedWords]',
		'value' => $__vars['option']['option_value']['disallowedWords'],
		'autosize' => 'true',
	)) . '
			<dfn class="inputChoices-explain">' . 'The words or phrases in this field will not be allowed in any part of tags. Put each word or phrase on its own line. Entering \'tar\' will disallow \'star\' and \'tarnish\' etc.' . '</dfn>
		</li>
		<li class="inputChoices-choice">
			<div>' . 'Tag match regular expression' . $__vars['xf']['language']['label_separator'] . '</div>
			' . $__templater->formTextBox(array(
		'name' => $__vars['inputName'] . '[matchRegex]',
		'value' => $__vars['option']['option_value']['matchRegex'],
	)) . '
			<dfn class="inputChoices-explain">' . 'This requires the new tags to match the given regular expression. You must include delimiters and modifiers (e.g., /example/siU).' . '</dfn>
		</li>
	</ul>
', array(
		'label' => $__templater->escape($__vars['option']['title']),
		'hint' => $__templater->escape($__vars['hintHtml']),
		'explain' => 'These limits only apply when a tag is created. Existing tags may always be used.',
		'html' => $__templater->escape($__vars['listedHtml']),
	));
	return $__finalCompiled;
});