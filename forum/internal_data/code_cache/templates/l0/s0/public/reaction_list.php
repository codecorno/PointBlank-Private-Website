<?php
// FROM HASH: 6b6c5bb98d1e71199a7ae5aeacafdcf5
return array('macros' => array('reactions_list' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'reactions' => '!',
		'hasNext' => '!',
		'content' => '!',
		'link' => '!',
		'linkParams' => '!',
		'page' => '!',
		'reactionId' => '0',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	<ol class="block-body js-reactionList-' . $__templater->escape($__vars['reactionId']) . '">
		';
	if ($__templater->isTraversable($__vars['reactions'])) {
		foreach ($__vars['reactions'] AS $__vars['reaction']) {
			$__finalCompiled .= '
			<li class="block-row block-row--separated">
				';
			$__vars['extraData'] = $__templater->preEscaped('
					' . $__templater->func('reaction', array(array(
				'id' => $__vars['reaction'],
				'class' => 'reaction--right',
			))) . '
					' . $__templater->func('date_dynamic', array($__vars['reaction']['reaction_date'], array(
			))) . '
				');
			$__finalCompiled .= '
				' . $__templater->callMacro('member_list_macros', 'item', array(
				'user' => $__vars['reaction']['ReactionUser'],
				'extraData' => $__templater->filter($__vars['extraData'], array(array('preescaped', array()),), false),
			), $__vars) . '
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
						' . 'Continue' . $__vars['xf']['language']['ellipsis'] . '
				', array(
			'href' => $__templater->func('link', array($__vars['link'], $__vars['content'], $__vars['linkParams'] + array('reaction_id' => $__vars['reactionId'], 'list_only' => 1, 'page' => $__vars['page'] + 1, ), ), false),
			'data-xf-click' => 'inserter',
			'data-replace' => '.js-reactionList-' . $__vars['reactionId'],
			'data-scroll-target' => '< .overlay',
		), '', array(
		)) . '</span>
			</li>
		';
	}
	$__finalCompiled .= '
	</ol>
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__vars['title']) {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped($__templater->escape($__vars['title']));
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Members who reacted to this');
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	$__templater->setPageParam('head.' . 'metaNoindex', $__templater->preEscaped('<meta name="robots" content="noindex" />'));
	$__finalCompiled .= '

';
	if ($__vars['breadcrumbs']) {
		$__finalCompiled .= '
	';
		$__templater->breadcrumbs($__vars['breadcrumbs']);
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	if (!$__vars['listOnly']) {
		$__finalCompiled .= '
	<div class="block">
		<div class="block-container">
			<h3 class="tabs hScroller block-minorTabHeader" data-xf-init="tabs h-scroller"
				data-panes="< .block-container | .js-reactionTabPanes" role="tablist">
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
						'content' => $__vars['content'],
						'link' => $__vars['link'],
						'linkParams' => $__vars['linkParams'],
						'page' => $__vars['page'],
						'reactionId' => $__templater->filter($__vars['reactionId'], array(array('default', array(0, )),), false),
					), $__vars) . '
						</li>
					';
				} else {
					$__finalCompiled .= '
						<li data-href="' . $__templater->func('link', array($__vars['link'], $__vars['content'], $__vars['linkParams'] + array('reaction_id' => $__vars['reactionId'], 'list_only' => 1, ), ), true) . '" class="' . (($__vars['reactionId'] == $__vars['activeReactionId']) ? 'is-active' : '') . '" role="tabpanel" id="reaction-' . $__templater->escape($__vars['reactionId']) . '">
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
			'content' => $__vars['content'],
			'link' => $__vars['link'],
			'linkParams' => $__vars['linkParams'],
			'page' => $__vars['page'],
			'reactionId' => $__vars['activeReactionId'],
		), $__vars) . '
';
	}
	$__finalCompiled .= '

';
	return $__finalCompiled;
});