<?php
// FROM HASH: cdeb753af1c1bbf8dc49945637e19bd7
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Import complete!');
	$__finalCompiled .= '

' . $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formInfoRow('
				' . 'Your import is now complete and the necessary caches have been rebuilt.' . '
			', array(
	)) . '
			' . $__templater->callMacro('import_finalize', 'notes', array(
		'notes' => $__vars['notes'],
	), $__vars) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'submit' => 'Complete import',
		'sticky' => 'true',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('import/complete', ), false),
		'class' => 'block',
	));
	return $__finalCompiled;
});