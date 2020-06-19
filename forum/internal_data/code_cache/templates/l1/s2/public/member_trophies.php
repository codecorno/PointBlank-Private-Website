<?php
// FROM HASH: 3f808562a1a14f660e968ff6d2f6c170
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Trophies awarded to ' . $__templater->escape($__vars['user']['username']) . '');
	$__finalCompiled .= '

';
	$__templater->setPageParam('head.' . 'noindex', $__templater->preEscaped('<meta name="robots" content="noindex" />'));
	$__finalCompiled .= '

';
	$__templater->breadcrumb($__templater->preEscaped($__templater->escape($__vars['user']['username'])), $__templater->func('link', array('members', $__vars['user'], ), false), array(
	));
	$__finalCompiled .= '

<div class="block">
	<div class="block-container">
		';
	if (!$__templater->test($__vars['trophies'], 'empty', array())) {
		$__finalCompiled .= '
			<ol class="block-body">
				';
		if ($__templater->isTraversable($__vars['trophies'])) {
			foreach ($__vars['trophies'] AS $__vars['trophy']) {
				$__finalCompiled .= '
					<li class="block-row block-row--separated">
						<div class="contentRow">
							<span class="contentRow-figure contentRow-figure--text contentRow-figure--fixedSmall">' . $__templater->escape($__vars['trophy']['Trophy']['trophy_points']) . '</span>
							<div class="contentRow-main">
								<span class="contentRow-extra">' . $__templater->func('date_dynamic', array($__vars['trophy']['award_date'], array(
				))) . '</span>
								<h2 class="contentRow-header">' . $__templater->escape($__vars['trophy']['Trophy']['title']) . '</h2>
								<div class="contentRow-minor">' . $__templater->filter($__vars['trophy']['Trophy']['description'], array(array('raw', array()),), true) . '</div>
							</div>
						</div>
					</li>
				';
			}
		}
		$__finalCompiled .= '
			</ol>
		';
	} else {
		$__finalCompiled .= '
			<div class="block-body block-row">' . '' . $__templater->escape($__vars['user']['username']) . ' has not been awarded any trophies yet.' . '</div>
		';
	}
	$__finalCompiled .= '
		<div class="block-footer block-footer--split">
			<span class="block-footer-counter">' . 'Total points' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->func('number', array($__vars['user']['trophy_points'], ), true) . '</span>
			<span class="block-footer-controls">
				' . $__templater->button('
					' . 'View all available trophies' . '
				', array(
		'href' => $__templater->func('link', array('help', array('page_name' => 'trophies', ), ), false),
	), '', array(
	)) . '
			</span>
		</div>
	</div>
</div>';
	return $__finalCompiled;
});