<?php
// FROM HASH: 3b364de2e2c369973441657c21476789
return array('macros' => array(), 'code' => function($__templater, array $__vars)
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
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Members who liked this');
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

<div class="block">
	<div class="block-container js-likeList-' . $__templater->escape($__vars['type']) . $__templater->escape($__vars['id']) . '">
		<ol class="block-body">
			';
	if ($__templater->isTraversable($__vars['likes'])) {
		foreach ($__vars['likes'] AS $__vars['like']) {
			$__finalCompiled .= '
				<li class="block-row block-row--separated">
					' . $__templater->callMacro('member_list_macros', 'item', array(
				'user' => $__vars['like']['Liker'],
				'extraData' => $__templater->filter($__templater->func('date_dynamic', array($__vars['like']['like_date'], ), false), array(array('preescaped', array()),), false),
			), $__vars) . '
				</li>
			';
		}
	}
	$__finalCompiled .= '
		</ol>
		';
	if ($__vars['hasNext']) {
		$__finalCompiled .= '
			<div class="block-footer">
				<span class="block-footer-controls">' . $__templater->button('
					' . 'Continue' . $__vars['xf']['language']['ellipsis'] . '
				', array(
			'href' => $__templater->func('link', array($__vars['linkRoute'], $__vars['linkData'], $__vars['linkParams'] + array('page' => $__vars['page'] + 1, ), ), false),
			'data-xf-click' => 'inserter',
			'data-replace' => '.js-likeList-' . $__vars['type'] . $__vars['id'],
			'data-scroll-target' => '< .overlay',
		), '', array(
		)) . '</span>
			</div>
		';
	}
	$__finalCompiled .= '
	</div>
</div>';
	return $__finalCompiled;
});