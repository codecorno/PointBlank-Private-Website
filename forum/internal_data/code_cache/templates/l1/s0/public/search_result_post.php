<?php
// FROM HASH: c2a8cb78ba593d0bb40cf36f08fe1e7a
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<li class="block-row block-row--separated ' . ($__templater->method($__vars['post'], 'isIgnored', array()) ? 'is-ignored' : '') . ' js-inlineModContainer" data-author="' . ($__templater->escape($__vars['post']['User']['username']) ?: $__templater->escape($__vars['post']['username'])) . '">
	<div class="contentRow ' . ((!$__templater->method($__vars['post'], 'isVisible', array())) ? 'is-deleted' : '') . '">
		<span class="contentRow-figure">
			' . $__templater->func('avatar', array($__vars['post']['User'], 's', false, array(
		'defaultname' => $__vars['post']['username'],
	))) . '
		</span>
		<div class="contentRow-main">
			<h3 class="contentRow-title">
				<a href="' . $__templater->func('link', array('threads/post', $__vars['post']['Thread'], array('post_id' => $__vars['post']['post_id'], ), ), true) . '">' . ($__templater->func('prefix', array('thread', $__vars['post']['Thread'], ), true) . $__templater->func('highlight', array($__vars['post']['Thread']['title'], $__vars['options']['term'], ), true)) . '</a>
			</h3>

			<div class="contentRow-snippet">' . $__templater->func('snippet', array($__vars['post']['message'], 300, array('term' => $__vars['options']['term'], 'stripQuote' => true, ), ), true) . '</div>

			<div class="contentRow-minor contentRow-minor--hideLinks">
				<ul class="listInline listInline--bullet">
					';
	if (($__vars['options']['mod'] == 'post') AND $__templater->method($__vars['post'], 'canUseInlineModeration', array())) {
		$__finalCompiled .= '
						<li>' . $__templater->formCheckBox(array(
			'standalone' => 'true',
		), array(array(
			'value' => $__vars['post']['post_id'],
			'class' => 'js-inlineModToggle',
			'data-xf-init' => 'tooltip',
			'title' => 'Select for moderation',
			'_type' => 'option',
		))) . '</li>
					';
	}
	$__finalCompiled .= '
					<li>' . $__templater->func('username_link', array($__vars['post']['User'], false, array(
		'defaultname' => $__vars['post']['username'],
	))) . '</li>
					<li>' . 'Post #' . $__templater->func('number', array($__vars['post']['position'] + 1, ), true) . '' . '</li>
					<li>' . $__templater->func('date_dynamic', array($__vars['post']['post_date'], array(
	))) . '</li>
					<li>' . 'Forum' . $__vars['xf']['language']['label_separator'] . ' <a href="' . $__templater->func('link', array('forums', $__vars['post']['Thread']['Forum'], ), true) . '">' . $__templater->escape($__vars['post']['Thread']['Forum']['title']) . '</a></li>
				</ul>
			</div>
		</div>
	</div>
</li>';
	return $__finalCompiled;
});