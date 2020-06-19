<?php
// FROM HASH: 14abd284cf7744830a70a2761bdd7200
return array('macros' => array('notice_list' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'type' => '!',
		'notices' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	if (!$__templater->test($__vars['notices'], 'empty', array())) {
		$__finalCompiled .= '
		';
		$__templater->includeCss('notices.less');
		$__finalCompiled .= '
		';
		$__templater->includeJs(array(
			'src' => 'xf/notice.js',
			'min' => '1',
		));
		$__finalCompiled .= '
		';
		if ($__vars['type'] == 'scrolling') {
			$__finalCompiled .= '
			';
			$__templater->includeCss('lightslider.less');
			$__finalCompiled .= '
			';
			$__templater->includeJs(array(
				'prod' => 'xf/carousel-compiled.js',
				'dev' => 'vendor/lightslider/lightslider.min.js, xf/carousel.js',
			));
			$__finalCompiled .= '
		';
		}
		$__finalCompiled .= '

		<ul class="notices notices--' . $__templater->escape($__vars['type']) . ' ' . (($__templater->func('count', array($__vars['notices'], ), false) > 1) ? 'notices--isMulti' : '') . ' js-notices"
			data-xf-init="notices"
			data-type="' . $__templater->escape($__vars['type']) . '"
			data-scroll-interval="' . $__templater->func('property', array('noticeScrollInterval', ), true) . '">

			';
		if ($__templater->isTraversable($__vars['notices'])) {
			foreach ($__vars['notices'] AS $__vars['notice']) {
				$__finalCompiled .= '
				' . $__templater->callMacro(null, 'notice', array(
					'notice' => $__vars['notice'],
					'type' => $__vars['type'],
					'contentHtml' => $__vars['notice']['message'],
				), $__vars) . '
			';
			}
		}
		$__finalCompiled .= '
		</ul>
	';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},
'notice' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'notice' => '!',
		'type' => '!',
		'contentHtml' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	<li class="notice js-notice ' . (($__vars['notice']['display_style'] == 'custom') ? $__templater->escape($__vars['notice']['css_class']) : ('notice--' . $__templater->escape($__vars['notice']['display_style']))) . ($__vars['notice']['display_image'] ? ' notice--hasImage' : '') . ($__vars['notice']['visibility'] ? (' notice--hide' . $__templater->escape($__vars['notice']['visibility'])) : '') . '"
		data-notice-id="' . $__templater->escape($__vars['notice']['notice_id']) . '"
		data-delay-duration="' . $__templater->escape($__vars['notice']['delay_duration']) . '"
		data-display-duration="' . $__templater->escape($__vars['notice']['display_duration']) . '"
		data-auto-dismiss="' . $__templater->escape($__vars['notice']['auto_dismiss']) . '"
		data-visibility="' . $__templater->escape($__vars['notice']['visibility']) . '">

		';
	if ($__vars['notice']['display_image'] == 'avatar') {
		$__finalCompiled .= '
			<div class="notice-image">' . $__templater->func('avatar', array($__vars['xf']['visitor'], 's', false, array(
			'href' => '',
		))) . '</div>
		';
	} else if ($__vars['notice']['display_image'] == 'image') {
		$__finalCompiled .= '
			<div class="notice-image"><img src="' . $__templater->escape($__vars['notice']['image_url']) . '" alt="" /></div>
		';
	}
	$__finalCompiled .= '
		<div class="notice-content">
			';
	if ($__vars['notice']['dismissible'] AND (!$__vars['notice']['custom_dismissible'])) {
		$__finalCompiled .= '
				<a href="' . $__templater->func('link', array('account/dismiss-notice', null, array('notice_id' => $__vars['notice']['notice_id'], ), ), true) . '" class="notice-dismiss js-noticeDismiss" data-xf-init="tooltip" title="' . $__templater->filter('Dismiss notice', array(array('for_attr', array()),), true) . '"></a>
			';
	}
	$__finalCompiled .= '
			' . $__templater->filter($__vars['contentHtml'], array(array('raw', array()),), true) . '
		</div>
	</li>
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '

';
	return $__finalCompiled;
});