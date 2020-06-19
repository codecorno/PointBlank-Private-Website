<?php
// FROM HASH: 2c6fb5aaf5311b2c320fce61bf305c64
return array('macros' => array('item' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'thread' => '!',
		'forum' => '',
		'forceRead' => false,
		'showWatched' => true,
		'allowInlineMod' => true,
		'chooseName' => '',
		'extraInfo' => '',
		'allowEdit' => true,
	), $__arguments, $__vars);
	$__finalCompiled .= '

	';
	$__templater->includeCss('structured_list.less');
	$__finalCompiled .= '

	<div class="structItem structItem--thread' . ($__vars['thread']['prefix_id'] ? (' is-prefix' . $__templater->escape($__vars['thread']['prefix_id'])) : '') . ($__templater->method($__vars['thread'], 'isIgnored', array()) ? ' is-ignored' : '') . (($__templater->method($__vars['thread'], 'isUnread', array()) AND (!$__vars['forceRead'])) ? ' is-unread' : '') . (($__vars['thread']['discussion_state'] == 'moderated') ? ' is-moderated' : '') . (($__vars['thread']['discussion_state'] == 'deleted') ? ' is-deleted' : '') . ' js-inlineModContainer js-threadListItem-' . $__templater->escape($__vars['thread']['thread_id']) . '" data-author="' . ($__templater->escape($__vars['thread']['User']['username']) ?: $__templater->escape($__vars['thread']['username'])) . '">
		<div class="structItem-cell structItem-cell--icon">
			<div class="structItem-iconContainer">
				' . $__templater->func('avatar', array($__vars['thread']['User'], 's', false, array(
		'defaultname' => $__vars['thread']['username'],
	))) . '
				';
	if ($__templater->method($__vars['thread'], 'getUserPostCount', array())) {
		$__finalCompiled .= '
					' . $__templater->func('avatar', array($__vars['xf']['visitor'], 's', false, array(
			'href' => '',
			'class' => 'avatar--separated structItem-secondaryIcon',
			'tabindex' => '0',
			'data-xf-init' => 'tooltip',
			'data-trigger' => 'auto',
			'title' => 'You have posted ' . $__templater->method($__vars['thread'], 'getUserPostCount', array()) . ' message(s) in this thread',
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
	if (($__templater->func('property', array('reactionSummaryOnLists', ), false) == 'status') AND $__vars['thread']['first_post_reactions']) {
		$__compilerTemp1 .= '
						<li>' . $__templater->func('reactions_summary', array($__vars['thread']['first_post_reactions'])) . '</li>
					';
	}
	$__compilerTemp1 .= '
					';
	if ($__vars['thread']['discussion_state'] == 'moderated') {
		$__compilerTemp1 .= '
						<li>
							<i class="structItem-status structItem-status--moderated" aria-hidden="true" title="' . $__templater->filter('Awaiting approval', array(array('for_attr', array()),), true) . '"></i>
							<span class="u-srOnly">' . 'Awaiting approval' . '</span>
						</li>
					';
	}
	$__compilerTemp1 .= '
					';
	if ($__vars['thread']['discussion_state'] == 'deleted') {
		$__compilerTemp1 .= '
						<li>
							<i class="structItem-status structItem-status--deleted" aria-hidden="true" title="' . $__templater->filter('Deleted', array(array('for_attr', array()),), true) . '"></i>
							<span class="u-srOnly">' . 'Deleted' . '</span>
						</li>
					';
	}
	$__compilerTemp1 .= '
					';
	if (!$__vars['thread']['discussion_open']) {
		$__compilerTemp1 .= '
						<li>
							<i class="structItem-status structItem-status--locked" aria-hidden="true" title="' . $__templater->filter('Locked', array(array('for_attr', array()),), true) . '"></i>
							<span class="u-srOnly">' . 'Locked' . '</span>
						</li>
					';
	}
	$__compilerTemp1 .= '
					';
	if ($__vars['thread']['discussion_type'] == 'redirect') {
		$__compilerTemp1 .= '
						<li>
							<i class="structItem-status structItem-status--redirect" aria-hidden="true" title="' . $__templater->filter('Redirect', array(array('for_attr', array()),), true) . '"></i>
							<span class="u-srOnly">' . 'Redirect' . '</span>
						</li>
					';
	} else if ($__vars['thread']['discussion_type'] == 'poll') {
		$__compilerTemp1 .= '
						<li>
							<i class="structItem-status structItem-status--poll" aria-hidden="true" title="' . $__templater->filter('Poll', array(array('for_attr', array()),), true) . '"></i>
							<span class="u-srOnly">' . 'Poll' . '</span>
						</li>
					';
	}
	$__compilerTemp1 .= '
					';
	if ($__vars['thread']['sticky']) {
		$__compilerTemp1 .= '
						<li>
							<i class="structItem-status structItem-status--sticky" aria-hidden="true" title="' . $__templater->filter('Sticky', array(array('for_attr', array()),), true) . '"></i>
							<span class="u-srOnly">' . 'Sticky' . '</span>
						</li>
					';
	}
	$__compilerTemp1 .= '
					';
	if ($__vars['showWatched'] AND $__vars['xf']['visitor']['user_id']) {
		$__compilerTemp1 .= '
						';
		if ($__vars['thread']['Watch'][$__vars['xf']['visitor']['user_id']]) {
			$__compilerTemp1 .= '
							<li>
								<i class="structItem-status structItem-status--watched" aria-hidden="true" title="' . $__templater->filter('Thread watched', array(array('for_attr', array()),), true) . '"></i>
								<span class="u-srOnly">' . 'Thread watched' . '</span>
							</li>
						';
		} else if ((!$__vars['forum']) AND $__vars['thread']['Forum']['Watch'][$__vars['xf']['visitor']['user_id']]) {
			$__compilerTemp1 .= '
							<li>
								<i class="structItem-status structItem-status--watched" aria-hidden="true" title="' . $__templater->filter('Forum watched', array(array('for_attr', array()),), true) . '"></i>
								<span class="u-srOnly">' . 'Forum watched' . '</span>
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
	$__vars['canPreview'] = $__templater->method($__vars['thread'], 'canPreview', array());
	$__finalCompiled .= '
				';
	if ($__vars['thread']['prefix_id']) {
		$__finalCompiled .= '
					';
		if ($__vars['forum']) {
			$__finalCompiled .= '
						<a href="' . $__templater->func('link', array('forums', $__vars['forum'], array('prefix_id' => $__vars['thread']['prefix_id'], ), ), true) . '" class="labelLink" rel="nofollow">' . $__templater->func('prefix', array('thread', $__vars['thread'], 'html', '', ), true) . '</a>
					';
		} else {
			$__finalCompiled .= '
						' . $__templater->func('prefix', array('thread', $__vars['thread'], 'html', '', ), true) . '
					';
		}
		$__finalCompiled .= '
				';
	}
	$__finalCompiled .= '
				<a href="' . $__templater->func('link', array('threads' . (($__templater->method($__vars['thread'], 'isUnread', array()) AND (!$__vars['forceRead'])) ? '/unread' : ''), $__vars['thread'], ), true) . '" class="" data-tp-primary="on" data-xf-init="' . ($__vars['canPreview'] ? 'preview-tooltip' : '') . '" data-preview-url="' . ($__vars['canPreview'] ? $__templater->func('link', array('threads/preview', $__vars['thread'], ), true) : '') . '">' . $__templater->escape($__vars['thread']['title']) . '</a>
			</div>

			<div class="structItem-minor">
				';
	$__compilerTemp2 = '';
	$__compilerTemp2 .= '
						';
	if (($__templater->func('property', array('reactionSummaryOnLists', ), false) == 'minor_opposite') AND $__vars['thread']['first_post_reactions']) {
		$__compilerTemp2 .= '
							<li>' . $__templater->func('reactions_summary', array($__vars['thread']['first_post_reactions'])) . '</li>
						';
	}
	$__compilerTemp2 .= '
						';
	if ($__vars['extraInfo']) {
		$__compilerTemp2 .= '
							<li>' . $__templater->escape($__vars['extraInfo']) . '</li>
						';
	} else if ($__vars['allowEdit'] AND ($__templater->method($__vars['thread'], 'canEdit', array()) AND $__templater->method($__vars['thread'], 'canUseInlineModeration', array()))) {
		$__compilerTemp2 .= '
							';
		if ((!$__vars['allowInlineMod']) OR (!$__vars['forum'])) {
			$__compilerTemp2 .= '
								';
			$__vars['editParams'] = array('_xfNoInlineMod' => ((!$__vars['allowInlineMod']) ? 1 : null), '_xfForumName' => ((!$__vars['forum']) ? 1 : 0), );
			$__compilerTemp2 .= '
							';
		} else {
			$__compilerTemp2 .= '
								';
			$__vars['editParams'] = array();
			$__compilerTemp2 .= '
							';
		}
		$__compilerTemp2 .= '
							';
		if ($__vars['thread']['discussion_type'] != 'redirect') {
			$__compilerTemp2 .= '
								<li class="structItem-extraInfoMinor">
									<a href="' . $__templater->func('link', array('threads/edit', $__vars['thread'], ), true) . '" data-xf-click="overlay" data-cache="false" data-href="' . $__templater->func('link', array('threads/edit', $__vars['thread'], $__vars['editParams'], ), true) . '">
										' . 'Edit' . '
									</a>
								</li>
							';
		}
		$__compilerTemp2 .= '
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
			'value' => $__vars['thread']['thread_id'],
			'class' => 'js-chooseItem',
			'_type' => 'option',
		))) . '</li>
						';
	} else if ($__vars['allowInlineMod'] AND $__templater->method($__vars['thread'], 'canUseInlineModeration', array())) {
		$__compilerTemp2 .= '
							<li>' . $__templater->formCheckBox(array(
			'standalone' => 'true',
		), array(array(
			'value' => $__vars['thread']['thread_id'],
			'class' => 'js-inlineModToggle',
			'data-xf-init' => 'tooltip',
			'title' => 'Select for moderation',
			'label' => 'Select for moderation',
			'hiddenlabel' => 'true',
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
	if ($__vars['thread']['discussion_state'] == 'deleted') {
		$__finalCompiled .= '
					';
		if ($__vars['extraInfo']) {
			$__finalCompiled .= '<span class="structItem-extraInfo">' . $__templater->escape($__vars['extraInfo']) . '</span>';
		}
		$__finalCompiled .= '

					' . $__templater->callMacro('deletion_macros', 'notice', array(
			'log' => $__vars['thread']['DeletionLog'],
		), $__vars) . '
				';
	} else {
		$__finalCompiled .= '
					<ul class="structItem-parts">
						<li>' . $__templater->func('username_link', array($__vars['thread']['User'], false, array(
			'defaultname' => $__vars['thread']['username'],
		))) . '</li>
						<li class="structItem-startDate"><a href="' . $__templater->func('link', array('threads', $__vars['thread'], ), true) . '" rel="nofollow">' . $__templater->func('date_dynamic', array($__vars['thread']['post_date'], array(
		))) . '</a></li>
						';
		if (!$__vars['forum']) {
			$__finalCompiled .= '
							<li><a href="' . $__templater->func('link', array('forums', $__vars['thread']['Forum'], ), true) . '">' . $__templater->escape($__vars['thread']['Forum']['title']) . '</a></li>
						';
		}
		$__finalCompiled .= '
					</ul>

					';
		if (($__vars['thread']['discussion_type'] != 'redirect') AND (($__vars['thread']['reply_count'] >= $__vars['xf']['options']['messagesPerPage']) AND $__vars['xf']['options']['lastPageLinks'])) {
			$__finalCompiled .= '
						<span class="structItem-pageJump">
						';
			$__compilerTemp3 = $__templater->func('last_pages', array($__vars['thread']['reply_count'] + 1, $__vars['xf']['options']['messagesPerPage'], $__vars['xf']['options']['lastPageLinks'], ), false);
			if ($__templater->isTraversable($__compilerTemp3)) {
				foreach ($__compilerTemp3 AS $__vars['p']) {
					$__finalCompiled .= '
							<a href="' . $__templater->func('link', array('threads', $__vars['thread'], array('page' => $__vars['p'], ), ), true) . '">' . $__templater->escape($__vars['p']) . '</a>
						';
				}
			}
			$__finalCompiled .= '
						</span>
					';
		}
		$__finalCompiled .= '
				';
	}
	$__finalCompiled .= '
			</div>
		</div>
		<div class="structItem-cell structItem-cell--meta" title="' . $__templater->filter('First message reaction score' . $__vars['xf']['language']['label_separator'], array(array('for_attr', array()),), true) . ' ' . $__templater->filter($__vars['thread']['first_post_reaction_score'], array(array('number', array()),), true) . '">
			<dl class="pairs pairs--justified">
				<dt>' . 'Replies' . '</dt>
				<dd>' . (($__vars['thread']['discussion_type'] == 'redirect') ? '&ndash;' : $__templater->filter($__vars['thread']['reply_count'], array(array('number_short', array()),), true)) . '</dd>
			</dl>
			<dl class="pairs pairs--justified structItem-minor">
				<dt>' . 'Views' . '</dt>
				<dd>' . (($__vars['thread']['discussion_type'] == 'redirect') ? '&ndash;' : (($__vars['thread']['view_count'] > $__vars['thread']['reply_count']) ? $__templater->filter($__vars['thread']['view_count'], array(array('number_short', array()),), true) : $__templater->func('number_short', array($__vars['thread']['reply_count'] + 1, ), true))) . '</dd>
			</dl>
		</div>
		<div class="structItem-cell structItem-cell--latest">
			';
	if ($__vars['thread']['discussion_type'] == 'redirect') {
		$__finalCompiled .= '
				' . 'N/A' . '
			';
	} else {
		$__finalCompiled .= '
				<a href="' . $__templater->func('link', array('threads/latest', $__vars['thread'], ), true) . '" rel="nofollow">' . $__templater->func('date_dynamic', array($__vars['thread']['last_post_date'], array(
			'class' => 'structItem-latestDate',
		))) . '</a>
				<div class="structItem-minor">
					';
		if ($__templater->method($__vars['xf']['visitor'], 'isIgnoring', array($__vars['thread']['last_post_user_id'], ))) {
			$__finalCompiled .= '
						' . 'Ignored member' . '
					';
		} else {
			$__finalCompiled .= '
						' . $__templater->func('username_link', array($__vars['thread']['LastPoster'], false, array(
				'defaultname' => $__vars['thread']['last_post_username'],
			))) . '
					';
		}
		$__finalCompiled .= '
				</div>
			';
	}
	$__finalCompiled .= '
		</div>
		<div class="structItem-cell structItem-cell--icon structItem-cell--iconEnd">
			<div class="structItem-iconContainer">
				';
	if ($__templater->method($__vars['xf']['visitor'], 'isIgnoring', array($__vars['thread']['last_post_user_id'], )) OR ($__vars['thread']['discussion_type'] == 'redirect')) {
		$__finalCompiled .= '
					' . $__templater->func('avatar', array(null, 'xxs', false, array(
		))) . '
				';
	} else {
		$__finalCompiled .= '
					' . $__templater->func('avatar', array($__vars['thread']['LastPoster'], 'xxs', false, array(
			'defaultname' => $__vars['thread']['last_post_username'],
		))) . '
				';
	}
	$__finalCompiled .= '
			</div>
		</div>
	</div>
';
	return $__finalCompiled;
},
'item_new_posts' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'thread' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	<div class="contentRow">
		<div class="contentRow-figure">
			' . $__templater->func('avatar', array($__vars['thread']['LastPoster'], 'xxs', false, array(
		'defaultname' => $__vars['thread']['last_post_username'],
	))) . '
		</div>
		<div class="contentRow-main contentRow-main--close">
			';
	if ($__templater->method($__vars['thread'], 'isUnread', array())) {
		$__finalCompiled .= '
				<a href="' . $__templater->func('link', array('threads/unread', $__vars['thread'], ), true) . '">' . $__templater->func('prefix', array('thread', $__vars['thread'], ), true) . $__templater->escape($__vars['thread']['title']) . '</a>
			';
	} else {
		$__finalCompiled .= '
				<a href="' . $__templater->func('link', array('threads/post', $__vars['thread'], array('post_id' => $__vars['thread']['last_post_id'], ), ), true) . '">' . $__templater->func('prefix', array('thread', $__vars['thread'], ), true) . $__templater->escape($__vars['thread']['title']) . '</a>
			';
	}
	$__finalCompiled .= '

			<div class="contentRow-minor contentRow-minor--hideLinks">
				<ul class="listInline listInline--bullet">
					<li>' . 'Latest: ' . $__templater->escape($__vars['thread']['last_post_cache']['username']) . '' . '</li>
					<li>' . $__templater->func('date_dynamic', array($__vars['thread']['last_post_date'], array(
	))) . '</li>
				</ul>
			</div>
			<div class="contentRow-minor contentRow-minor--hideLinks">
				<a href="' . $__templater->func('link', array('forums', $__vars['thread']['Forum'], ), true) . '">' . $__templater->escape($__vars['thread']['Forum']['title']) . '</a>
			</div>
		</div>
	</div>
';
	return $__finalCompiled;
},
'item_new_threads' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'thread' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	<div class="contentRow">
		<div class="contentRow-figure">
			' . $__templater->func('avatar', array($__vars['thread']['User'], 'xxs', false, array(
		'defaultname' => $__vars['thread']['username'],
	))) . '
		</div>
		<div class="contentRow-main contentRow-main--close">
			<a href="' . $__templater->func('link', array('threads', $__vars['thread'], ), true) . '">' . $__templater->func('prefix', array('thread', $__vars['thread'], ), true) . $__templater->escape($__vars['thread']['title']) . '</a>

			<div class="contentRow-minor contentRow-minor--hideLinks">
				<ul class="listInline listInline--bullet">
					<li>' . 'Started by ' . $__templater->escape($__vars['thread']['username']) . '' . '</li>
					<li>' . $__templater->func('date_dynamic', array($__vars['thread']['post_date'], array(
	))) . '</li>
					<li>' . 'Replies' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->filter($__vars['thread']['reply_count'], array(array('number_short', array()),), true) . '</li>
				</ul>
			</div>
			<div class="contentRow-minor contentRow-minor--hideLinks">
				<a href="' . $__templater->func('link', array('forums', $__vars['thread']['Forum'], ), true) . '">' . $__templater->escape($__vars['thread']['Forum']['title']) . '</a>
			</div>
		</div>
	</div>
';
	return $__finalCompiled;
},
'quick_thread' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'forum' => '!',
		'page' => '1',
		'order' => 'last_post_date',
		'direction' => 'desc',
	), $__arguments, $__vars);
	$__finalCompiled .= '

	';
	$__templater->includeCss('structured_list.less');
	$__finalCompiled .= '

	';
	if ($__templater->method($__vars['forum'], 'canCreateThread', array())) {
		$__finalCompiled .= '

		';
		$__templater->includeJs(array(
			'src' => 'xf/thread.js',
			'min' => '1',
		));
		$__finalCompiled .= '

		';
		$__vars['inlineMode'] = ((($__vars['page'] == 1) AND (($__vars['order'] == 'last_post_date') AND ($__vars['direction'] == 'desc'))) ? true : false);
		$__finalCompiled .= '

		' . $__templater->form('

			<div class="structItem-cell structItem-cell--icon">
				<div class="structItem-iconContainer">
					' . $__templater->func('avatar', array($__vars['xf']['visitor'], 's', false, array(
		))) . '
				</div>
			</div>
			<div class="structItem-cell structItem-cell--newThread js-prefixListenContainer">

				' . $__templater->formRow('

					' . $__templater->formPrefixInput($__templater->method($__vars['forum'], 'getUsablePrefixes', array()), array(
			'maxlength' => $__templater->func('max_length', array('XF:Thread', 'title', ), false),
			'placeholder' => $__vars['forum']['thread_prompt'],
			'title' => 'Post a new thread in this forum',
			'prefix-value' => $__vars['forum']['default_prefix_id'],
			'type' => 'thread',
			'data-xf-init' => 'tooltip',
			'rows' => '1',
		)) . '
				', array(
			'rowtype' => 'noGutter noLabel fullWidth noPadding mergeNext',
			'label' => 'Title',
		)) . '

				<div class="js-quickThreadFields inserter-container is-hidden"><!--' . 'Loading' . $__vars['xf']['language']['ellipsis'] . '--></div>
			</div>
		', array(
			'action' => $__templater->func('link', array('forums/post-thread', $__vars['forum'], array('inline-mode' => $__vars['inlineMode'], ), ), false),
			'class' => 'structItem',
			'ajax' => 'true',
			'draft' => $__templater->func('link', array('forums/draft', $__vars['forum'], ), false),
			'data-xf-init' => 'quick-thread',
			'data-focus-activate' => '.js-titleInput',
			'data-focus-activate-href' => $__templater->func('link', array('forums/post-thread', $__vars['forum'], array('inline-mode' => true, ), ), false),
			'data-focus-activate-target' => '.js-quickThreadFields',
			'data-insert-target' => '.js-threadList',
			'data-replace-target' => '.js-emptyThreadList',
		)) . '
	';
	}
	$__finalCompiled .= '

';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '

' . '

' . '

';
	return $__finalCompiled;
});