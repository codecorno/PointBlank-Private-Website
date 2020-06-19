<?php
// FROM HASH: 96290f6a25c12b2026414d4a0aa6e8da
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Translate phrases' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['language']['title']));
	$__finalCompiled .= '

<div class="block">
	<div class="block-outer">
		<div class="block-outer-opposite">
			<div class="buttonGroup">
				' . $__templater->button('Refine search', array(
		'class' => 'button--link menuTrigger',
		'data-xf-click' => 'menu',
		'aria-expanded' => 'false',
		'aria-haspopup' => 'true',
	), '', array(
	)) . '
				<div class="menu menu--wide" data-menu="menu" aria-hidden="true"
					data-href="' . $__templater->func('link', array('phrases/refine-search', null, array('language_id' => $__vars['language']['language_id'], 'translate_only' => true, ) + $__vars['conditions'], ), true) . '"
					data-load-target=".js-filterMenuBody">

					<div class="menu-content">
						<h4 class="menu-header">' . 'Refine search' . '</h4>
						<div class="js-filterMenuBody">
							<div class="menu-row">' . 'Loading' . $__vars['xf']['language']['ellipsis'] . '</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

';
	if (!$__templater->test($__vars['phrases'], 'empty', array())) {
		$__finalCompiled .= '
	<div>
		<div class="js-phraseTarget">
			';
		if ($__templater->isTraversable($__vars['phrases'])) {
			foreach ($__vars['phrases'] AS $__vars['phrase']) {
				$__finalCompiled .= '
				<span class="u-anchorTarget" id="phrase-' . $__templater->escape($__vars['phrase']['phrase_id']) . '"></span>
				' . $__templater->callMacro('phrase_translate_macros', 'expanded', array(
					'phrase' => $__vars['phrase'],
					'language' => $__vars['language'],
				), $__vars) . '
			';
			}
		}
		$__finalCompiled .= '
		</div>
		<div class="block js-phraseLoadMore">
			<div class="block-container">
				<!--<div class="block-body block-row block-row&#45;&#45;alt block-row&#45;&#45;minor block-row&#45;&#45;separated">-->
				<div class="block-footer block-footer--split">
					<span class="block-footer-counter">' . $__templater->func('display_totals', array($__vars['count'], $__vars['total'], ), true) . '</span>
					';
		if ($__vars['hasMore']) {
			$__finalCompiled .= '
						<span class="block-footer-controls">' . $__templater->button('
							' . 'More' . '
						', array(
				'href' => $__templater->func('link', array('phrases/translation', null, array('last_title' => $__vars['last']['title'], 'last_count' => $__vars['count'], 'total' => $__vars['total'], 'language_id' => $__vars['language']['language_id'], ) + $__vars['conditions'], ), false),
				'data-xf-click' => 'inserter',
				'data-append' => '.js-phraseTarget',
				'data-replace' => '.js-phraseLoadMore',
				'data-scroll-target' => '#phrase-' . $__vars['last']['phrase_id'],
				'data-animate-replace' => 'false',
			), '', array(
			)) . '</span>
					';
		}
		$__finalCompiled .= '
				</div>
			</div>
		</div>
	</div>
';
	} else {
		$__finalCompiled .= '
	<div class="blockMessage">' . 'No results found.' . '</div>
';
	}
	return $__finalCompiled;
});