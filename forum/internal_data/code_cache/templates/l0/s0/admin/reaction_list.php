<?php
// FROM HASH: 451a9bdaaa43075e7ed44d1b35f04285
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Reactions');
	$__finalCompiled .= '

';
	$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	<div class="buttonGroup">
		' . $__templater->button('Add reaction', array(
		'href' => $__templater->func('link', array('reactions/add', ), false),
		'icon' => 'add',
	), '', array(
	)) . '
		' . $__templater->button('', array(
		'href' => $__templater->func('link', array('reactions/sort', ), false),
		'icon' => 'sort',
		'overlay' => 'true',
	), '', array(
	)) . '
	</div>
');
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	$__compilerTemp2 = true;
	if ($__templater->isTraversable($__vars['reactions'])) {
		foreach ($__vars['reactions'] AS $__vars['reactionId'] => $__vars['reaction']) {
			$__compilerTemp2 = false;
			$__compilerTemp1 .= '
					';
			$__compilerTemp3 = '';
			if ($__vars['reaction']['reaction_type'] == 'positive') {
				$__compilerTemp3 .= '
									(+' . $__templater->escape($__vars['reaction']['reaction_score']) . ')
								';
			} else if ($__vars['reaction']['reaction_type'] == 'negative') {
				$__compilerTemp3 .= '
									(' . $__templater->escape($__vars['reaction']['reaction_score']) . ')
								';
			}
			$__compilerTemp4 = array(array(
				'href' => $__templater->func('link', array('reactions/edit', $__vars['reaction'], ), false),
				'_type' => 'cell',
				'html' => '
							' . $__templater->func('reaction', array(array(
				'id' => $__vars['reaction'],
				'showtitle' => 'true',
			))) . '
						',
			)
,array(
				'href' => $__templater->func('link', array('reactions/edit', $__vars['reaction'], ), false),
				'_type' => 'cell',
				'html' => '
							<span class="reactionScore">
								' . $__templater->escape($__vars['reaction']['score_title']) . '
								' . $__compilerTemp3 . '
							</span>
						',
			));
			if ($__templater->method($__vars['reaction'], 'canToggle', array())) {
				$__compilerTemp4[] = array(
					'name' => 'active[' . $__vars['reaction']['reaction_id'] . ']',
					'selected' => $__vars['reaction']['active'],
					'class' => 'dataList-cell--separated',
					'submit' => 'true',
					'tooltip' => 'Enable / disable \'' . $__vars['reaction']['title'] . '\'',
					'_type' => 'toggle',
					'html' => '',
				);
			} else {
				$__compilerTemp4[] = array(
					'class' => 'dataList-cell--min dataList-cell--separated dataList-cell--alt',
					'_type' => 'cell',
					'html' => '',
				);
			}
			if ($__templater->method($__vars['reaction'], 'canDelete', array())) {
				$__compilerTemp4[] = array(
					'href' => $__templater->func('link', array('reactions/delete', $__vars['reaction'], ), false),
					'_type' => 'delete',
					'html' => '',
				);
			} else {
				$__compilerTemp4[] = array(
					'class' => 'dataList-cell--min dataList-cell--alt',
					'_type' => 'cell',
					'html' => '',
				);
			}
			$__compilerTemp1 .= $__templater->dataRow(array(
			), $__compilerTemp4) . '
				';
		}
	}
	if ($__compilerTemp2) {
		$__compilerTemp1 .= '
					' . $__templater->dataRow(array(
			'rowclass' => 'dataList-row--noHover dataList-row--note',
		), array(array(
			'colspan' => '3',
			'class' => 'dataList-cell--noSearch',
			'_type' => 'cell',
			'html' => '
							' . 'No reactions have been added yet.' . '
						',
		))) . '
				';
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-outer">
		' . $__templater->callMacro('filter_macros', 'quick_filter', array(
		'key' => 'reactions',
		'class' => 'block-outer-opposite',
	), $__vars) . '
	</div>
	<div class="block-container">

		<div class="block-body">
			' . $__templater->dataList('
				' . $__compilerTemp1 . '
			', array(
	)) . '
		</div>
		<div class="block-footer block-footer--split">
			<span class="block-footer-counter">' . $__templater->func('display_totals', array($__templater->method($__vars['reactions'], 'count', array()), ), true) . '</span>
		</div>
	</div>
', array(
		'action' => $__templater->func('link', array('reactions/toggle', ), false),
		'ajax' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});