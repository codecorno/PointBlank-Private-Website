<?php
// FROM HASH: 81f6d824852495d367c8dec25069286e
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Thread prompts');
	$__finalCompiled .= '

' . $__templater->includeTemplate('base_prompt_list', $__vars) . '

<div class="block">
	<div class="block-container">
		<h2 class="block-header">' . 'Default thread prompt' . '</h2>
		<div class="block-body">
			' . $__templater->dataList('
				' . $__templater->dataRow(array(
	), array(array(
		'href' => $__templater->func('link', array('phrases/edit-by-name', array(), array('title' => 'thread_prompt.default', ), ), false),
		'class' => 'dataList-cell',
		'_type' => 'cell',
		'html' => 'Thread title',
	))) . '
			', array(
	)) . '
		</div>
	</div>
</div>';
	return $__finalCompiled;
});