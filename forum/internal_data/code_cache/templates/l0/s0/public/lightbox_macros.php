<?php
// FROM HASH: 9de001bf1bb08047496a2911b9218e38
return array('macros' => array('setup' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'canViewAttachments' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	if ($__vars['canViewAttachments']) {
		$__finalCompiled .= '
		';
		$__templater->includeCss('lightbox.less');
		$__finalCompiled .= '
		';
		$__templater->includeJs(array(
			'prod' => 'xf/lightbox-compiled.js',
			'dev' => 'vendor/lightgallery/lightgallery-all.min.js, xf/lightbox.js',
		));
		$__finalCompiled .= '
	';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},
'single_image' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'canViewAttachments' => '!',
		'id' => '!',
		'src' => '!',
		'dataUrl' => '',
		'containerZoom' => '1',
		'alt' => '',
		'styleAttr' => '',
		'alignClass' => '',
	), $__arguments, $__vars);
	$__finalCompiled .= '

	' . $__templater->callMacro(null, 'setup', array(
		'canViewAttachments' => $__vars['canViewAttachments'],
	), $__vars) . '

	<div class="lbContainer lbContainer--inline ' . $__templater->escape($__vars['alignClass']) . '" title="' . $__templater->filter($__vars['alt'], array(array('for_attr', array()),), true) . '"
		data-xf-init="lightbox"
		data-lb-single-image="1"
		data-lb-container-zoom="' . $__templater->escape($__vars['containerZoom']) . '"
		data-lb-trigger=".js-lbImage-' . $__templater->escape($__vars['id']) . '"
		data-lb-id="' . $__templater->escape($__vars['id']) . '">
		';
	if ($__vars['containerZoom']) {
		$__finalCompiled .= '
			<div class="lbContainer-zoomer js-lbImage-' . $__templater->escape($__vars['id']) . '" data-src="' . $__templater->escape($__vars['src']) . '" aria-label="' . $__templater->filter('Zoom', array(array('for_attr', array()),), true) . '"></div>
		';
	}
	$__finalCompiled .= '
		<img src="' . $__templater->escape($__vars['src']) . '" data-url="' . $__templater->escape($__vars['dataUrl']) . '" class="bbImage" data-zoom-target="1" alt="' . $__templater->filter($__vars['alt'], array(array('for_attr', array()),), true) . '" style="' . $__templater->escape($__vars['styleAttr']) . '" />
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