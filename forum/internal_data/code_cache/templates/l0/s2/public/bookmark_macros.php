<?php
// FROM HASH: 2614586846d428d198d4d7b985dc2a9e
return array('macros' => array('link' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'content' => '!',
		'confirmUrl' => '!',
		'editText' => 'Edit bookmark',
		'addText' => 'Add bookmark',
		'showText' => true,
		'class' => 'actionBar-action actionBar-action--bookmarkLink',
	), $__arguments, $__vars);
	$__finalCompiled .= '

	';
	if ($__templater->method($__vars['content'], 'canBookmark', array())) {
		$__finalCompiled .= '
		<a href="' . $__templater->escape($__vars['confirmUrl']) . '" class="bookmarkLink ' . $__templater->escape($__vars['class']) . ' ' . ($__templater->method($__vars['content'], 'isBookmarked', array()) ? 'is-bookmarked' : '') . '"
			title="' . ($__vars['showText'] ? '' : $__templater->filter('Bookmark', array(array('for_attr', array()),), true)) . '"
			data-xf-click="bookmark-click"
			data-label=".js-bookmarkText"
			data-sk-bookmarked="addClass:is-bookmarked, ' . $__templater->filter($__vars['editText'], array(array('for_attr', array()),), true) . '"
			data-sk-bookmarkremoved="removeClass:is-bookmarked, ' . $__templater->filter($__vars['addText'], array(array('for_attr', array()),), true) . '">';
		$__compilerTemp1 = '';
		if ($__templater->method($__vars['content'], 'isBookmarked', array())) {
			$__compilerTemp1 .= $__templater->escape($__vars['editText']);
		} else {
			$__compilerTemp1 .= $__templater->escape($__vars['addText']);
		}
		$__finalCompiled .= trim('
			<span class="js-bookmarkText ' . ($__vars['showText'] ? '' : 'u-srOnly') . '">' . $__compilerTemp1 . '</span>
		') . '</a>
	';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},
'button' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'content' => '!',
		'confirmUrl' => '!',
		'class' => 'button--link',
		'showText' => false,
		'editText' => 'Edit bookmark',
		'addText' => 'Add bookmark',
	), $__arguments, $__vars);
	$__finalCompiled .= '

	';
	if ($__templater->method($__vars['content'], 'canBookmark', array())) {
		$__finalCompiled .= '
		';
		$__compilerTemp1 = '';
		if ($__templater->method($__vars['content'], 'isBookmarked', array())) {
			$__compilerTemp1 .= $__templater->escape($__vars['editText']);
		} else {
			$__compilerTemp1 .= $__templater->escape($__vars['addText']);
		}
		$__finalCompiled .= $__templater->button(trim('
			<span class="js-bookmarkText ' . ($__vars['showText'] ? '' : 'u-srOnly') . '">' . $__compilerTemp1 . '</span>
		'), array(
			'href' => $__vars['confirmUrl'],
			'icon' => 'bookmark',
			'class' => ($__vars['showText'] ? '' : 'button--iconOnly') . ' ' . $__vars['class'] . ' ' . ($__templater->method($__vars['content'], 'isBookmarked', array()) ? 'is-bookmarked' : ''),
			'title' => ($__vars['showText'] ? '' : 'Bookmark'),
			'data-xf-click' => 'bookmark-click',
			'data-label' => '.js-bookmarkText',
			'data-sk-bookmarked' => 'addClass:is-bookmarked, ' . $__vars['editText'],
			'data-sk-bookmarkremoved' => 'removeClass:is-bookmarked, ' . $__vars['addText'],
		), '', array(
		)) . '
	';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},
'row' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'bookmark' => '!',
		'content' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '

	<div class="contentRow">
		';
	if ($__vars['bookmark']['has_custom_icon']) {
		$__finalCompiled .= '
			' . $__templater->filter($__templater->method($__vars['bookmark'], 'renderCustomIcon', array()), array(array('raw', array()),), true) . '
		';
	} else if ($__vars['bookmark']['content_user']) {
		$__finalCompiled .= '
			<div class="contentRow-figure contentRow-figure--fixedBookmarkIcon">
				' . $__templater->func('avatar', array($__vars['bookmark']['content_user'], 's', false, array(
			'href' => '',
			'defaultname' => $__vars['bookmark']['content_user']['username'],
		))) . '
			</div>
		';
	}
	$__finalCompiled .= '

		<div class="contentRow-main">
			' . $__templater->callMacro(null, 'item_buttons', array(
		'bookmark' => $__vars['bookmark'],
	), $__vars) . '

			<div class="contentRow-title">
				<a href="' . $__templater->escape($__vars['bookmark']['content_link']) . '">' . $__templater->escape($__vars['bookmark']['content_title']) . '</a>
			</div>

			<div class="contentRow-snippet">
				';
	if ($__vars['bookmark']['message']) {
		$__finalCompiled .= '
					' . $__templater->func('structured_text', array($__vars['bookmark']['message'], ), true) . '
				';
	} else {
		$__finalCompiled .= '
					';
		$__compilerTemp1 = '';
		$__compilerTemp1 .= '
							' . $__templater->filter($__templater->method($__vars['bookmark'], 'renderMessageFallback', array()), array(array('raw', array()),), true) . '
						';
		if (strlen(trim($__compilerTemp1)) > 0) {
			$__finalCompiled .= '
						' . $__compilerTemp1 . '
					';
		} else {
			$__finalCompiled .= '
						' . 'No bookmark note.' . '
					';
		}
		$__finalCompiled .= '
				';
	}
	$__finalCompiled .= '
			</div>

			' . $__templater->callMacro(null, 'item_footer', array(
		'bookmark' => $__vars['bookmark'],
		'content' => $__vars['content'],
	), $__vars) . '
		</div>
	</div>
';
	return $__finalCompiled;
},
'item_buttons' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'bookmark' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '

	<div class="contentRow-extra">
		' . $__templater->button('
			' . $__templater->fontAwesome('fa-cog', array(
	)) . '
		', array(
		'class' => 'button--link button--smaller menuTrigger',
		'data-xf-click' => 'menu',
		'aria-label' => 'More options',
		'aria-expanded' => 'false',
		'aria-haspopup' => 'true',
	), '', array(
	)) . '

		<div class="menu" data-menu="menu" aria-hidden="true">
			<div class="menu-content">
				<h3 class="menu-header">' . 'Bookmark tools' . '</h3>
				<a class="menu-linkRow" role="button" tabindex="0" data-menu-closer="on" data-xf-init="copy-to-clipboard" data-copy-text="' . $__templater->escape($__vars['bookmark']['content_link']) . '">' . 'Copy link' . '</a>
				<a href="' . $__templater->escape($__vars['bookmark']['edit_link']) . '" class="menu-linkRow" data-xf-click="overlay">' . 'Edit' . '</a>
				<a href="' . $__templater->escape($__vars['bookmark']['delete_link']) . '" class="menu-linkRow" data-xf-click="overlay">' . 'Delete' . '</a>
			</div>
		</div>
	</div>
';
	return $__finalCompiled;
},
'item_footer' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'bookmark' => '!',
		'content' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	<div class="contentRow-minor contentRow-minor--hideLinks contentRow-minor--smaller">
		<ul class="listInline listInline--bullet">
			';
	if ($__vars['bookmark']['content_user'] OR $__templater->method($__vars['content'], 'isValidKey', array('username', ))) {
		$__finalCompiled .= '
				<li>
					' . $__templater->fontAwesome('fa-user', array(
			'title' => 'User',
		)) . '
					<span class="u-srOnly">' . 'User' . '</span>
					' . $__templater->func('username_link', array($__vars['bookmark']['content_user'], false, array(
			'defaultname' => ($__templater->method($__vars['content'], 'isValidKey', array('username', )) ? $__vars['content']['username'] : ''),
		))) . '
				</li>
			';
	}
	$__finalCompiled .= '
			<li>
				' . $__templater->fontAwesome('fa-clock', array(
		'title' => 'Bookmarked',
	)) . '
				<span class="u-srOnly">' . 'Bookmarked' . '</span>
				' . $__templater->func('date_dynamic', array($__vars['bookmark']['bookmark_date'], array(
	))) . '
			</li>
			';
	if ($__vars['bookmark']['labels']) {
		$__finalCompiled .= '
				<li class="tagList">
					<span class="u-srOnly">' . 'Labels' . '</span>
					';
		if ($__templater->isTraversable($__vars['bookmark']['labels'])) {
			foreach ($__vars['bookmark']['labels'] AS $__vars['label']) {
				$__finalCompiled .= '
						<a href="' . $__templater->func('link', array('account/bookmarks', null, array('label' => $__vars['label']['label'], ), ), true) . '" class="tagItem" dir="auto">' . $__templater->escape($__vars['label']['label']) . '</a>
					';
			}
		}
		$__finalCompiled .= '
				</li>
			';
	}
	$__finalCompiled .= '
		</ul>
	</div>
';
	return $__finalCompiled;
},
'filter' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'label' => $__vars['label'],
		'allLabels' => array(),
		'name' => 'labels',
		'minLength' => '0',
		'maxTokens' => '1',
		'placeholder' => 'Filter by label' . $__vars['xf']['language']['ellipsis'],
	), $__arguments, $__vars);
	$__finalCompiled .= '
	' . $__templater->formTokenInput(array(
		'inputclass' => 'js-labelFilter',
		'name' => $__vars['name'],
		'value' => $__vars['label'],
		'min-length' => $__vars['minLength'],
		'max-tokens' => $__vars['maxTokens'],
		'list-data' => $__templater->filter($__vars['allLabels'], array(array('json', array()),), false),
		'placeholder' => $__vars['placeholder'],
	)) . '
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '

' . '

' . '

' . '

' . '

';
	return $__finalCompiled;
});