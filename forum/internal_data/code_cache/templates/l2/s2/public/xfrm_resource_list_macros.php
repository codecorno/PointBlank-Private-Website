<?php
// FROM HASH: 5bf1046e84e5beb76cc61e4a9f238439
return array('macros' => array('resource' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'filterPrefix' => false,
		'resource' => '!',
		'category' => null,
		'showWatched' => true,
		'allowInlineMod' => true,
		'chooseName' => '',
		'extraInfo' => '',
	), $__arguments, $__vars);
	$__finalCompiled .= '

	';
	$__templater->includeCss('structured_list.less');
	$__finalCompiled .= '
	';
	$__templater->includeCss('xfrm.less');
	$__finalCompiled .= '

	<div class="structItem structItem--resource ' . ($__vars['resource']['prefix_id'] ? ('is-prefix' . $__templater->escape($__vars['resource']['prefix_id'])) : '') . ' ' . ($__templater->method($__vars['resource'], 'isIgnored', array()) ? 'is-ignored' : '') . (($__vars['resource']['resource_state'] == 'moderated') ? 'is-moderated' : '') . (($__vars['resource']['resource_state'] == 'deleted') ? 'is-deleted' : '') . ' js-inlineModContainer js-resourceListItem-' . $__templater->escape($__vars['resource']['resource_id']) . '" data-author="' . ($__templater->escape($__vars['resource']['User']['username']) ?: $__templater->escape($__vars['resource']['username'])) . '">
		<div class="structItem-cell structItem-cell--icon structItem-cell--iconExpanded">
			<div class="structItem-iconContainer">
				';
	if ($__vars['xf']['options']['xfrmAllowIcons']) {
		$__finalCompiled .= '
					' . $__templater->func('resource_icon', array($__vars['resource'], 's', $__templater->func('link', array('resources', $__vars['resource'], ), false), ), true) . '
					' . $__templater->func('avatar', array($__vars['resource']['User'], 's', false, array(
			'href' => '',
			'class' => 'avatar--separated structItem-secondaryIcon',
		))) . '
				';
	} else {
		$__finalCompiled .= '
					' . $__templater->func('avatar', array($__vars['resource']['User'], 's', false, array(
			'defaultname' => $__vars['resource']['username'],
		))) . '
				';
	}
	$__finalCompiled .= '
			</div>
		</div>
		<div class="structItem-cell structItem-cell--main" data-xf-init="touch-proxy">
			';
	$__compilerTemp1 = '';
	$__compilerTemp1 .= '
					';
	if ($__vars['resource']['Featured']) {
		$__compilerTemp1 .= '
						<li>
							<i class="structItem-status structItem-status--attention" aria-hidden="true" title="' . $__templater->filter('xfrm_featured', array(array('for_attr', array()),), true) . '"></i>
							<span class="u-srOnly">' . 'xfrm_featured' . '</span>
						</li>
					';
	}
	$__compilerTemp1 .= '
					';
	if ($__vars['resource']['resource_state'] == 'moderated') {
		$__compilerTemp1 .= '
						<li>
							<i class="structItem-status structItem-status--moderated" aria-hidden="true" title="' . $__templater->filter('Aguardando aprovação', array(array('for_attr', array()),), true) . '"></i>
							<span class="u-srOnly">' . 'Aguardando aprovação' . '</span>
						</li>
					';
	}
	$__compilerTemp1 .= '
					';
	if ($__vars['resource']['resource_state'] == 'deleted') {
		$__compilerTemp1 .= '
						<li>
							<i class="structItem-status structItem-status--deleted" aria-hidden="true" title="' . $__templater->filter('Excluído', array(array('for_attr', array()),), true) . '"></i>
							<span class="u-srOnly">' . 'Excluído' . '</span>
						</li>
					';
	}
	$__compilerTemp1 .= '
					';
	if ($__vars['showWatched'] AND $__vars['xf']['visitor']['user_id']) {
		$__compilerTemp1 .= '
						';
		if ($__vars['resource']['Watch'][$__vars['xf']['visitor']['user_id']]) {
			$__compilerTemp1 .= '
							<li>
								<i class="structItem-status structItem-status--watched" aria-hidden="true" title="' . $__templater->filter('xfrm_resource_watched', array(array('for_attr', array()),), true) . '"></i>
								<span class="u-srOnly">' . 'xfrm_resource_watched' . '</span>
							</li>
						';
		} else if ((!$__vars['category']) AND $__vars['resource']['Category']['Watch'][$__vars['xf']['visitor']['user_id']]) {
			$__compilerTemp1 .= '
							<li>
								<i class="structItem-status structItem-status--watched" aria-hidden="true" title="' . $__templater->filter('xfrm_category_watched', array(array('for_attr', array()),), true) . '"></i>
								<span class="u-srOnly">' . 'xfrm_category_watched' . '</span>
							</li>
						';
		}
		$__compilerTemp1 .= '
					';
	}
	$__compilerTemp1 .= '
				';
	if (strlen(trim($__compilerTemp1)) > 0) {
		$__finalCompiled .= '
				<ul class="structItem-statuses">
				' . $__compilerTemp1 . '
				</ul>
			';
	}
	$__finalCompiled .= '

			<div class="structItem-title">
				';
	if ($__vars['resource']['prefix_id']) {
		$__finalCompiled .= '
					';
		if ($__vars['category']) {
			$__finalCompiled .= '
						<a href="' . $__templater->func('link', array('resources/categories', $__vars['category'], array('prefix_id' => $__vars['resource']['prefix_id'], ), ), true) . '" class="labelLink" rel="nofollow">' . $__templater->func('prefix', array('resource', $__vars['resource'], 'html', '', ), true) . '</a>
					';
		} else {
			$__finalCompiled .= '
						';
			if ($__vars['filterPrefix']) {
				$__finalCompiled .= '
							<a href="' . $__templater->func('link', array('resources', null, array('prefix_id' => $__vars['resource']['prefix_id'], ), ), true) . '" class="labelLink" rel="nofollow">' . $__templater->func('prefix', array('resource', $__vars['resource'], 'html', '', ), true) . '</a>
						';
			} else {
				$__finalCompiled .= '
							' . $__templater->func('prefix', array('resource', $__vars['resource'], 'html', '', ), true) . '
						';
			}
			$__finalCompiled .= '
					';
		}
		$__finalCompiled .= '
				';
	}
	$__finalCompiled .= '
				<a href="' . $__templater->func('link', array('resources', $__vars['resource'], ), true) . '" class="" data-tp-primary="on">' . $__templater->escape($__vars['resource']['title']) . '</a>
				';
	if ($__templater->method($__vars['resource'], 'isVersioned', array())) {
		$__finalCompiled .= '
					<span class="u-muted">' . $__templater->escape($__vars['resource']['CurrentVersion']['version_string']) . '</span>
				';
	}
	$__finalCompiled .= '
				';
	if ($__templater->method($__vars['resource'], 'isExternalPurchasable', array())) {
		$__finalCompiled .= '
					<span class="label label--primary label--smallest">' . $__templater->filter($__vars['resource']['price'], array(array('currency', array($__vars['resource']['currency'], )),), true) . '</span>
				';
	}
	$__finalCompiled .= '
			</div>

			<div class="structItem-minor">
				';
	$__compilerTemp2 = '';
	$__compilerTemp2 .= '
						';
	if ($__vars['extraInfo']) {
		$__compilerTemp2 .= '
							<li>' . $__templater->escape($__vars['extraInfo']) . '</li>
						';
	}
	$__compilerTemp2 .= '
						';
	if ($__vars['chooseName']) {
		$__compilerTemp2 .= '
							<li>' . $__templater->formCheckBox(array(
			'standalone' => 'true',
		), array(array(
			'name' => $__vars['chooseName'] . '[]',
			'value' => $__vars['resource']['resource_id'],
			'class' => 'js-chooseItem',
			'_type' => 'option',
		))) . '</li>
						';
	} else if ($__vars['allowInlineMod'] AND $__templater->method($__vars['resource'], 'canUseInlineModeration', array())) {
		$__compilerTemp2 .= '
							<li>' . $__templater->formCheckBox(array(
			'standalone' => 'true',
		), array(array(
			'value' => $__vars['resource']['resource_id'],
			'class' => 'js-inlineModToggle',
			'data-xf-init' => 'tooltip',
			'title' => 'Selecione para moderação',
			'_type' => 'option',
		))) . '</li>
						';
	}
	$__compilerTemp2 .= '
					';
	if (strlen(trim($__compilerTemp2)) > 0) {
		$__finalCompiled .= '
					<ul class="structItem-extraInfo">
					' . $__compilerTemp2 . '
					</ul>
				';
	}
	$__finalCompiled .= '

				';
	if ($__vars['resource']['resource_state'] == 'deleted') {
		$__finalCompiled .= '
					' . $__templater->callMacro('deletion_macros', 'notice', array(
			'log' => $__vars['resource']['DeletionLog'],
		), $__vars) . '
				';
	} else {
		$__finalCompiled .= '
					<ul class="structItem-parts">
						<li>' . $__templater->func('username_link', array($__vars['resource']['User'], false, array(
			'defaultname' => $__vars['resource']['username'],
		))) . '</li>
						<li class="structItem-startDate"><a href="' . $__templater->func('link', array('resources', $__vars['resource'], ), true) . '" rel="nofollow">' . $__templater->func('date_dynamic', array($__vars['resource']['resource_date'], array(
		))) . '</a></li>
						';
		if ((!$__vars['category']) OR $__templater->method($__vars['category'], 'hasChildren', array())) {
			$__finalCompiled .= '
							<li><a href="' . $__templater->func('link', array('resources/categories', $__vars['resource']['Category'], ), true) . '">' . $__templater->escape($__vars['resource']['Category']['title']) . '</a></li>
						';
		}
		$__finalCompiled .= '
					</ul>
				';
	}
	$__finalCompiled .= '
			</div>

			';
	if ($__vars['resource']['resource_state'] != 'deleted') {
		$__finalCompiled .= '
				<div class="structItem-resourceTagLine">' . $__templater->escape($__vars['resource']['tag_line']) . '</div>
			';
	}
	$__finalCompiled .= '
		</div>
		<div class="structItem-cell structItem-cell--resourceMeta">
			<div class="structItem-metaItem  structItem-metaItem--rating">
				' . $__templater->callMacro('rating_macros', 'stars_text', array(
		'rating' => $__vars['resource']['rating_avg'],
		'count' => $__vars['resource']['rating_count'],
		'rowClass' => 'ratingStarsRow--justified',
		'starsClass' => 'ratingStars--large',
	), $__vars) . '
			</div>

			';
	if ($__templater->method($__vars['resource'], 'isDownloadable', array())) {
		$__finalCompiled .= '
				<dl class="pairs pairs--justified structItem-minor structItem-metaItem structItem-metaItem--downloads">
					<dt>' . 'xfrm_downloads' . '</dt>
					<dd>' . $__templater->filter($__vars['resource']['download_count'], array(array('number', array()),), true) . '</dd>
				</dl>
			';
	}
	$__finalCompiled .= '
			<dl class="pairs pairs--justified structItem-minor structItem-metaItem structItem-metaItem--lastUpdate">
				<dt>' . 'Updated' . '</dt>
				<dd><a href="' . $__templater->func('link', array('resources/updates', $__vars['resource'], ), true) . '" class="u-concealed">' . $__templater->func('date_dynamic', array($__vars['resource']['last_update'], array(
	))) . '</a></dd>
			</dl>
		</div>
	</div>
';
	return $__finalCompiled;
},
'resource_simple' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'resource' => '!',
		'withMeta' => true,
	), $__arguments, $__vars);
	$__finalCompiled .= '
	<div class="contentRow">
		<div class="contentRow-figure">
			';
	if ($__vars['xf']['options']['xfrmAllowIcons']) {
		$__finalCompiled .= '
				' . $__templater->func('resource_icon', array($__vars['resource'], 'xxs', $__templater->func('link', array('resources', $__vars['resource'], ), false), ), true) . '
			';
	} else {
		$__finalCompiled .= '
				' . $__templater->func('avatar', array($__vars['resource']['User'], 'xxs', false, array(
		))) . '
			';
	}
	$__finalCompiled .= '
		</div>
		<div class="contentRow-main contentRow-main--close">
			<a href="' . $__templater->func('link', array('resources', $__vars['resource'], ), true) . '">' . $__templater->func('prefix', array('resource', $__vars['resource'], ), true) . $__templater->escape($__vars['resource']['title']) . '</a>
			<div class="contentRow-lesser">' . $__templater->escape($__vars['resource']['tag_line']) . '</div>
			';
	if ($__vars['withMeta']) {
		$__finalCompiled .= '
				<div class="contentRow-minor contentRow-minor--smaller">
					<ul class="listInline listInline--bullet">
						<li>' . ($__templater->escape($__vars['resource']['User']['username']) ?: $__templater->escape($__vars['resource']['username'])) . '</li>
						<li>' . 'Updated' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->func('date_dynamic', array($__vars['resource']['last_update'], array(
		))) . '</li>
					</ul>
				</div>
			';
	}
	$__finalCompiled .= '
		</div>
	</div>
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '

';
	return $__finalCompiled;
});