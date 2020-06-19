<?php
// FROM HASH: 13e13aa30ab11eeda0704bf62101c6bd
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Sort smilies');
	$__finalCompiled .= '

' . $__templater->callMacro('public:nestable_macros', 'setup', array(), $__vars) . '

';
	$__compilerTemp1 = '';
	if ($__templater->isTraversable($__vars['smilieData']['smilieCategories'])) {
		foreach ($__vars['smilieData']['smilieCategories'] AS $__vars['smilieCategoryId'] => $__vars['smilieCategory']) {
			$__compilerTemp1 .= '
			';
			if (($__vars['smilieCategoryId'] > 0)) {
				$__compilerTemp1 .= '
				<h4 class="block-minorHeader">' . $__templater->escape($__vars['smilieCategory']['title']) . '</h4>
			';
			} else {
				$__compilerTemp1 .= '
				<h4 class="block-minorHeader">' . 'Uncategorized smilies' . '</h4>
			';
			}
			$__compilerTemp1 .= '
			<div class="block-body">
				<div class="nestable-container" data-xf-init="nestable" data-parent-id="' . $__templater->escape($__vars['smilieCategoryId']) . '" data-max-depth="1" data-value-target=".js-smilieData">
					';
			$__compilerTemp2 = '';
			$__compilerTemp2 .= '
							';
			$__vars['i'] = 0;
			if ($__templater->isTraversable($__vars['smilieData']['smilies'][$__vars['smilieCategoryId']])) {
				foreach ($__vars['smilieData']['smilies'][$__vars['smilieCategoryId']] AS $__vars['smilieId'] => $__vars['smilie']) {
					$__vars['i']++;
					$__compilerTemp2 .= '
								<li class="nestable-item" data-id="' . $__templater->escape($__vars['smilieId']) . '">
									<div class="nestable-handle nestable-handle--full" aria-label="' . $__templater->filter('Drag handle', array(array('for_attr', array()),), true) . '">' . $__templater->fontAwesome('fa-bars', array(
					)) . '</div>
									<div class="nestable-content">' . $__templater->func('smilie', array($__vars['smilie']['smilie_text_options']['0'], ), true) . ' ' . $__templater->escape($__vars['smilie']['title']) . ' ';
					if ($__templater->isTraversable($__vars['smilie']['smilie_text_options'])) {
						foreach ($__vars['smilie']['smilie_text_options'] AS $__vars['smilieText']) {
							$__compilerTemp2 .= '<span class="smilieText">' . $__templater->escape($__vars['smilieText']) . '</span> ';
						}
					}
					$__compilerTemp2 .= '</div>
								</li>
							';
				}
			}
			$__compilerTemp2 .= '
							';
			if (strlen(trim($__compilerTemp2)) > 0) {
				$__compilerTemp1 .= '
						<ol class="nestable-list">
							' . $__compilerTemp2 . '
						</ol>
					';
			}
			$__compilerTemp1 .= '
					' . $__templater->formHiddenVal('smilies[]', '', array(
				'class' => 'js-smilieData',
			)) . '
				</div>
			</div>
		';
		}
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		' . $__compilerTemp1 . '
		' . $__templater->formSubmitRow(array(
		'icon' => 'save',
	), array(
		'rowtype' => 'simple',
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('smilies/sort', ), false),
		'class' => 'block',
		'ajax' => 'true',
	));
	return $__finalCompiled;
});