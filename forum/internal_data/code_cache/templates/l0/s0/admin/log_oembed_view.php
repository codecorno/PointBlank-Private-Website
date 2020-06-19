<?php
// FROM HASH: 9b07f529d62092df409d41f0fac5a805
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('oEmbed media details');
	$__finalCompiled .= '

';
	$__templater->includeJs(array(
		'src' => 'xf/embed.js',
		'min' => '1',
	));
	$__finalCompiled .= '
';
	$__templater->setPageParam('jsState.' . $__vars['providerJs'], true);
	$__finalCompiled .= '

<div class="block">
	<div class="block-container">
		<div class="block-body">
			';
	if ($__vars['oembed']['title']) {
		$__finalCompiled .= '
				' . $__templater->formRow('
					' . $__templater->escape($__vars['oembed']['title']) . '
				', array(
			'label' => 'Title',
			'explain' => $__templater->escape($__vars['oembed']['BbCodeMediaSite']['site_title']) . ': ' . $__templater->escape($__vars['oembed']['media_id']),
		)) . '
			';
	} else {
		$__finalCompiled .= '
				' . $__templater->formRow($__templater->escape($__vars['oembed']['BbCodeMediaSite']['site_title']) . ': ' . $__templater->escape($__vars['oembed']['media_id']), array(
			'label' => 'Title',
		)) . '
			';
	}
	$__finalCompiled .= '
			' . $__templater->formRow('
				<div>' . $__templater->filter($__vars['html'], array(array('raw', array()),), true) . '</div>
				<a href="' . $__templater->escape($__vars['oembed']['url']) . '" target="_blank">' . $__templater->escape($__vars['oembed']['url']) . '</a>
			', array(
		'label' => 'Media',
	)) . '
			' . $__templater->formRow($__templater->filter($__vars['oembed']['views'], array(array('number', array()),), true), array(
		'label' => 'Hits',
	)) . '
			' . $__templater->formRow($__templater->func('date_dynamic', array($__vars['oembed']['first_request_date'], array(
	))), array(
		'label' => 'First requested',
	)) . '
			' . $__templater->formRow($__templater->func('date_dynamic', array($__vars['oembed']['last_request_date'], array(
	))), array(
		'label' => 'Last requested',
	)) . '

			';
	if ($__vars['xf']['options']['oEmbedRequestReferrer']['enabled'] AND !$__templater->test($__vars['oembed']['Referrers'], 'empty', array())) {
		$__finalCompiled .= '
				';
		$__compilerTemp1 = '';
		if ($__templater->isTraversable($__vars['oembed']['Referrers'])) {
			foreach ($__vars['oembed']['Referrers'] AS $__vars['referrer']) {
				$__compilerTemp1 .= '
						' . $__templater->dataRow(array(
					'rowclass' => 'dataList-row--noHover',
				), array(array(
					'dir' => 'auto',
					'_type' => 'cell',
					'html' => '<a href="' . $__templater->escape($__vars['referrer']['referrer_url']) . '" target="_blank">' . $__templater->escape($__vars['referrer']['referrer_url']) . '</a>',
				),
				array(
					'_type' => 'cell',
					'html' => $__templater->filter($__vars['referrer']['hits'], array(array('number', array()),), true),
				),
				array(
					'_type' => 'cell',
					'html' => $__templater->func('date_dynamic', array($__vars['referrer']['first_date'], array(
				))),
				),
				array(
					'_type' => 'cell',
					'html' => $__templater->func('date_dynamic', array($__vars['referrer']['last_date'], array(
				))),
				))) . '
					';
			}
		}
		$__finalCompiled .= $__templater->dataList('
					' . $__templater->dataRow(array(
			'rowtype' => 'header',
		), array(array(
			'_type' => 'cell',
			'html' => 'Referrer',
		),
		array(
			'_type' => 'cell',
			'html' => 'Hits',
		),
		array(
			'_type' => 'cell',
			'html' => 'First seen',
		),
		array(
			'_type' => 'cell',
			'html' => 'Last seen',
		))) . '
					' . $__compilerTemp1 . '
				', array(
			'class' => 'dataList--separatedTop',
			'data-xf-init' => 'responsive-data-list',
		)) . '
			';
	}
	$__finalCompiled .= '
		</div>
	</div>
</div>';
	return $__finalCompiled;
});