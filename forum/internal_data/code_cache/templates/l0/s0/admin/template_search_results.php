<?php
// FROM HASH: 2937ac55b7b809ce305a222fb6cdfaf4
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Template search results' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['style']['title']));
	$__finalCompiled .= '

<div class="block">
	';
	if (!$__templater->test($__vars['templates'], 'empty', array())) {
		$__finalCompiled .= '
		<div class="block-outer">
			' . $__templater->callMacro('filter_macros', 'quick_filter', array(
			'class' => 'block-outer-opposite',
		), $__vars) . '
		</div>
	';
	}
	$__finalCompiled .= '
	<div class="block-container">
		' . $__templater->callMacro('template_list', 'search_menu', array(
		'style' => $__vars['style'],
		'conditions' => $__vars['conditions'],
	), $__vars) . '
		';
	if (!$__templater->test($__vars['templates'], 'empty', array())) {
		$__finalCompiled .= '
			<div class="block-body">
				' . $__templater->dataList('
					' . $__templater->callMacro('template_list', 'template_list', array(
			'templates' => $__vars['templates'],
			'style' => $__vars['style'],
		), $__vars) . '
				', array(
		)) . '
			</div>
			<div class="block-footer">
				<span class="block-footer-counter">' . $__templater->func('display_totals', array($__vars['templates'], ), true) . '</span>
			</div>
		';
	} else {
		$__finalCompiled .= '
			<div class="block-body block-row">' . 'No results found.' . '</div>
		';
	}
	$__finalCompiled .= '
	</div>
</div>';
	return $__finalCompiled;
});