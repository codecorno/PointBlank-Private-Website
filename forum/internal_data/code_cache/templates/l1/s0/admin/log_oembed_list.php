<?php
// FROM HASH: 7d4f98362ddf77fe2c5dcc064dab47c3
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('oEmbed log');
	$__finalCompiled .= '

';
	$__compilerTemp1 = array(array(
		'value' => '',
		'_type' => 'option',
	));
	if ($__templater->isTraversable($__vars['mediaSites'])) {
		foreach ($__vars['mediaSites'] AS $__vars['mediaSite']) {
			$__compilerTemp1[] = array(
				'value' => $__vars['mediaSite']['media_site_id'],
				'label' => $__templater->escape($__vars['mediaSite']['site_title']),
				'_type' => 'option',
			);
		}
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<div class="block-body block-row">
			<span>
				<label>' . 'Media provider is' . $__vars['xf']['language']['label_separator'] . '
				' . $__templater->formSelect(array(
		'name' => 'mediaSiteId',
		'value' => $__vars['filters']['mediaSiteId'] . '}',
		'class' => 'input--inline',
	), $__compilerTemp1) . '</label>
			</span>
			<span>
				<label>' . 'Ordered by' . $__vars['xf']['language']['label_separator'] . '
				' . $__templater->formSelect(array(
		'name' => 'order',
		'value' => ($__vars['filters']['order'] ? $__vars['filters']['order'] : 'last_request_date'),
		'class' => 'input--inline',
	), array(array(
		'value' => 'last_request_date',
		'label' => 'Last requested',
		'_type' => 'option',
	),
	array(
		'value' => 'first_request_date',
		'label' => 'First requested',
		'_type' => 'option',
	),
	array(
		'value' => 'views',
		'label' => 'Hits',
		'_type' => 'option',
	))) . '</label>
			</span>

			' . $__templater->button('Go', array(
		'type' => 'submit',
	), '', array(
	)) . '
		</div>
	</div>
', array(
		'action' => $__templater->func('link', array('logs/oembed', ), false),
		'class' => 'block',
	)) . '

';
	if (!$__templater->test($__vars['entries'], 'empty', array())) {
		$__finalCompiled .= '
	<div class="block">
		<div class="block-container">
			<div class="block-body">
				';
		$__compilerTemp2 = '';
		if ($__templater->isTraversable($__vars['entries'])) {
			foreach ($__vars['entries'] AS $__vars['entry']) {
				$__compilerTemp2 .= '
						' . $__templater->dataRow(array(
				), array(array(
					'href' => $__templater->func('link', array('logs/oembed', $__vars['entry'], ), false),
					'overlay' => 'true',
					'overlaycache' => 'false',
					'_type' => 'cell',
					'html' => '
								<div class="dataList-textRow">
									<b>' . $__templater->escape($__vars['entry']['BbCodeMediaSite']['site_title']) . '</b>:
									<span dir="auto">' . ($__vars['entry']['title'] ? $__templater->escape($__vars['entry']['title']) : $__templater->escape($__vars['entry']['media_id'])) . '</span>
								</div>
								<div class="dataList-subRow">
									<ul class="listInline listInline--bullet">
										<li>' . 'First requested' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->func('date_dynamic', array($__vars['entry']['first_request_date'], array(
				))) . '</li>
										<li>' . 'Last requested' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->func('date_dynamic', array($__vars['entry']['last_request_date'], array(
				))) . '</li>
									</ul>
								</div>
							',
				),
				array(
					'href' => $__templater->func('link', array('logs/oembed-item', $__vars['entry'], ), false),
					'class' => 'dataList-cell--min',
					'overlay' => 'true',
					'_type' => 'cell',
					'html' => '
								' . $__templater->filter($__vars['entry']['views'], array(array('number', array()),), true) . '
							',
				))) . '
					';
			}
		}
		$__finalCompiled .= $__templater->dataList('
					' . $__templater->dataRow(array(
			'rowtype' => 'header',
		), array(array(
			'_type' => 'cell',
			'html' => 'Media',
		),
		array(
			'_type' => 'cell',
			'html' => 'Hits',
		))) . '
					' . $__compilerTemp2 . '
				', array(
			'data-xf-init' => 'responsive-data-list',
		)) . '
				<div class="block-footer">
					<span class="block-footer-counter">' . $__templater->func('display_totals', array($__vars['entries'], $__vars['total'], ), true) . '</span>
				</div>
			</div>
		</div>

		' . $__templater->func('page_nav', array(array(
			'page' => $__vars['page'],
			'total' => $__vars['total'],
			'link' => 'logs/oembed',
			'params' => $__vars['filters'],
			'wrapperclass' => 'block-outer block-outer--after',
			'perPage' => $__vars['perPage'],
		))) . '
	</div>
';
	} else {
		$__finalCompiled .= '
	<div class="blockMessage">' . 'No entries have been logged.' . '</div>
';
	}
	return $__finalCompiled;
});