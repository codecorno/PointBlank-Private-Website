<?php
// FROM HASH: ca05ae84e4bfefced9aecf67488d2552
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Merge tags');
	$__finalCompiled .= '

' . $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formRow('
				' . $__templater->escape($__vars['tag']['tag']) . '
			', array(
		'explain' => 'This tag will be deleted.',
		'label' => 'Source tag',
	)) . '

			' . $__templater->formTextBoxRow(array(
		'name' => 'target',
		'ac' => 'single',
		'data-acurl' => $__templater->func('link_type', array('public', 'misc/tag-auto-complete', ), false),
	), array(
		'label' => 'Target tag',
		'explain' => 'All content tagged with ' . $__templater->escape($__vars['tag']['tag']) . ' will now be tagged with this tag.',
	)) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'submit' => 'Merge',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('tags/merge', $__vars['tag'], ), false),
		'ajax' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});