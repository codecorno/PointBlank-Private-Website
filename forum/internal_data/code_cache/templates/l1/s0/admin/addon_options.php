<?php
// FROM HASH: 4181e3ab719faf0f70e43ca9a7ec0e17
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Options' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['addOn']['title']));
	$__finalCompiled .= '

';
	if (!$__templater->test($__vars['groups'], 'empty', array())) {
		$__finalCompiled .= '
	';
		$__compilerTemp1 = '';
		if ($__templater->isTraversable($__vars['groups'])) {
			foreach ($__vars['groups'] AS $__vars['groupId'] => $__vars['group']) {
				$__compilerTemp1 .= '
				';
				if (!$__templater->test($__vars['groupedOptions'][$__vars['groupId']], 'empty', array())) {
					$__compilerTemp1 .= '
					<h3 class="block-formSectionHeader">
						<span class="' . (($__templater->method($__vars['groups'], 'count', array()) > 1) ? 'collapseTrigger collapseTrigger--block' : '') . ' is-active" data-xf-click="' . (($__templater->method($__vars['groups'], 'count', array()) > 1) ? 'toggle' : '') . '" data-target="< :up:next">
							' . $__templater->escape($__vars['group']['title']) . '
						</span>
						<span class="block-desc">
							' . $__templater->escape($__vars['group']['description']) . '
						</span>
					</h3>
					<div class="block-body ' . (($__templater->method($__vars['groups'], 'count', array()) > 1) ? 'block-body--collapsible' : '') . ' is-active">

						';
					$__vars['hundred'] = '0';
					$__compilerTemp1 .= '

						';
					if ($__templater->isTraversable($__vars['groupedOptions'][$__vars['groupId']])) {
						foreach ($__vars['groupedOptions'][$__vars['groupId']] AS $__vars['option']) {
							$__compilerTemp1 .= '

							';
							if ($__vars['group']) {
								$__compilerTemp1 .= '
								';
								$__vars['curHundred'] = $__templater->func('floor', array($__vars['option']['Relations'][$__vars['group']['group_id']]['display_order'] / 100, ), false);
								$__compilerTemp1 .= '
								';
								if (($__vars['curHundred'] > $__vars['hundred'])) {
									$__compilerTemp1 .= '
									';
									$__vars['hundred'] = $__vars['curHundred'];
									$__compilerTemp1 .= '
									<hr class="formRowSep" />
								';
								}
								$__compilerTemp1 .= '
							';
							}
							$__compilerTemp1 .= '

							' . $__templater->callMacro('option_macros', 'option_row', array(
								'group' => $__vars['group'],
								'option' => $__vars['option'],
								'includeAddOnHint' => false,
							), $__vars) . '
						';
						}
					}
					$__compilerTemp1 .= '
					</div>
				';
				}
				$__compilerTemp1 .= '
			';
			}
		}
		$__finalCompiled .= $__templater->form('
		<div class="block-container">
			' . $__compilerTemp1 . '
			' . $__templater->formSubmitRow(array(
			'sticky' => 'true',
			'icon' => 'save',
		), array(
		)) . '
		</div>
	', array(
			'action' => $__templater->func('link', array('options/update', ), false),
			'ajax' => 'true',
			'class' => 'block',
		)) . '
';
	} else {
		$__finalCompiled .= '
	<div class="blockMessage">' . 'No items have been created yet.' . '</div>
';
	}
	return $__finalCompiled;
});