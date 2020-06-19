<?php
// FROM HASH: c8af51d8cd81fbfbd130344b8f7d93f8
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<li class="block-row block-row--separated ' . ($__templater->method($__vars['profilePost'], 'isIgnored', array()) ? 'is-ignored' : '') . ' js-inlineModContainer" data-author="' . ($__templater->escape($__vars['profilePost']['User']['username']) ?: $__templater->escape($__vars['profilePost']['username'])) . '">
	<div class="contentRow ' . ((!$__templater->method($__vars['profilePost'], 'isVisible', array())) ? 'is-deleted' : '') . '">
		<span class="contentRow-figure">
			' . $__templater->func('avatar', array($__vars['profilePost']['User'], 's', false, array(
		'defaultname' => $__vars['profilePost']['username'],
	))) . '
		</span>
		<div class="contentRow-main">
			<h3 class="contentRow-title">
				<a href="' . $__templater->func('link', array('profile-posts', $__vars['profilePost'], ), true) . '">' . $__templater->func('snippet', array($__vars['profilePost']['message'], 100, array('term' => $__vars['options']['term'], 'stripQuote' => true, 'fromStart' => true, 'hideUnviewable' => false, ), ), true) . '</a>
			</h3>

			<div class="contentRow-snippet">' . $__templater->func('snippet', array($__vars['profilePost']['message'], 300, array('term' => $__vars['options']['term'], 'stripQuote' => true, ), ), true) . '</div>

			<div class="contentRow-minor contentRow-minor--hideLinks">
				<ul class="listInline listInline--bullet">
					';
	if (($__vars['options']['mod'] == 'profile_post') AND $__templater->method($__vars['profilePost'], 'canUseInlineModeration', array())) {
		$__finalCompiled .= '
						<li>' . $__templater->formCheckBox(array(
			'standalone' => 'true',
		), array(array(
			'value' => $__vars['profilePost']['profile_post_id'],
			'class' => 'js-inlineModToggle',
			'data-xf-init' => 'tooltip',
			'title' => 'Select for moderation',
			'_type' => 'option',
		))) . '</li>
					';
	}
	$__finalCompiled .= '
					<li>' . $__templater->func('username_link', array($__vars['profilePost']['User'], false, array(
		'defaultname' => $__vars['profilePost']['username'],
	))) . '</li>
					<li>' . 'Profile post' . '</li>
					<li>' . $__templater->func('date_dynamic', array($__vars['profilePost']['post_date'], array(
	))) . '</li>
				</ul>
			</div>
		</div>
	</div>
</li>';
	return $__finalCompiled;
});