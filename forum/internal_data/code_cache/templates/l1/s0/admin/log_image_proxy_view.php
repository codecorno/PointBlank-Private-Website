<?php
// FROM HASH: ef1b3a2680429ff4f50a6abc4620c2c3
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Image proxy details');
	$__finalCompiled .= '

<div class="block">
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formRow('
				<div><img src="' . $__templater->func('link', array('logs/image-proxy/image', $__vars['image'], ), true) . '" alt="" style="max-width: 100%; max-height: 300px; display: block; margin: auto" /></div>
				<a href="' . $__templater->escape($__vars['image']['url']) . '" target="_blank">' . $__templater->escape($__vars['image']['url']) . '</a>
			', array(
		'label' => 'Image',
	)) . '
			' . $__templater->formRow($__templater->filter($__vars['image']['views'], array(array('number', array()),), true), array(
		'label' => 'Hits',
	)) . '
			' . $__templater->formRow($__templater->func('date_dynamic', array($__vars['image']['first_request_date'], array(
	))), array(
		'label' => 'First requested',
	)) . '
			' . $__templater->formRow($__templater->func('date_dynamic', array($__vars['image']['last_request_date'], array(
	))), array(
		'label' => 'Last requested',
	)) . '

			';
	if ($__vars['xf']['options']['imageLinkProxyReferrer']['enabled'] AND !$__templater->test($__vars['image']['Referrers'], 'empty', array())) {
		$__finalCompiled .= '
				';
		$__compilerTemp1 = '';
		if ($__templater->isTraversable($__vars['image']['Referrers'])) {
			foreach ($__vars['image']['Referrers'] AS $__vars['referrer']) {
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