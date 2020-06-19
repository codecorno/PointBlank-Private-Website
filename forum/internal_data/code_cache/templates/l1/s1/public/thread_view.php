<?php
// FROM HASH: 47369c10ef020ca30a060d2ea8191787
return array('macros' => array('thread_status' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'thread' => '!',
		'wrapperClass' => '',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	$__compilerTemp1 = '';
	$__compilerTemp1 .= '
					';
	if ($__vars['thread']['discussion_state'] == 'deleted') {
		$__compilerTemp1 .= '
						<dd class="blockStatus-message blockStatus-message--deleted">
							' . $__templater->callMacro('deletion_macros', 'notice', array(
			'log' => $__vars['thread']['DeletionLog'],
		), $__vars) . '
						</dd>
					';
	} else if ($__vars['thread']['discussion_state'] == 'moderated') {
		$__compilerTemp1 .= '
						<dd class="blockStatus-message blockStatus-message--moderated">
							' . 'Awaiting approval before being displayed publicly.' . '
						</dd>
					';
	}
	$__compilerTemp1 .= '
					';
	if (!$__vars['thread']['discussion_open']) {
		$__compilerTemp1 .= '
						<dd class="blockStatus-message blockStatus-message--locked">
							' . 'Not open for further replies.' . '
						</dd>
					';
	}
	$__compilerTemp1 .= '
				';
	if (strlen(trim($__compilerTemp1)) > 0) {
		$__finalCompiled .= '
		<div class="' . $__templater->escape($__vars['wrapperClass']) . '">
			<dl class="blockStatus">
				<dt>' . 'Status' . '</dt>
				' . $__compilerTemp1 . '
			</dl>
		</div>
	';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped($__templater->func('prefix', array('thread', $__vars['thread'], 'escaped', ), true) . $__templater->escape($__vars['thread']['title']));
	$__templater->pageParams['pageNumber'] = $__vars['page'];
	$__finalCompiled .= '
';
	$__templater->pageParams['pageH1'] = $__templater->preEscaped($__templater->func('prefix', array('thread', $__vars['thread'], ), true) . $__templater->escape($__vars['thread']['title']));
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	if ($__vars['xf']['options']['enableTagging'] AND ($__templater->method($__vars['thread'], 'canEditTags', array()) OR $__vars['thread']['tags'])) {
		$__compilerTemp1 .= '
			<li>
				' . $__templater->callMacro('tag_macros', 'list', array(
			'tags' => $__vars['thread']['tags'],
			'tagList' => 'tagList--thread-' . $__vars['thread']['thread_id'],
			'editLink' => ($__templater->method($__vars['thread'], 'canEditTags', array()) ? $__templater->func('link', array('threads/tags', $__vars['thread'], ), false) : ''),
		), $__vars) . '
			</li>
		';
	}
	$__templater->pageParams['pageDescription'] = $__templater->preEscaped('
	<ul class="listInline listInline--bullet">
		<li>
			' . $__templater->fontAwesome('fa-user', array(
		'title' => $__templater->filter('Thread starter', array(array('for_attr', array()),), false),
	)) . '
			<span class="u-srOnly">' . 'Thread starter' . '</span>

			' . $__templater->func('username_link', array($__vars['thread']['User'], false, array(
		'defaultname' => $__vars['thread']['username'],
		'class' => 'u-concealed',
	))) . '
		</li>
		<li>
			' . $__templater->fontAwesome('fa-clock', array(
		'title' => $__templater->filter('Start date', array(array('for_attr', array()),), false),
	)) . '
			<span class="u-srOnly">' . 'Start date' . '</span>

			<a href="' . $__templater->func('link', array('threads', $__vars['thread'], ), true) . '" class="u-concealed">' . $__templater->func('date_dynamic', array($__vars['thread']['post_date'], array(
	))) . '</a>
		</li>
		' . $__compilerTemp1 . '
	</ul>
');
	$__templater->pageParams['pageDescriptionMeta'] = false;
	$__finalCompiled .= '

';
	$__vars['fpSnippet'] = $__templater->func('snippet', array($__vars['firstPost']['message'], 0, array('stripBbCode' => true, ), ), false);
	$__finalCompiled .= '

' . $__templater->callMacro('metadata_macros', 'metadata', array(
		'description' => $__vars['fpSnippet'],
		'shareUrl' => $__templater->func('link', array('canonical:threads', $__vars['thread'], ), false),
		'canonicalUrl' => $__templater->func('link', array('canonical:threads', $__vars['thread'], array('page' => $__vars['page'], ), ), false),
	), $__vars) . '

';
	$__compilerTemp2 = '';
	if ($__vars['thread']['User']['avatar_highdpi']) {
		$__compilerTemp2 .= '
		';
		$__vars['image'] = $__templater->preEscaped($__templater->escape($__templater->method($__vars['thread']['User'], 'getAvatarUrl', array('h', null, true, ))));
		$__compilerTemp2 .= '
	';
	} else if ($__vars['thread']['User']['avatar_date']) {
		$__compilerTemp2 .= '
		';
		$__vars['image'] = $__templater->preEscaped($__templater->escape($__templater->method($__vars['thread']['User'], 'getAvatarUrl', array('l', null, true, ))));
		$__compilerTemp2 .= '
	';
	} else if ($__templater->func('property', array('publicMetadataLogoUrl', ), false)) {
		$__compilerTemp2 .= '
		';
		$__vars['image'] = $__templater->preEscaped($__templater->func('base_url', array($__templater->func('property', array('publicMetadataLogoUrl', ), false), true, ), true));
		$__compilerTemp2 .= '
	';
	}
	$__compilerTemp3 = '';
	if ($__vars['image']) {
		$__compilerTemp3 .= '
		<script type="application/ld+json">
		{
			"@context": "https://schema.org",
			"@type": "DiscussionForumPosting",
			"@id": "' . $__templater->filter($__templater->func('link', array('canonical:threads', $__vars['thread'], ), false), array(array('escape', array('json', )),), true) . '",
			"headline": "' . $__templater->filter($__vars['thread']['title'], array(array('escape', array('json', )),), true) . '",
			"articleBody": "' . $__templater->filter($__vars['fpSnippet'], array(array('escape', array('json', )),), true) . '",
			"articleSection": "' . $__templater->filter($__vars['thread']['Forum']['Node']['title'], array(array('escape', array('json', )),), true) . '",
			"author": {
				"@type": "Person",
				"name": "' . $__templater->filter(($__vars['thread']['User'] ? $__vars['thread']['User']['username'] : $__vars['thread']['username']), array(array('escape', array('json', )),), true) . '"
			},
			"datePublished": "' . $__templater->filter($__templater->func('date', array($__vars['thread']['post_date'], 'Y-m-d', ), false), array(array('escape', array('json', )),), true) . '",
			"dateModified": "' . $__templater->filter($__templater->func('date', array($__vars['thread']['last_post_date'], 'Y-m-d', ), false), array(array('escape', array('json', )),), true) . '",
			"image": "' . $__templater->filter($__vars['image'], array(array('escape', array('json', )),), true) . '",
			"interactionStatistic": {
				"@type": "InteractionCounter",
				"interactionType": "https://schema.org/ReplyAction",
				"userInteractionCount": ' . $__templater->escape($__vars['thread']['reply_count']) . '
			}
		}
		</script>
	';
	}
	$__templater->setPageParam('ldJsonHtml', '
	' . $__compilerTemp2 . '
	' . $__compilerTemp3 . '
');
	$__finalCompiled .= '

' . '

';
	if ($__vars['pendingApproval']) {
		$__finalCompiled .= '
	<div class="blockMessage blockMessage--important">' . 'Your content has been submitted and will be displayed pending approval by a moderator.' . '</div>
';
	}
	$__finalCompiled .= '

' . $__templater->callMacro('forum_macros', 'forum_page_options', array(
		'forum' => $__vars['forum'],
		'thread' => $__vars['thread'],
	), $__vars) . '

';
	$__templater->breadcrumbs($__templater->method($__vars['forum'], 'getBreadcrumbs', array()));
	$__finalCompiled .= '

';
	if ($__vars['canInlineMod'] OR $__templater->method($__vars['thread'], 'canUseInlineModeration', array())) {
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

' . $__templater->callMacro('lightbox_macros', 'setup', array(
		'canViewAttachments' => $__templater->method($__vars['thread'], 'canViewAttachments', array()),
	), $__vars) . '

';
	if ($__vars['poll']) {
		$__finalCompiled .= '
	' . $__templater->callMacro('poll_macros', 'poll_block', array(
			'poll' => $__vars['poll'],
		), $__vars) . '
';
	}
	$__finalCompiled .= '

' . $__templater->callAdsMacro('thread_view_above_messages', array(
		'thread' => $__vars['thread'],
	), $__vars) . '

<div class="block block--messages" data-xf-init="' . ($__vars['canInlineMod'] ? 'inline-mod' : '') . '" data-type="post" data-href="' . $__templater->func('link', array('inline-mod', ), true) . '">

	' . $__templater->callMacro(null, 'thread_status', array(
		'thread' => $__vars['thread'],
		'wrapperClass' => 'block-outer',
	), $__vars) . '

	<div class="block-outer">';
	$__compilerTemp4 = '';
	$__compilerTemp5 = '';
	$__compilerTemp5 .= '
					';
	if ($__vars['canInlineMod']) {
		$__compilerTemp5 .= '
						' . $__templater->callMacro('inline_mod_macros', 'button', array(), $__vars) . '
					';
	}
	$__compilerTemp5 .= '
					';
	if (($__vars['thread']['discussion_state'] == 'deleted') AND $__templater->method($__vars['thread'], 'canUndelete', array())) {
		$__compilerTemp5 .= '
						' . $__templater->button('
							' . 'Undelete' . '
						', array(
			'href' => $__templater->func('link', array('threads/undelete', $__vars['thread'], ), false),
			'class' => 'button--link',
			'overlay' => 'true',
		), '', array(
		)) . '
					';
	}
	$__compilerTemp5 .= '
					';
	if ($__templater->method($__vars['thread'], 'canApproveUnapprove', array()) AND ($__vars['thread']['discussion_state'] == 'moderated')) {
		$__compilerTemp5 .= '
						' . $__templater->button('
							' . 'Approve' . '
						', array(
			'href' => $__templater->func('link', array('threads/approve', $__vars['thread'], ), false),
			'class' => 'button--link',
			'overlay' => 'true',
		), '', array(
		)) . '
					';
	}
	$__compilerTemp5 .= '
					';
	if ($__vars['xf']['visitor']['user_id'] AND $__templater->method($__vars['thread'], 'isUnread', array())) {
		$__compilerTemp5 .= '
						' . $__templater->button('
								' . 'Jump to new' . '
						', array(
			'href' => ($__vars['firstUnread'] ? ('#post-' . $__vars['firstUnread']['post_id']) : $__templater->func('link', array('threads/unread', $__vars['thread'], array('new' => 1, ), ), false)),
			'class' => 'button--link',
			'data-xf-click' => 'scroll-to',
			'data-silent' => 'true',
		), '', array(
		)) . '
					';
	}
	$__compilerTemp5 .= '
					';
	if ($__templater->method($__vars['thread'], 'canWatch', array())) {
		$__compilerTemp5 .= '
						';
		$__compilerTemp6 = '';
		if ($__vars['thread']['Watch'][$__vars['xf']['visitor']['user_id']]) {
			$__compilerTemp6 .= '
								' . 'Unwatch' . '
							';
		} else {
			$__compilerTemp6 .= '
								' . 'Watch' . '
							';
		}
		$__compilerTemp5 .= $__templater->button('
							' . $__compilerTemp6 . '
						', array(
			'href' => $__templater->func('link', array('threads/watch', $__vars['thread'], ), false),
			'class' => 'button--link',
			'data-xf-click' => 'switch-overlay',
			'data-sk-watch' => 'Watch',
			'data-sk-unwatch' => 'Unwatch',
		), '', array(
		)) . '
					';
	}
	$__compilerTemp5 .= '

					';
	$__compilerTemp7 = '';
	$__compilerTemp7 .= '
										' . '
										';
	if ($__templater->method($__vars['thread'], 'canEdit', array())) {
		$__compilerTemp7 .= '
											<a href="' . $__templater->func('link', array('threads/edit', $__vars['thread'], ), true) . '" data-xf-click="overlay" class="menu-linkRow">' . 'Edit thread' . '</a>
										';
	}
	$__compilerTemp7 .= '
										';
	if ($__templater->method($__vars['thread'], 'canLockUnlock', array())) {
		$__compilerTemp7 .= '
											<a href="' . $__templater->func('link', array('threads/quick-close', $__vars['thread'], ), true) . '"
												class="menu-linkRow"
												data-xf-click="switch"
												data-menu-closer="true">

												';
		if ($__vars['thread']['discussion_open']) {
			$__compilerTemp7 .= '
													' . 'Lock thread' . '
												';
		} else {
			$__compilerTemp7 .= '
													' . 'Unlock thread' . '
												';
		}
		$__compilerTemp7 .= '
											</a>
										';
	}
	$__compilerTemp7 .= '
										';
	if ($__templater->method($__vars['thread'], 'canStickUnstick', array())) {
		$__compilerTemp7 .= '
											<a href="' . $__templater->func('link', array('threads/quick-stick', $__vars['thread'], ), true) . '"
												class="menu-linkRow"
												data-xf-click="switch"
												data-menu-closer="true">

												';
		if ($__vars['thread']['sticky']) {
			$__compilerTemp7 .= '
													' . 'Unstick thread' . '
												';
		} else {
			$__compilerTemp7 .= '
													' . 'Stick thread' . '
												';
		}
		$__compilerTemp7 .= '
											</a>
										';
	}
	$__compilerTemp7 .= '
										';
	if ($__templater->method($__vars['thread'], 'canCreatePoll', array())) {
		$__compilerTemp7 .= '
											<a href="' . $__templater->func('link', array('threads/poll/create', $__vars['thread'], ), true) . '" data-xf-click="overlay" class="menu-linkRow">' . 'Create poll' . '</a>
										';
	}
	$__compilerTemp7 .= '
										';
	if ($__templater->method($__vars['thread'], 'canDelete', array('soft', ))) {
		$__compilerTemp7 .= '
											<a href="' . $__templater->func('link', array('threads/delete', $__vars['thread'], ), true) . '" data-xf-click="overlay" class="menu-linkRow">' . 'Delete thread' . '</a>
										';
	}
	$__compilerTemp7 .= '
										';
	if ($__templater->method($__vars['thread'], 'canMove', array())) {
		$__compilerTemp7 .= '
											<a href="' . $__templater->func('link', array('threads/move', $__vars['thread'], ), true) . '" data-xf-click="overlay" class="menu-linkRow">' . 'Move thread' . '</a>
										';
	}
	$__compilerTemp7 .= '
										';
	if ($__templater->method($__vars['thread'], 'canReplyBan', array())) {
		$__compilerTemp7 .= '
											<a href="' . $__templater->func('link', array('threads/reply-bans', $__vars['thread'], ), true) . '" data-xf-click="overlay" class="menu-linkRow">' . 'Manage reply bans' . '</a>
										';
	}
	$__compilerTemp7 .= '
										';
	if ($__templater->method($__vars['thread'], 'canViewModeratorLogs', array())) {
		$__compilerTemp7 .= '
											<a href="' . $__templater->func('link', array('threads/moderator-actions', $__vars['thread'], ), true) . '" data-xf-click="overlay" class="menu-linkRow">' . 'Moderator actions' . '</a>
										';
	}
	$__compilerTemp7 .= '
										' . '
										';
	if ($__templater->method($__vars['thread'], 'canUseInlineModeration', array())) {
		$__compilerTemp7 .= '
											<div class="menu-footer"
												data-xf-init="inline-mod"
												data-type="thread"
												data-href="' . $__templater->func('link', array('inline-mod', ), true) . '"
												data-toggle=".js-threadInlineModToggle">
												' . $__templater->formCheckBox(array(
		), array(array(
			'class' => 'js-threadInlineModToggle',
			'value' => $__vars['thread']['thread_id'],
			'label' => 'Select for moderation',
			'_type' => 'option',
		))) . '
											</div>
										';
	}
	$__compilerTemp7 .= '
										' . '
									';
	if (strlen(trim($__compilerTemp7)) > 0) {
		$__compilerTemp5 .= '
						<div class="buttonGroup-buttonWrapper">
							' . $__templater->button('&#8226;&#8226;&#8226;', array(
			'class' => 'button--link menuTrigger',
			'data-xf-click' => 'menu',
			'aria-expanded' => 'false',
			'aria-haspopup' => 'true',
			'title' => 'More options',
		), '', array(
		)) . '
							<div class="menu" data-menu="menu" aria-hidden="true">
								<div class="menu-content">
									<h4 class="menu-header">' . 'More options' . '</h4>
									' . $__compilerTemp7 . '
								</div>
							</div>
						</div>
					';
	}
	$__compilerTemp5 .= '
				';
	if (strlen(trim($__compilerTemp5)) > 0) {
		$__compilerTemp4 .= '
			<div class="block-outer-opposite">
				<div class="buttonGroup">
				' . $__compilerTemp5 . '
				</div>
			</div>
		';
	}
	$__finalCompiled .= trim('
		' . $__templater->func('page_nav', array(array(
		'page' => $__vars['page'],
		'total' => ($__vars['thread']['reply_count'] + 1),
		'link' => 'threads',
		'data' => $__vars['thread'],
		'wrapperclass' => 'block-outer-main',
		'perPage' => $__vars['perPage'],
	))) . '
		' . $__compilerTemp4 . '
	') . '</div>

	<div class="block-outer js-threadStatusField">';
	$__compilerTemp8 = '';
	$__compilerTemp9 = '';
	$__compilerTemp9 .= '
					' . $__templater->callMacro('custom_fields_macros', 'custom_fields_view', array(
		'type' => 'threads',
		'group' => 'thread_status',
		'onlyInclude' => $__vars['forum']['field_cache'],
		'set' => $__vars['thread']['custom_fields'],
		'wrapperClass' => 'blockStatus-message',
	), $__vars) . '
				';
	if (strlen(trim($__compilerTemp9)) > 0) {
		$__compilerTemp8 .= '
			<div class="blockStatus blockStatus--info">
				' . $__compilerTemp9 . '
			</div>
		';
	}
	$__finalCompiled .= trim('
		' . $__compilerTemp8 . '
	') . '</div>

	<div class="block-container lbContainer"
		data-xf-init="lightbox' . ($__vars['xf']['options']['selectQuotable'] ? ' select-to-quote' : '') . '"
		data-message-selector=".js-post"
		data-lb-id="thread-' . $__templater->escape($__vars['thread']['thread_id']) . '"
		data-lb-universal="' . $__templater->escape($__vars['xf']['options']['lightBoxUniversal']) . '">

		<div class="block-body js-replyNewMessageContainer">
			';
	if ($__templater->isTraversable($__vars['posts'])) {
		foreach ($__vars['posts'] AS $__vars['post']) {
			$__finalCompiled .= '
				';
			if ($__vars['post']['message_state'] == 'deleted') {
				$__finalCompiled .= '
					' . $__templater->callMacro('post_macros', 'post_deleted', array(
					'post' => $__vars['post'],
					'thread' => $__vars['thread'],
				), $__vars) . '
				';
			} else {
				$__finalCompiled .= '
					' . $__templater->callMacro('post_macros', 'post', array(
					'post' => $__vars['post'],
					'thread' => $__vars['thread'],
				), $__vars) . '
				';
			}
			$__finalCompiled .= '
			';
		}
	}
	$__finalCompiled .= '
		</div>
	</div>

	';
	$__compilerTemp10 = '';
	$__compilerTemp10 .= '
				' . $__templater->func('page_nav', array(array(
		'page' => $__vars['page'],
		'total' => ($__vars['thread']['reply_count'] + 1),
		'link' => 'threads',
		'data' => $__vars['thread'],
		'wrapperclass' => 'block-outer-main',
		'perPage' => $__vars['perPage'],
	))) . '
				' . $__templater->func('show_ignored', array(array(
		'wrapperclass' => 'block-outer-opposite',
	))) . '
				';
	if ((!$__templater->method($__vars['thread'], 'canReply', array())) AND (($__vars['thread']['discussion_state'] == 'visible') AND $__vars['thread']['discussion_open'])) {
		$__compilerTemp10 .= '
					<div class="block-outer-opposite">
						';
		if ($__vars['xf']['visitor']['user_id']) {
			$__compilerTemp10 .= '
							<span class="button is-disabled">
								' . 'You have insufficient privileges to reply here.' . '
								<!-- this is not interactive so shouldn\'t be a button element -->
							</span>
						';
		} else {
			$__compilerTemp10 .= '
							' . $__templater->button('
								' . 'You must log in or register to reply here.' . '
							', array(
				'href' => $__templater->func('link', array('login', ), false),
				'class' => 'button--link',
				'overlay' => 'true',
			), '', array(
			)) . '
						';
		}
		$__compilerTemp10 .= '
					</div>
				';
	}
	$__compilerTemp10 .= '
			';
	if (strlen(trim($__compilerTemp10)) > 0) {
		$__finalCompiled .= '
		<div class="block-outer block-outer--after">
			' . $__compilerTemp10 . '
		</div>
	';
	}
	$__finalCompiled .= '

	' . $__templater->callMacro(null, 'thread_status', array(
		'thread' => $__vars['thread'],
		'wrapperClass' => 'block-outer block-outer--after',
	), $__vars) . '
</div>

' . $__templater->callAdsMacro('thread_view_below_messages', array(
		'thread' => $__vars['thread'],
	), $__vars) . '

';
	if ($__templater->method($__vars['thread'], 'canReply', array())) {
		$__finalCompiled .= '
	';
		$__templater->includeJs(array(
			'src' => 'xf/message.js',
			'min' => '1',
		));
		$__vars['lastPost'] = $__templater->filter($__vars['posts'], array(array('last', array()),), false);
		$__finalCompiled .= $__templater->form('

		' . '' . '
		' . '' . '

		<div class="block-container">
			<div class="block-body">
				' . $__templater->callMacro('quick_reply_macros', 'body', array(
			'message' => $__vars['thread']['draft_reply']['message'],
			'attachmentData' => $__vars['attachmentData'],
			'forceHash' => $__vars['thread']['draft_reply']['attachment_hash'],
			'messageSelector' => '.js-post',
			'multiQuoteHref' => $__templater->func('link', array('threads/multi-quote', $__vars['thread'], ), false),
			'multiQuoteStorageKey' => 'multiQuoteThread',
			'lastDate' => $__vars['lastPost']['post_date'],
			'lastKnownDate' => $__vars['thread']['last_post_date'],
		), $__vars) . '
			</div>
		</div>
	', array(
			'action' => $__templater->func('link', array('threads/add-reply', $__vars['thread'], ), false),
			'ajax' => 'true',
			'draft' => $__templater->func('link', array('threads/draft', $__vars['thread'], ), false),
			'class' => 'block js-quickReply',
			'data-xf-init' => 'attachment-manager quick-reply' . ($__templater->method($__vars['xf']['visitor'], 'isShownCaptcha', array()) ? ' guest-captcha' : ''),
			'data-message-container' => 'div[data-type=\'post\'] .js-replyNewMessageContainer',
			'data-preview-url' => $__templater->func('link', array('threads/reply-preview', $__vars['thread'], array('quick_reply' => 1, ), ), false),
		)) . '
';
	}
	$__finalCompiled .= '

<div class="blockMessage blockMessage--none">
	' . $__templater->callMacro('share_page_macros', 'buttons', array(
		'iconic' => true,
		'label' => 'Share' . $__vars['xf']['language']['label_separator'],
	), $__vars) . '
</div>

' . '

';
	$__templater->modifySidebarHtml('_xfWidgetPositionSidebarThreadViewSidebar', $__templater->widgetPosition('thread_view_sidebar', array(
		'thread' => $__vars['thread'],
	)), 'replace');
	return $__finalCompiled;
});