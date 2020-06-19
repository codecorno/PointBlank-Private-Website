<?php
// FROM HASH: a5157f5396b962cd603f535fb77cc239
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Install/upgrade from archive');
	$__finalCompiled .= '

' . $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formUploadRow(array(
		'name' => 'uploads[]',
		'multiple' => 'multiple',
		'required' => 'required',
		'accept' => '.zip',
	), array(
		'label' => 'Add-on archive(s)',
	)) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'icon' => 'upload',
		'submit' => 'Upload',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('add-ons/install-from-archive', ), false),
		'upload' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});