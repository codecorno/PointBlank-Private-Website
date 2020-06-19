<?php
// FROM HASH: 663da437549b6c0ff904332e64c81317
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Quick set prompts');
	$__finalCompiled .= '

';
	$__compilerTemp1 = array(array(
		'value' => '-1',
		'label' => $__vars['xf']['language']['parenthesis_open'] . 'None' . $__vars['xf']['language']['parenthesis_close'],
		'_type' => 'option',
	));
	if ($__templater->isTraversable($__vars['promptGroups'])) {
		foreach ($__vars['promptGroups'] AS $__vars['groupId'] => $__vars['promptGroup']) {
			if (($__templater->func('count', array($__vars['promptsGrouped'][$__vars['groupId']], ), false) > 0)) {
				$__compilerTemp1[] = array(
					'label' => ($__vars['groupId'] ? $__vars['promptGroup']['title'] : 'Ungrouped'),
					'_type' => 'optgroup',
					'options' => array(),
				);
				end($__compilerTemp1); $__compilerTemp2 = key($__compilerTemp1);
				if ($__templater->isTraversable($__vars['promptsGrouped'][$__vars['groupId']])) {
					foreach ($__vars['promptsGrouped'][$__vars['groupId']] AS $__vars['promptId'] => $__vars['prompt']) {
						$__compilerTemp1[$__compilerTemp2]['options'][] = array(
							'value' => $__vars['promptId'],
							'label' => $__templater->escape($__vars['prompt']['title']),
							'_type' => 'option',
						);
					}
				}
			}
		}
	}
	$__compilerTemp3 = '';
	if ($__templater->isTraversable($__vars['promptIds'])) {
		foreach ($__vars['promptIds'] AS $__vars['promptId']) {
			$__compilerTemp3 .= '
		' . $__templater->formHiddenVal('prompt_ids[]', $__vars['promptId'], array(
			)) . '
	';
		}
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formSelectRow(array(
		'name' => 'prompt_id',
	), $__compilerTemp1, array(
		'label' => 'Copy settings from',
		'explain' => 'On the next page, you may apply settings to all the selected prompts. If you would like to base these settings on an existing prompt, select it here.',
	)) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'submit' => 'Proceed' . $__vars['xf']['language']['ellipsis'],
		'name' => 'quickset',
		'value' => '1',
	), array(
	)) . '
	</div>
	' . $__compilerTemp3 . '
', array(
		'action' => $__templater->func('link', array($__vars['linkPrefix'] . '/quick-set', ), false),
		'class' => 'block',
	));
	return $__finalCompiled;
});