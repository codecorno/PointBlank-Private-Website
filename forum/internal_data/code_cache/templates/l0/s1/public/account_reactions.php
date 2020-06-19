<?php
// FROM HASH: 78f04ed151e31d43e52ec8a593b9af2c
return array('macros' => array('reactions_list' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'reactions' => '!',
		'hasNext' => '!',
		'page' => '!',
		'reactionId' => '0',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	<ul class="block-body js-reactionList-' . $__templater->escape($__vars['reactionId']) . '">
		';
	if ($__templater->isTraversable($__vars['reactions'])) {
		foreach ($__vars['reactions'] AS $__vars['reaction']) {
			$__finalCompiled .= '
			<li class="block-row block-row--separated">
				<div class="contentRow">
					<span class="contentRow-figure">
						' . $__templater->func('avatar', array($__vars['reaction']['ReactionUser'], 's', false, array(
			))) . '
					</span>
					<div class="contentRow-main">
						' . $__templater->filter($__templater->method($__vars['reaction'], 'render', array()), array(array('raw', array()),), true) . '
					</div>
				</div>
			</li>
		';
		}
	}
	$__finalCompiled .= '
		';
	if ($__vars['hasNext']) {
		$__finalCompiled .= '
			<li class="block-footer">
				<span class="block-footer-controls">' . $__templater->button('
						' . 'More' . $__vars['xf']['language']['ellipsis'] . '
				', array(
			'href' => $__templater->func('link', array('account/reactions', null, array('reaction_id' => $__vars['reactionId'], 'page' => $__vars['page'] + 1, ), ), false),
			'data-xf-click' => 'inserter',
			'data-replace' => '.js-reactionList-' . $__vars['reactionId'],
			'data-scroll-target' => '< .overlay',
		), '', array(
		)) . '</span>
			</li>
		';
	}
	$__finalCompiled .= '
	</ul>
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Reactions received');
	$__finalCompiled .= '

';
	$__templater->wrapTemplate('account_wrapper', $__vars);
	$__finalCompiled .= '

';
	if ($__templater->func('count', array($__vars['reactions'], ), false) > 0) {
		$__finalCompiled .= '
	';
		if (!$__vars['listOnly']) {
			$__finalCompiled .= '
		<div class="block">
			<div class="block-container">
				<h3 class="tabs hScroller block-minorTabHeader" data-xf-init="tabs h-scroller"
					data-panes="< .block-container | .js-reactionTabPanes" data-state="replace" role="tablist">
					<span class="hScroller-scroll">
						';
			if ($__templater->isTraversable($__vars['tabSummary'])) {
				foreach ($__vars['tabSummary'] AS $__vars['reactionId'] => $__vars['total']) {
					$__finalCompiled .= '
							<a class="tabs-tab tabs-tab--reaction' . $__templater->escape($__vars['reactionId']) . (($__vars['reactionId'] == $__vars['activeReactionId']) ? ' is-active' : '') . '" role="tab" id="' . ($__vars['reactionId'] ? ('reaction-' . $__templater->escape($__vars['reactionId'])) : '') . '">
								';
					if ($__vars['reactionId']) {
						$__finalCompiled .= '
									' . $__templater->func('reaction', array(array(
							'id' => $__vars['reactionId'],
							'small' => 'true',
							'showtitle' => 'true',
							'appendtitle' => $__templater->filter($__vars['total'], array(array('number', array()),array('parens', array()),), false),
						))) . '
								';
					} else {
						$__finalCompiled .= '
									<bdi>' . 'All' . '</bdi> ' . $__templater->filter($__vars['total'], array(array('number', array()),array('parens', array()),), true) . '
								';
					}
					$__finalCompiled .= '
							</a>
						';
				}
			}
			$__finalCompiled .= '
					</span>
				</h3>
				<ul class="tabPanes js-reactionTabPanes">
					';
			if ($__templater->isTraversable($__vars['tabSummary'])) {
				foreach ($__vars['tabSummary'] AS $__vars['reactionId'] => $__vars['total']) {
					$__finalCompiled .= '
						';
					if ($__vars['reactionId'] == $__vars['activeReactionId']) {
						$__finalCompiled .= '
							<li class="' . (($__vars['reactionId'] == $__vars['activeReactionId']) ? 'is-active' : '') . '" role="tabpanel" id="reaction-' . $__templater->escape($__vars['reactionId']) . '">
								' . $__templater->callMacro(null, 'reactions_list', array(
							'reactions' => $__vars['reactions'],
							'hasNext' => $__vars['hasNext'],
							'page' => $__vars['page'],
							'reactionId' => $__templater->filter($__vars['reactionId'], array(array('default', array(0, )),), false),
						), $__vars) . '
							</li>
						';
					} else {
						$__finalCompiled .= '
							<li data-href="' . $__templater->func('link', array('account/reactions', null, array('reaction_id' => $__vars['reactionId'], 'list_only' => 1, ), ), true) . '" class="' . (($__vars['reactionId'] == $__vars['activeReactionId']) ? 'is-active' : '') . '" role="tabpanel" id="reaction-' . $__templater->escape($__vars['reactionId']) . '">
								<div class="blockMessage">' . 'Loading' . $__vars['xf']['language']['ellipsis'] . '</div>
							</li>
						';
					}
					$__finalCompiled .= '
					';
				}
			}
			$__finalCompiled .= '
				</ul>
			</div>
		</div>
	';
		} else {
			$__finalCompiled .= '
		' . $__templater->callMacro(null, 'reactions_list', array(
				'reactions' => $__vars['reactions'],
				'hasNext' => $__vars['hasNext'],
				'page' => $__vars['page'],
				'reactionId' => $__templater->filter($__vars['activeReactionId'], array(array('default', array(0, )),), false),
			), $__vars) . '
	';
		}
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	<div class="blockMessage">' . 'Unfortunately, none of your content has received any reactions yet. You\'ll need to keep posting!' . '</div>
';
	}
	$__finalCompiled .= '

';
	return $__finalCompiled;
});