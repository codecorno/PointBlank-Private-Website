<?php
// FROM HASH: 3e1422f64b877a8f5061879cab48828b
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('BB code media sites');
	$__finalCompiled .= '

';
	$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('Add BB code media site', array(
		'href' => $__templater->func('link', array('bb-code-media-sites/add', ), false),
		'icon' => 'add',
	), '', array(
	)) . '
');
	$__finalCompiled .= '

';
	if (!$__templater->test($__vars['sites'], 'empty', array())) {
		$__finalCompiled .= '
	';
		$__compilerTemp1 = '';
		if ($__templater->isTraversable($__vars['sites'])) {
			foreach ($__vars['sites'] AS $__vars['site']) {
				$__compilerTemp1 .= '
						';
				$__compilerTemp2 = array(array(
					'hash' => $__vars['site']['media_site_id'],
					'href' => $__templater->func('link', array('bb-code-media-sites/edit', $__vars['site'], ), false),
					'label' => $__templater->escape($__vars['site']['site_title']),
					'hint' => $__templater->escape($__vars['site']['site_url']),
					'_type' => 'main',
					'html' => '',
				)
,array(
					'name' => 'active[' . $__vars['site']['media_site_id'] . ']',
					'selected' => $__vars['site']['active'],
					'class' => 'dataList-cell--separated',
					'submit' => 'true',
					'tooltip' => 'Enable / disable \'' . $__vars['site']['site_title'] . '\'',
					'_type' => 'toggle',
					'html' => '',
				));
				if ($__vars['site']['site_url']) {
					$__compilerTemp2[] = array(
						'href' => $__vars['site']['site_url'],
						'_type' => 'action',
						'html' => 'Visit site',
					);
				} else {
					$__compilerTemp2[] = array(
						'class' => 'dataList-cell--alt',
						'_type' => 'cell',
						'html' => '&nbsp;',
					);
				}
				$__compilerTemp2[] = array(
					'href' => $__templater->func('link', array('bb-code-media-sites/delete', $__vars['site'], ), false),
					'_type' => 'delete',
					'html' => '',
				);
				$__compilerTemp1 .= $__templater->dataRow(array(
				), $__compilerTemp2) . '
					';
			}
		}
		$__finalCompiled .= $__templater->form('
		<div class="block-outer">
			' . $__templater->callMacro('filter_macros', 'quick_filter', array(
			'key' => 'help-pages',
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
			<div class="block-footer">
				<span class="block-footer-counter">' . $__templater->func('display_totals', array($__vars['sites'], ), true) . '</span>
			</div>
		</div>
	', array(
			'action' => $__templater->func('link', array('bb-code-media-sites/toggle', ), false),
			'class' => 'block',
			'ajax' => 'true',
		)) . '
';
	} else {
		$__finalCompiled .= '
	<div class="blockMessage">' . 'No items have been created yet.' . '</div>
';
	}
	return $__finalCompiled;
});