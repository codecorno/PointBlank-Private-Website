<?php
// FROM HASH: fce33fa98af0ca29e0c7a0874a43b408
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Tags');
	$__finalCompiled .= '

';
	$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('Add tag', array(
		'href' => $__templater->func('link', array('tags/add', ), false),
		'icon' => 'add',
	), '', array(
	)) . '
');
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	if (!$__templater->test($__vars['tags'], 'empty', array())) {
		$__compilerTemp1 .= '
			<div class="block-body">
				';
		$__compilerTemp2 = '';
		if ($__templater->isTraversable($__vars['tags'])) {
			foreach ($__vars['tags'] AS $__vars['tag']) {
				$__compilerTemp2 .= '
						';
				$__compilerTemp3 = '';
				if ($__vars['tag']['last_use_date']) {
					$__compilerTemp3 .= '
									' . $__templater->func('date_dynamic', array($__vars['tag']['last_use_date'], array(
					))) . '
									';
				} else {
					$__compilerTemp3 .= '
									&nbsp;
								';
				}
				$__compilerTemp2 .= $__templater->dataRow(array(
				), array(array(
					'href' => $__templater->func('link', array('tags/edit', $__vars['tag'], ), false),
					'_type' => 'cell',
					'html' => $__templater->escape($__vars['tag']['tag']),
				),
				array(
					'href' => $__templater->func('link', array('tags/edit', $__vars['tag'], ), false),
					'_type' => 'cell',
					'html' => $__templater->filter($__vars['tag']['use_count'], array(array('number', array()),), true),
				),
				array(
					'href' => $__templater->func('link', array('tags/edit', $__vars['tag'], ), false),
					'_type' => 'cell',
					'html' => '
								' . $__compilerTemp3 . '
							',
				),
				array(
					'href' => $__templater->func('link_type', array('public', 'tags', $__vars['tag'], ), false),
					'_type' => 'action',
					'html' => 'View',
				),
				array(
					'href' => $__templater->func('link', array('tags/merge', $__vars['tag'], ), false),
					'overlay' => 'true',
					'_type' => 'action',
					'html' => 'Merge',
				),
				array(
					'href' => $__templater->func('link', array('tags/delete', $__vars['tag'], ), false),
					'_type' => 'delete',
					'html' => '',
				))) . '
					';
			}
		}
		$__compilerTemp1 .= $__templater->dataList('
					' . $__templater->dataRow(array(
			'rowtype' => 'header',
		), array(array(
			'_type' => 'cell',
			'html' => 'Tag',
		),
		array(
			'_type' => 'cell',
			'html' => 'Total',
		),
		array(
			'_type' => 'cell',
			'html' => 'Last used',
		),
		array(
			'class' => 'dataList-cell--min',
			'_type' => 'cell',
			'html' => '&nbsp;',
		),
		array(
			'class' => 'dataList-cell--min',
			'_type' => 'cell',
			'html' => '&nbsp;',
		),
		array(
			'class' => 'dataList-cell--min',
			'_type' => 'cell',
			'html' => '&nbsp;',
		))) . '
					' . $__compilerTemp2 . '
				', array(
			'data-xf-init' => 'responsive-data-list',
		)) . '
			</div>
			<div class="block-footer">
				<span class="block-footer-counter">' . $__templater->func('display_totals', array($__vars['tags'], $__vars['total'], ), true) . '</span>
			</div>
		';
	} else {
		$__compilerTemp1 .= '
			<div class="block-body block-row">' . 'No results found.' . '</div>
		';
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-outer">
		<span class="filterBlock">
			' . $__templater->formTextBox(array(
		'name' => 'containing',
		'value' => $__vars['filters']['containing'],
		'placeholder' => 'Tag contains' . $__vars['xf']['language']['ellipsis'],
		'class' => 'input--inline filterBlock-input',
	)) . '
			<span>
				<span class="filterBlock-label">' . 'Ordered by' . $__vars['xf']['language']['label_separator'] . '</span>
				' . $__templater->formSelect(array(
		'name' => 'order',
		'value' => ($__vars['filters']['order'] ? $__vars['filters']['order'] : 'tag'),
		'class' => 'input--inline filterBlock-input',
	), array(array(
		'value' => 'tag',
		'label' => 'Tag',
		'_type' => 'option',
	),
	array(
		'value' => 'use_count',
		'label' => 'Total',
		'_type' => 'option',
	),
	array(
		'value' => 'last_use_date',
		'label' => 'Last used',
		'_type' => 'option',
	))) . '
			</span>
			' . $__templater->button('Go', array(
		'type' => 'submit',
	), '', array(
	)) . '
		</span>
	</div>
	<div class="block-container">
		' . $__compilerTemp1 . '
	</div>

	' . $__templater->func('page_nav', array(array(
		'page' => $__vars['page'],
		'total' => $__vars['total'],
		'link' => 'tags',
		'params' => $__vars['filters'],
		'wrapperclass' => 'block-outer block-outer--after',
		'perPage' => $__vars['perPage'],
	))) . '
', array(
		'action' => $__templater->func('link', array('tags', ), false),
		'class' => 'block',
	));
	return $__finalCompiled;
});