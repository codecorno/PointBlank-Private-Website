<?php
// FROM HASH: 3a6d89e5668cefc4a068a1df1a43fa3e
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Smilies');
	$__finalCompiled .= '

';
	$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	<div class="buttonGroup">
		' . $__templater->button('Add smilie', array(
		'href' => $__templater->func('link', array('smilies/add', ), false),
		'icon' => 'add',
	), '', array(
	)) . '
		' . $__templater->button('Add category', array(
		'href' => $__templater->func('link', array('smilie-categories/add', ), false),
		'icon' => 'add',
		'overlay' => 'true',
	), '', array(
	)) . '

		<div class="buttonGroup-buttonWrapper">
			' . $__templater->button('&#8226;&#8226;&#8226;', array(
		'class' => 'menuTrigger',
		'data-xf-click' => 'menu',
		'aria-expanded' => 'false',
		'aria-haspopup' => 'true',
		'title' => 'More options',
	), '', array(
	)) . '
			<div class="menu" data-menu="menu" aria-hidden="true">
				<div class="menu-content">
					<h4 class="menu-header">' . 'More options' . '</h4>
					<a href="' . $__templater->func('link', array('smilies/sort', ), true) . '" class="menu-linkRow" data-xf-click="overlay">' . 'Sort' . '</a>
					<a href="' . $__templater->func('link', array('smilies/import', ), true) . '" class="menu-linkRow">' . 'Import' . '</a>
					<a href="' . $__templater->func('link', array('smilies', array(), array('export' => 1, ), ), true) . '" class="menu-linkRow">' . 'Export' . '</a>
				</div>
			</div>
		</div>
	</div>
');
	$__finalCompiled .= '

';
	$__templater->includeCss('public:help_page_smilies.less');
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	if ($__templater->isTraversable($__vars['smilieData']['smilieCategories'])) {
		foreach ($__vars['smilieData']['smilieCategories'] AS $__vars['smilieCategoryId'] => $__vars['smilieCategory']) {
			$__compilerTemp1 .= '
					<tbody class="dataList-rowGroup">
					';
			if (($__templater->func('count', array($__vars['smilieData']['smilieCategories'], ), false) > 1)) {
				$__compilerTemp1 .= '
						';
				$__compilerTemp2 = array();
				if ($__vars['exportView']) {
					$__compilerTemp2[] = array(
						'_type' => 'cell',
						'html' => $__templater->formCheckBox(array(
						'standalone' => 'true',
					), array(array(
						'check-all' => '< .dataList-rowGroup',
						'_type' => 'option',
					))),
					);
				}
				if (($__vars['smilieCategoryId'] > 0)) {
					$__compilerTemp2[] = array(
						'href' => $__templater->func('link', array('smilie-categories/edit', $__vars['smilieCategory'], ), false),
						'colspan' => '3',
						'overlay' => 'true',
						'_type' => 'cell',
						'html' => '
									' . $__templater->escape($__vars['smilieCategory']['title']) . '
								',
					);
					if (!$__vars['exportView']) {
						$__compilerTemp2[] = array(
							'href' => $__templater->func('link', array('smilie-categories/delete', $__vars['smilieCategory'], ), false),
							'_type' => 'delete',
							'html' => '',
						);
					}
				} else {
					$__compilerTemp2[] = array(
						'colspan' => '4',
						'_type' => 'cell',
						'html' => 'Uncategorized smilies',
					);
				}
				$__compilerTemp1 .= $__templater->dataRow(array(
					'rowtype' => 'subsection',
					'rowclass' => ((!$__vars['smilieCategoryId']) ? 'dataList-row--noHover' : ''),
				), $__compilerTemp2) . '
					';
			}
			$__compilerTemp1 .= '

					';
			$__compilerTemp3 = true;
			if ($__templater->isTraversable($__vars['smilieData']['smilies'][$__vars['smilieCategoryId']])) {
				foreach ($__vars['smilieData']['smilies'][$__vars['smilieCategoryId']] AS $__vars['smilieId'] => $__vars['smilie']) {
					$__compilerTemp3 = false;
					$__compilerTemp1 .= '
						';
					$__compilerTemp4 = array();
					if ($__vars['exportView']) {
						$__compilerTemp4[] = array(
							'name' => 'export[]',
							'value' => $__vars['smilie']['smilie_id'],
							'_type' => 'toggle',
							'html' => '',
						);
					}
					$__compilerTemp4[] = array(
						'href' => $__templater->func('link', array('smilies/edit', $__vars['smilie'], ), false),
						'class' => 'dataList-cell--min',
						'_type' => 'cell',
						'html' => '
								' . $__templater->func('smilie', array($__vars['smilie']['smilie_text_options']['0'], ), true) . '
							',
					);
					$__compilerTemp4[] = array(
						'href' => $__templater->func('link', array('smilies/edit', $__vars['smilie'], ), false),
						'_type' => 'cell',
						'html' => '
								' . $__templater->escape($__vars['smilie']['title']) . '
							',
					);
					$__compilerTemp5 = '';
					if ($__templater->isTraversable($__vars['smilie']['smilie_text_options'])) {
						foreach ($__vars['smilie']['smilie_text_options'] AS $__vars['smilieText']) {
							$__compilerTemp5 .= '
									<span class="smilieText">' . $__templater->escape($__vars['smilieText']) . '</span>
								';
						}
					}
					$__compilerTemp4[] = array(
						'href' => $__templater->func('link', array('smilies/edit', $__vars['smilie'], ), false),
						'_type' => 'cell',
						'html' => '
								' . $__compilerTemp5 . '
							',
					);
					if (!$__vars['exportView']) {
						$__compilerTemp4[] = array(
							'href' => $__templater->func('link', array('smilies/delete', $__vars['smilie'], ), false),
							'_type' => 'delete',
							'html' => '',
						);
					}
					$__compilerTemp1 .= $__templater->dataRow(array(
					), $__compilerTemp4) . '
					';
				}
			}
			if ($__compilerTemp3) {
				$__compilerTemp1 .= '
						';
				$__compilerTemp6 = '';
				if (($__templater->func('count', array($__vars['smilieData']['smilieCategories'], ), false) > 1)) {
					$__compilerTemp6 .= '
									' . 'No smilies have been added to this category yet.' . '
								';
				} else {
					$__compilerTemp6 .= '
									' . 'No smilies have been added yet.' . '
								';
				}
				$__compilerTemp1 .= $__templater->dataRow(array(
					'rowclass' => 'dataList-row--noHover dataList-row--note',
				), array(array(
					'colspan' => '4',
					'class' => 'dataList-cell--noSearch',
					'_type' => 'cell',
					'html' => '
								' . $__compilerTemp6 . '
							',
				))) . '
					';
			}
			$__compilerTemp1 .= '
					</tbody>
				';
		}
	}
	$__compilerTemp7 = '';
	if ($__vars['exportView']) {
		$__compilerTemp7 .= '
				<span class="block-footer-select">' . $__templater->formCheckBox(array(
			'standalone' => 'true',
		), array(array(
			'check-all' => '.dataList',
			'label' => 'Select all',
			'_type' => 'option',
		))) . '</span>
				<span class="block-footer-controls">' . $__templater->button('', array(
			'type' => 'submit',
			'icon' => 'export',
		), '', array(
		)) . '</span>
			';
	}
	$__finalCompiled .= $__templater->form('

	<div class="block-outer">
		' . $__templater->callMacro('filter_macros', 'quick_filter', array(
		'key' => 'smilies',
		'class' => 'block-outer-opposite',
	), $__vars) . '
	</div>
	<div class="block-container">

		<h2 class="block-tabHeader tabs" role="tablist">
			' . '
			<a class="tabs-tab ' . ($__vars['exportView'] ? '' : 'is-active') . '" role="tab" tabindex="0" aria-controls="smilie-list" href="' . $__templater->func('link', array('smilies', ), true) . '">' . 'Smilies' . '</a>
			<a class="tabs-tab ' . ($__vars['exportView'] ? 'is-active' : '') . '" role="tab" tabindex="0" aria-controls="export-mode" href="' . $__templater->func('link', array('smilies', array(), array('export' => 1, ), ), true) . '">' . 'Export' . '</a>
			' . '
		</h2>

		<div class="block-body">
			' . $__templater->dataList('
				' . $__compilerTemp1 . '
			', array(
	)) . '
		</div>
		<div class="block-footer block-footer--split">
			<span class="block-footer-counter">' . $__templater->func('display_totals', array($__vars['smilieData']['totalSmilies'], ), true) . '</span>
			' . $__compilerTemp7 . '
		</div>
	</div>
', array(
		'action' => $__templater->func('link', array(($__vars['exportView'] ? 'smilies/export' : 'smilies/toggle'), ), false),
		'ajax' => ($__vars['exportView'] ? false : true),
		'class' => 'block',
		'data-xf-init' => 'select-plus',
		'data-sp-checkbox' => '.dataList-cell--toggle input:checkbox',
		'data-sp-container' => '.dataList-row',
		'data-sp-control' => '.dataList-cell--link a[href]',
		'data-sp-debug' => true,
	));
	return $__finalCompiled;
});