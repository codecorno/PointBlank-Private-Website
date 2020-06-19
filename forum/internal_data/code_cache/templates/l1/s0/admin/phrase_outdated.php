<?php
// FROM HASH: b30322f6177a29ec08614415b6b98788
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Outdated phrases');
	$__finalCompiled .= '

';
	if ($__vars['total']) {
		$__finalCompiled .= '
	<div class="block">
		<div class="block-outer">
			' . $__templater->callMacro('filter_macros', 'quick_filter', array(
			'key' => 'outdated-phrases',
			'class' => 'block-outer-opposite',
		), $__vars) . '
		</div>
		<div class="block-container">
			<div class="block-body">
				';
		$__compilerTemp1 = '';
		$__compilerTemp2 = $__templater->method($__vars['languageTree'], 'getFlattened', array(0, ));
		if ($__templater->isTraversable($__compilerTemp2)) {
			foreach ($__compilerTemp2 AS $__vars['treeEntry']) {
				$__compilerTemp1 .= '
						';
				$__vars['language'] = $__vars['treeEntry']['record'];
				$__compilerTemp1 .= '
						';
				$__vars['outdatedForLang'] = $__vars['outdatedGrouped'][$__vars['language']['language_id']];
				$__compilerTemp1 .= '
						';
				if (!$__templater->test($__vars['outdatedForLang'], 'empty', array())) {
					$__compilerTemp1 .= '
							' . $__templater->dataRow(array(
						'rowtype' => 'subsection',
						'rowclass' => 'dataList-row--noHover',
					), array(array(
						'_type' => 'cell',
						'html' => $__templater->escape($__vars['language']['title']),
					))) . '
							';
					if ($__templater->isTraversable($__vars['outdatedForLang'])) {
						foreach ($__vars['outdatedForLang'] AS $__vars['outdated']) {
							$__compilerTemp1 .= '
								' . $__templater->dataRow(array(
							), array(array(
								'href' => $__templater->func('link', array('phrases/edit', $__vars['outdated']['phrase'], ), false),
								'class' => 'dataList-cell--main',
								'_type' => 'cell',
								'html' => '
										<div class="dataList-mainRow" dir="auto">' . $__templater->escape($__vars['outdated']['phrase']['title']) . '</div>
										<div class="dataList-subRow">
											<ul class="listInline listInline--bullet">
												<li>' . 'Custom version' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['outdated']['phrase']['version_string']) . '</li>
												<li>' . 'Parent version' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['outdated']['parent_version_string']) . '</li>
											</ul>
										</div>
									',
							))) . '
							';
						}
					}
					$__compilerTemp1 .= '
						';
				}
				$__compilerTemp1 .= '
					';
			}
		}
		$__finalCompiled .= $__templater->dataList('
					' . $__compilerTemp1 . '
				', array(
		)) . '
			</div>
			<div class="block-footer">
				<span class="block-footer-counter">' . $__templater->func('display_totals', array($__vars['total'], ), true) . '</span>
			</div>
		</div>
	</div>
';
	} else {
		$__finalCompiled .= '
	<div class="blockMessage">' . 'There are no outdated phrases.' . '</div>
';
	}
	return $__finalCompiled;
});