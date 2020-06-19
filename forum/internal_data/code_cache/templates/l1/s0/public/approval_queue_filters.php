<?php
// FROM HASH: 94210a77e5dad6788fd02df1ba1575ba
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__compilerTemp1 = array(array(
		'value' => '',
		'label' => $__vars['xf']['language']['parenthesis_open'] . 'Any' . $__vars['xf']['language']['parenthesis_close'],
		'_type' => 'option',
	));
	if ($__templater->isTraversable($__vars['contentTypes'])) {
		foreach ($__vars['contentTypes'] AS $__vars['contentType']) {
			$__compilerTemp1[] = array(
				'value' => $__vars['contentType'],
				'label' => $__templater->escape($__templater->method($__vars['xf']['app'], 'getContentTypePhrase', array($__vars['contentType'], ))),
				'_type' => 'option',
			);
		}
	}
	$__finalCompiled .= $__templater->form('
	' . '
	<div class="menu-row menu-row--separated">
		<label for="ctrl_content_type">' . 'Content type' . $__vars['xf']['language']['label_separator'] . '</label>
		<div class="u-inputSpacer">
			' . $__templater->formSelect(array(
		'name' => 'content_type',
		'value' => $__vars['filters']['content_type'],
		'id' => 'ctrl_content_type',
	), $__compilerTemp1) . '
		</div>
	</div>
	
	' . '
	<div class="menu-row menu-row--separated">
		' . 'Sort direction' . $__vars['xf']['language']['label_separator'] . '
		<div class="inputGroup u-inputSpacer">
			' . $__templater->formSelect(array(
		'name' => 'direction',
		'value' => ($__vars['filters']['direction'] ?: 'asc'),
		'aria-labelledby' => 'ctrl_sort_direction',
	), array(array(
		'value' => 'asc',
		'label' => 'Ascending',
		'_type' => 'option',
	),
	array(
		'value' => 'desc',
		'label' => 'Descending',
		'_type' => 'option',
	))) . '
		</div>
	</div>
	' . $__templater->formHiddenVal('order', ($__vars['filters']['order'] ?: 'content_date'), array(
	)) . '

	<div class="menu-footer">
		<span class="menu-footer-controls">
			' . $__templater->button('Filter', array(
		'type' => 'submit',
		'class' => 'button--primary',
	), '', array(
	)) . '
		</span>
	</div>
	' . $__templater->formHiddenVal('apply', '1', array(
	)) . '
', array(
		'action' => $__templater->func('link', array('approval-queue/filters', ), false),
	));
	return $__finalCompiled;
});