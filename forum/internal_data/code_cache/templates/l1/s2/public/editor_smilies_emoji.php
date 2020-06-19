<?php
// FROM HASH: 168412715c3f0715fc0fcee51182cf05
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<div class="menu-row menu-row--highlighted menu-row--insertedMessage js-emojiInsertedRow">
	<div class="menu-emojiInsertedMessage">
		' . 'Inserted' . $__vars['xf']['language']['label_separator'] . ' <span class="js-emojiInsert"></span>
	</div>
</div>
<div class="menu-row menu-row--alt menu-row--close">
	<div class="inputGroup">
		' . $__templater->formTextBox(array(
		'class' => 'js-emojiSearch',
		'placeholder' => 'Search' . $__vars['xf']['language']['ellipsis'],
		'data-no-auto-focus' => 'true',
	)) . '
		' . $__templater->button('
			<span class="u-srOnly">' . 'Close' . '</span>
		', array(
		'icon' => 'close',
		'class' => 'button--iconOnly button--plain js-emojiCloser',
		'title' => 'Close',
		'data-no-auto-focus' => 'true',
		'data-menu-closer' => 'true',
	), '', array(
	)) . '
	</div>
</div>
<div class="menu-scroller js-emojiFullList">
	<h3 class="menu-header js-recentHeader ' . ((!$__vars['recent']) ? 'is-hidden' : '') . '">' . 'Recently used' . '</h3>
	<div class="menu-row js-recentBlock ' . ((!$__vars['recent']) ? 'is-hidden' : '') . '">
		<ul class="emojiList js-recentList">
			';
	if ($__templater->isTraversable($__vars['recent'])) {
		foreach ($__vars['recent'] AS $__vars['shortname'] => $__vars['emoji']) {
			$__finalCompiled .= '
				<li><a class="js-emoji" data-shortname="' . $__templater->escape($__vars['shortname']) . '">
					';
			if ($__vars['emoji']['smilie_id']) {
				$__finalCompiled .= '
						' . $__templater->func('smilie', array($__vars['shortname'], ), true) . '
					';
			} else {
				$__finalCompiled .= '
						' . $__templater->filter($__vars['emoji']['html'], array(array('raw', array()),), true) . '
					';
			}
			$__finalCompiled .= '
				</a></li>
			';
		}
	}
	$__finalCompiled .= '
		</ul>
	</div>
	';
	if ($__templater->isTraversable($__vars['smilieCategories'])) {
		foreach ($__vars['smilieCategories'] AS $__vars['categoryId'] => $__vars['category']) {
			$__finalCompiled .= '
		';
			$__compilerTemp1 = '';
			$__compilerTemp1 .= '
						';
			if ($__templater->isTraversable($__vars['groupedSmilies'][$__vars['categoryId']])) {
				foreach ($__vars['groupedSmilies'][$__vars['categoryId']] AS $__vars['smilie']) {
					$__compilerTemp1 .= '
							<li><a class="js-emoji" data-shortname="' . $__templater->escape($__vars['smilie']['smilie_text_options']['0']) . '">' . $__templater->func('smilie', array($__vars['smilie']['smilie_text_options']['0'], ), true) . '</a></li>
						';
				}
			}
			$__compilerTemp1 .= '
					';
			if (strlen(trim($__compilerTemp1)) > 0) {
				$__finalCompiled .= '
			<h3 class="menu-header">' . ($__vars['categoryId'] ? $__templater->escape($__vars['category']['title']) : 'Smilies') . '</h3>
			<div class="menu-row">
				<ul class="emojiList js-emojiList">
					' . $__compilerTemp1 . '
				</ul>
			</div>
		';
			}
			$__finalCompiled .= '
	';
		}
	}
	$__finalCompiled .= '
	';
	if ($__templater->isTraversable($__vars['emojiCategories'])) {
		foreach ($__vars['emojiCategories'] AS $__vars['categoryId'] => $__vars['name']) {
			$__finalCompiled .= '
		<h3 class="menu-header">' . $__templater->escape($__vars['name']) . '</h3>
		<div class="menu-row">
			<ul class="emojiList js-emojiList">
				';
			if ($__templater->isTraversable($__vars['groupedEmoji'][$__vars['categoryId']])) {
				foreach ($__vars['groupedEmoji'][$__vars['categoryId']] AS $__vars['emoji']) {
					$__finalCompiled .= '
					<li><a class="js-emoji" data-shortname="' . $__templater->escape($__vars['emoji']['shortname']) . '">' . $__templater->filter($__vars['emoji']['html'], array(array('raw', array()),), true) . '</a></li>
				';
				}
			}
			$__finalCompiled .= '
			</ul>
		</div>
	';
		}
	}
	$__finalCompiled .= '
</div>
<div class="menu-scroller js-emojiSearchResults" style="display: none;"></div>';
	return $__finalCompiled;
});