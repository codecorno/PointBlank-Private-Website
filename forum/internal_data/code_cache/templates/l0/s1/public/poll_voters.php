<?php
// FROM HASH: 6c4d7c59894a5ebe69e15e1adf4a4093
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Poll voters' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['poll']['question']));
	$__finalCompiled .= '

';
	$__templater->setPageParam('head.' . 'metaNoindex', $__templater->preEscaped('<meta name="robots" content="noindex" />'));
	$__finalCompiled .= '

';
	$__templater->breadcrumbs($__vars['breadcrumbs']);
	$__finalCompiled .= '

<div class="block">
	<div class="block-container">
		<div class="block-header">' . 'Members who voted for \'' . $__templater->escape($__vars['response']['response']) . '\'' . '</div>
		<div class="block-body block-row">
			<div class="js-pollVoters">
				<ul class="listInline listInline--comma">';
	$__compilerTemp1 = '';
	if ($__templater->isTraversable($__vars['response']['Votes'])) {
		foreach ($__vars['response']['Votes'] AS $__vars['vote']) {
			$__compilerTemp1 .= '
						<li>' . $__templater->func('username_link', array($__vars['vote']['User'], false, array(
			))) . '</li>
					';
		}
	}
	$__finalCompiled .= trim('
					' . $__compilerTemp1 . '
				') . '</ul>
			</div>
		</div>
	</div>
</div>';
	return $__finalCompiled;
});