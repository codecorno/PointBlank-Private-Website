<?php
// FROM HASH: 759bead01d14ad2568fd206e857241dc
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->func('count', array($__vars['smiliesInfo']['smilies'], ), false) > 1) {
		$__finalCompiled .= '
	<div>
		<h3 class="tabs tabs--editor hScroller" data-xf-init="tabs h-scroller" role="tablist">
			<span class="hScroller-scroll">
			';
		$__vars['i'] = 0;
		if ($__templater->isTraversable($__vars['smiliesInfo']['smilieCategories'])) {
			foreach ($__vars['smiliesInfo']['smilieCategories'] AS $__vars['categoryId'] => $__vars['category']) {
				if (!$__templater->test($__vars['smiliesInfo']['smilies'][$__vars['categoryId']], 'empty', array())) {
					$__vars['i']++;
					$__finalCompiled .= '

				<a class="tabs-tab ' . (($__vars['i'] == 1) ? 'is-active' : '') . '"
					role="tab" tabindex="0" aria-controls="' . $__templater->func('unique_id', array('smilies' . $__vars['i'], ), true) . '">' . $__templater->escape($__vars['category']['title']) . '</a>
			';
				}
			}
		}
		$__finalCompiled .= '
			</span>
		</h3>

		<ul class="tabPanes is-structureList">
			';
		$__vars['i'] = 0;
		if ($__templater->isTraversable($__vars['smiliesInfo']['smilieCategories'])) {
			foreach ($__vars['smiliesInfo']['smilieCategories'] AS $__vars['categoryId'] => $__vars['category']) {
				if (!$__templater->test($__vars['smiliesInfo']['smilies'][$__vars['categoryId']], 'empty', array())) {
					$__vars['i']++;
					$__finalCompiled .= '

				<li class="' . (($__vars['i'] == 1) ? 'is-active' : '') . '" role="tabpanel" id="' . $__templater->func('unique_id', array('smilies' . $__vars['i'], ), true) . '">
				';
					if ($__templater->isTraversable($__vars['smiliesInfo']['smilies'][$__vars['categoryId']])) {
						foreach ($__vars['smiliesInfo']['smilies'][$__vars['categoryId']] AS $__vars['smilie']) {
							$__finalCompiled .= '
					' . $__templater->func('smilie', array($__vars['smilie']['smilie_text_options']['0'], ), true) . '
				';
						}
					}
					$__finalCompiled .= '
				</li>
			';
				}
			}
		}
		$__finalCompiled .= '
		</ul>
	</div>
';
	} else {
		$__finalCompiled .= '
	<div class="block-body block-row">
	';
		$__vars['i'] = 0;
		if ($__templater->isTraversable($__vars['smiliesInfo']['smilieCategories'])) {
			foreach ($__vars['smiliesInfo']['smilieCategories'] AS $__vars['categoryId'] => $__vars['category']) {
				if (!$__templater->test($__vars['smiliesInfo']['smilies'][$__vars['categoryId']], 'empty', array())) {
					$__vars['i']++;
					$__finalCompiled .= '

		';
					if ($__templater->isTraversable($__vars['smiliesInfo']['smilies'][$__vars['categoryId']])) {
						foreach ($__vars['smiliesInfo']['smilies'][$__vars['categoryId']] AS $__vars['smilie']) {
							$__finalCompiled .= '
			' . $__templater->func('smilie', array($__vars['smilie']['smilie_text_options']['0'], ), true) . '
		';
						}
					}
					$__finalCompiled .= '
	';
				}
			}
		}
		$__finalCompiled .= '
	</div>
';
	}
	return $__finalCompiled;
});