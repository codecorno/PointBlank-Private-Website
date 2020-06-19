<?php
// FROM HASH: a6cdf2ecd310a31d7a6d6715cbda82dd
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Insert spoiler');
	$__finalCompiled .= '

<form class="block" id="editor_spoiler_form">
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formTextBoxRow(array(
		'id' => 'editor_spoiler_title',
	), array(
		'label' => 'Enter spoiler title',
		'explain' => 'If you would like the \'Spoiler\' button to show a title that hints at its content, enter your text here. To avoid having a title, leave the text box empty.',
	)) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'submit' => 'Continue',
		'id' => 'editor_spoiler_submit',
	), array(
	)) . '
	</div>
</form>';
	return $__finalCompiled;
});