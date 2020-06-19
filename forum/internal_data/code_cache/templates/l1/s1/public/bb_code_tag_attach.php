<?php
// FROM HASH: c898076ad666ad3178429b7c9757f74a
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if (!$__vars['attachment']) {
		$__finalCompiled .= trim('
	<a href="' . $__templater->func('link', array('full:attachments', array('attachment_id' => $__vars['id'], ), ), true) . '" target="_blank">' . 'View attachment ' . $__templater->escape($__vars['id']) . '' . '</a>
');
	} else if ($__vars['attachment']['is_video'] AND ($__vars['full'] AND $__vars['canView'])) {
		$__finalCompiled .= trim('
	<div class="bbMediaWrapper ' . $__templater->escape($__vars['alignClass']) . '">
		<div class="bbMediaWrapper-inner">
			<video controls data-xf-init="video-init"
				style="' . $__templater->escape($__vars['styleAttr']) . '">
				<source src="' . $__templater->func('base_url', array($__vars['attachment']['video_url'], ), true) . '" />
				<div class="bbMediaWrapper-fallback">' . 'Your browser is not able to display this video.' . '</div>
			</video>
		</div>
	</div>
');
	} else if (!$__vars['attachment']['has_thumbnail']) {
		$__finalCompiled .= trim('
	<a href="' . $__templater->func('link', array('full:attachments', $__vars['attachment'], array('hash' => $__vars['attachment']['temp_hash'], ), ), true) . '" target="_blank">' . 'View attachment ' . $__templater->escape($__vars['attachment']['filename']) . '' . '</a>
');
	} else if ($__vars['canView'] AND $__vars['full']) {
		$__compilerTemp1 = '';
		if ($__vars['noLightbox']) {
			$__compilerTemp1 .= '
		<img src="' . $__templater->func('link', array('full:attachments', $__vars['attachment'], array('hash' => $__vars['attachment']['temp_hash'], ), ), true) . '"
			class="bbImage ' . $__templater->escape($__vars['alignClass']) . '"
			style="' . $__templater->escape($__vars['styleAttr']) . '"
			alt="' . $__templater->escape($__vars['alt']) . '" />
		';
		} else {
			$__compilerTemp1 .= '
		' . $__templater->callMacro('lightbox_macros', 'single_image', array(
				'canViewAttachments' => $__vars['canView'],
				'id' => 'attachment' . $__vars['attachment']['attachment_id'],
				'src' => $__templater->func('link', array('full:attachments', $__vars['attachment'], array('hash' => $__vars['attachment']['temp_hash'], ), ), false),
				'alt' => $__vars['alt'],
				'styleAttr' => $__vars['styleAttr'],
				'alignClass' => $__vars['alignClass'],
			), $__vars) . '
	';
		}
		$__finalCompiled .= trim('
	' . $__compilerTemp1 . '
');
	} else if ($__vars['canView']) {
		$__finalCompiled .= trim('
	' . $__templater->callMacro('lightbox_macros', 'setup', array(
			'canViewAttachments' => $__vars['canView'],
		), $__vars) . '
	<a href="' . $__templater->func('link', array('full:attachments', $__vars['attachment'], array('hash' => $__vars['attachment']['temp_hash'], ), ), true) . '"
		target="_blank" class="js-lbImage"><img src="' . $__templater->escape($__vars['attachment']['thumbnail_url']) . '"
		class="bbImage ' . $__templater->escape($__vars['alignClass']) . '"
		style="' . $__templater->escape($__vars['styleAttr']) . '"
		alt="' . $__templater->escape($__vars['alt']) . '" /></a>
');
	} else {
		$__finalCompiled .= trim('
	<a href="' . $__templater->func('link', array('full:attachments', $__vars['attachment'], array('hash' => $__vars['attachment']['temp_hash'], ), ), true) . '"
		target="_blank"><img src="' . $__templater->escape($__vars['attachment']['thumbnail_url']) . '"
		class="bbImage ' . $__templater->escape($__vars['alignClass']) . '"
		style="' . $__templater->escape($__vars['styleAttr']) . '"
		alt="' . $__templater->escape($__vars['alt']) . '" /></a>
');
	}
	return $__finalCompiled;
});