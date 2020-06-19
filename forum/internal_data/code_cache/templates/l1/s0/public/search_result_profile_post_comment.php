<?php
// FROM HASH: e95af420f694e9907cbb03bddf016a4b
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<li class="block-row block-row--separated ' . ($__templater->method($__vars['comment'], 'isIgnored', array()) ? 'is-ignored' : '') . '" data-author="' . ($__templater->escape($__vars['comment']['User']['username']) ?: $__templater->escape($__vars['comment']['username'])) . '">
	<div class="contentRow ' . ((!$__templater->method($__vars['comment'], 'isVisible', array())) ? 'is-deleted' : '') . '">
		<span class="contentRow-figure">
			' . $__templater->func('avatar', array($__vars['comment']['User'], 's', false, array(
		'defaultname' => $__vars['comment']['username'],
	))) . '
		</span>
		<div class="contentRow-main">
			<h3 class="contentRow-title">
				<a href="' . $__templater->func('link', array('profile-posts/comments', $__vars['comment'], ), true) . '">' . $__templater->func('snippet', array($__vars['comment']['message'], 100, array('term' => $__vars['options']['term'], 'stripQuote' => true, 'fromStart' => true, 'hideUnviewable' => false, ), ), true) . '</a>
			</h3>

			<div class="contentRow-snippet">' . $__templater->func('snippet', array($__vars['comment']['message'], 300, array('term' => $__vars['options']['term'], 'stripQuote' => true, ), ), true) . '</div>

			<div class="contentRow-minor contentRow-minor--hideLinks">
				<ul class="listInline listInline--bullet">
					<li>' . $__templater->func('username_link', array($__vars['comment']['User'], false, array(
		'defaultname' => $__vars['comment']['username'],
	))) . '</li>
					<li>' . 'Profile post comment' . '</li>
					<li>' . $__templater->func('date_dynamic', array($__vars['comment']['comment_date'], array(
	))) . '</li>
				</ul>
			</div>
		</div>
	</div>
</li>';
	return $__finalCompiled;
});