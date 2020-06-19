<?php
// FROM HASH: 28f4ee6f1307f5a39c62d71fb316770d
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Sitemap log');
	$__finalCompiled .= '

';
	$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('Download sitemap', array(
		'href' => $__templater->func('base_url', array('sitemap.php', ), false),
		'icon' => 'export',
	), '', array(
	)) . '
');
	$__finalCompiled .= '

';
	if (!$__templater->test($__vars['entries'], 'empty', array())) {
		$__finalCompiled .= '
	<div class="block">
		<div class="block-container">
			<div class="block-body">
				';
		$__compilerTemp1 = '';
		if ($__templater->isTraversable($__vars['entries'])) {
			foreach ($__vars['entries'] AS $__vars['entry']) {
				$__compilerTemp1 .= '
						';
				$__compilerTemp2 = '';
				if ($__vars['entry']['complete_date']) {
					$__compilerTemp2 .= '
									' . $__templater->func('date_dynamic', array($__vars['entry']['complete_date'], array(
						'data-full-date' => 'true',
					))) . '
								';
				} else {
					$__compilerTemp2 .= '
									' . 'N/A' . '
								';
				}
				$__compilerTemp1 .= $__templater->dataRow(array(
					'rowclass' => ($__vars['entry']['is_active'] ? 'dataList-row--highlighted' : ''),
				), array(array(
					'_type' => 'cell',
					'html' => '
								' . $__templater->func('date_dynamic', array($__vars['entry']['sitemap_id'], array(
					'data-full-date' => 'true',
				))) . '
							',
				),
				array(
					'_type' => 'cell',
					'html' => '
								' . $__compilerTemp2 . '
							',
				),
				array(
					'_type' => 'cell',
					'html' => $__templater->filter($__vars['entry']['file_count'], array(array('number', array()),), true),
				),
				array(
					'_type' => 'cell',
					'html' => $__templater->filter($__vars['entry']['entry_count'], array(array('number', array()),), true),
				),
				array(
					'_type' => 'cell',
					'html' => ($__vars['entry']['is_compressed'] ? 'Yes' : 'No'),
				))) . '
					';
			}
		}
		$__finalCompiled .= $__templater->dataList('
					' . $__templater->dataRow(array(
			'rowtype' => 'header',
		), array(array(
			'_type' => 'cell',
			'html' => 'Start date',
		),
		array(
			'_type' => 'cell',
			'html' => 'Completion date',
		),
		array(
			'class' => 'dataList-cell--min',
			'_type' => 'cell',
			'html' => 'Total files',
		),
		array(
			'class' => 'dataList-cell--min',
			'_type' => 'cell',
			'html' => 'Total URLs',
		),
		array(
			'class' => 'dataList-cell--min',
			'_type' => 'cell',
			'html' => 'Compressed',
		))) . '

					' . $__compilerTemp1 . '
				', array(
			'data-xf-init' => 'responsive-data-list',
		)) . '
			</div>
			<div class="block-footer">
				<span class="block-footer-counter">' . $__templater->func('display_totals', array($__vars['entries'], $__vars['total'], ), true) . '</span>
			</div>
		</div>
	</div>
';
	} else {
		$__finalCompiled .= '
	<div class="blockMessage">' . 'There are no sitemap logs to display.' . '</div>
';
	}
	return $__finalCompiled;
});