<?php
// FROM HASH: 67104836648fa42324bb1c50e82dfe8d
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__vars['search']['search_query']) {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Search results for query' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['search']['search_query']));
		$__templater->pageParams['pageNumber'] = $__vars['page'];
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageH1'] = $__templater->preEscaped('Search results for query' . $__vars['xf']['language']['label_separator'] . ' <a href="' . $__templater->func('link', array('search', $__vars['search'], array('searchform' => '1', ), ), true) . '"><em>' . $__templater->escape($__vars['search']['search_query']) . '</em></a>');
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Search results');
		$__templater->pageParams['pageNumber'] = $__vars['page'];
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	$__templater->includeCss('search_results.less');
	$__finalCompiled .= '

';
	$__templater->breadcrumb($__templater->preEscaped('Search'), $__templater->func('link', array('full:search', ), false), array(
	));
	$__finalCompiled .= '

';
	$__templater->setPageParam('head.' . 'robots', $__templater->preEscaped('<meta name="robots" content="noindex" />'));
	$__finalCompiled .= '

';
	if ($__vars['activeModType']) {
		$__finalCompiled .= '
	';
		$__templater->includeJs(array(
			'src' => 'xf/inline_mod.js',
			'min' => '1',
		));
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

<div class="block" data-xf-init="' . ($__vars['activeModType'] ? 'inline-mod' : '') . '" data-type="' . $__templater->escape($__vars['activeModType']) . '" data-href="' . $__templater->func('link', array('inline-mod', ), true) . '">
	';
	if ($__vars['search']['warnings']) {
		$__finalCompiled .= '
		<div class="block-outer">
			<ol class="listPlain blockMessage blockMessage--warning blockMessage--close">
				';
		if ($__templater->isTraversable($__vars['search']['warnings'])) {
			foreach ($__vars['search']['warnings'] AS $__vars['warning']) {
				$__finalCompiled .= '
					<li>' . $__templater->escape($__vars['warning']) . '</li>
				';
			}
		}
		$__finalCompiled .= '
			</ol>
		</div>
	';
	}
	$__finalCompiled .= '
	';
	if ($__vars['modTypes']) {
		$__finalCompiled .= '
		<div class="block-outer">
			<div class="block-outer-opposite">
				<div class="buttonGroup">
					';
		if ($__vars['activeModType']) {
			$__finalCompiled .= '
						' . $__templater->callMacro('inline_mod_macros', 'button', array(), $__vars) . '
					';
		}
		$__finalCompiled .= '
					<div class="buttonGroup-buttonWrapper">
						' . $__templater->button('Enable moderation', array(
			'class' => 'button--link menuTrigger',
			'data-xf-click' => 'menu',
			'aria-expanded' => 'false',
			'aria-haspopup' => 'true',
		), '', array(
		)) . '
						<div class="menu" data-menu="menu" aria-hidden="true">
							<div class="menu-content">
								<h3 class="menu-header">' . 'Enable moderation' . '</h3>
								';
		if ($__templater->isTraversable($__vars['modTypes'])) {
			foreach ($__vars['modTypes'] AS $__vars['type'] => $__vars['text']) {
				$__finalCompiled .= '
									<a href="' . $__templater->func('link', array('search', $__vars['search'], array('mod' => $__vars['type'], 'page' => (($__vars['page'] > 1) ? $__vars['page'] : ''), ), ), true) . '" class="menu-linkRow ' . (($__vars['activeModType'] == $__vars['type']) ? 'is-selected' : '') . '">' . $__templater->escape($__vars['text']) . '</a>
								';
			}
		}
		$__finalCompiled .= '
								';
		if ($__vars['activeModType']) {
			$__finalCompiled .= '
									<hr class="menu-separator" />
									<a href="' . $__templater->func('link', array('search', $__vars['search'], array('page' => (($__vars['page'] > 1) ? $__vars['page'] : ''), ), ), true) . '" class="menu-linkRow">' . 'Disable' . '</a>
								';
		}
		$__finalCompiled .= '
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	';
	}
	$__finalCompiled .= '

	<div class="block-container">
		<ol class="block-body">
			';
	if ($__templater->isTraversable($__vars['results'])) {
		foreach ($__vars['results'] AS $__vars['result']) {
			$__finalCompiled .= '
				' . $__templater->filter($__templater->method($__vars['result'], 'render', array(array('mod' => $__vars['activeModType'], ), )), array(array('raw', array()),), true) . '
			';
		}
	}
	$__finalCompiled .= '
		</ol>
		';
	if ($__vars['getOlderResultsDate']) {
		$__finalCompiled .= '
			<div class="block-footer">
				<span class="block-footer-controls">' . $__templater->button('
					' . 'View older results' . '
				', array(
			'href' => $__templater->func('link', array('search/older', $__vars['search'], array('before' => $__vars['getOlderResultsDate'], ), ), false),
			'class' => 'button--link',
		), '', array(
		)) . '</span>
			</div>
		';
	}
	$__finalCompiled .= '
	</div>

	<div class="block-outer block-outer--after">
		' . $__templater->func('page_nav', array(array(
		'page' => $__vars['page'],
		'total' => $__vars['search']['result_count'],
		'link' => 'search',
		'data' => $__vars['search'],
		'params' => array('mod' => $__vars['activeModType'], ),
		'wrapperclass' => 'block-outer-main',
		'perPage' => $__vars['perPage'],
	))) . '
		' . $__templater->func('show_ignored', array(array(
		'wrapperclass' => 'block-outer-opposite',
	))) . '
	</div>
</div>

';
	if ($__vars['activeModType']) {
		$__finalCompiled .= '
	';
		$__templater->includeJs(array(
			'src' => 'xf/inline_mod.js',
			'min' => '1',
		));
		$__finalCompiled .= '
';
	}
	return $__finalCompiled;
});