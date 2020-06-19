<?php
// FROM HASH: 96dff9b81af586d2c132b6313761b064
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Recent content by ' . $__templater->escape($__vars['user']['username']) . '');
	$__finalCompiled .= '

';
	$__templater->setPageParam('head.' . 'metaNoindex', $__templater->preEscaped('<meta name="robots" content="noindex" />'));
	$__finalCompiled .= '

';
	$__templater->breadcrumb($__templater->preEscaped($__templater->escape($__vars['user']['username'])), $__templater->func('link', array('members', $__vars['user'], ), false), array(
	));
	$__finalCompiled .= '

';
	$__templater->includeCss('search_results.less');
	$__finalCompiled .= '

';
	if ($__vars['resultCount']) {
		$__finalCompiled .= '
	<div class="block">
		<div class="block-container">
			<ol class="block-body">
				';
		if ($__templater->isTraversable($__vars['results'])) {
			foreach ($__vars['results'] AS $__vars['result']) {
				$__finalCompiled .= '
					' . $__templater->filter($__templater->method($__vars['result'], 'render', array()), array(array('raw', array()),), true) . '
				';
			}
		}
		$__finalCompiled .= '
			</ol>
			<div class="block-footer">
				<span class="block-footer-controls">' . $__templater->button('
					' . 'See more' . '
				', array(
			'href' => $__templater->func('link', array('search/member', null, array('user_id' => $__vars['user']['user_id'], ), ), false),
			'rel' => 'nofollow',
		), '', array(
		)) . '</span>
			</div>
		</div>
	</div>
';
	} else {
		$__finalCompiled .= '
	<div class="blockMessage">' . '' . $__templater->escape($__vars['user']['username']) . ' has not posted any content recently.' . '</div>
';
	}
	return $__finalCompiled;
});