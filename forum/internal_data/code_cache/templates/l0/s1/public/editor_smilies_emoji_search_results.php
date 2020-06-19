<?php
// FROM HASH: 610484a8da7999d6392d97bb25bb24bf
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<div class="block-body block-body--emoji js-emojiSearchResults">
	<div class="block-row">
		';
	if ($__vars['results']) {
		$__finalCompiled .= '
			<ul class="emojiList">
				';
		if ($__templater->isTraversable($__vars['results'])) {
			foreach ($__vars['results'] AS $__vars['result']) {
				$__finalCompiled .= '
					<li><a class="js-emoji" data-shortname="' . $__templater->escape($__vars['result']['shortname']) . '">' . $__templater->filter($__vars['result']['html'], array(array('raw', array()),), true) . '</a></li>
				';
			}
		}
		$__finalCompiled .= '
			</ul>
		';
	} else {
		$__finalCompiled .= '
			' . 'No results found.' . '
		';
	}
	$__finalCompiled .= '
	</div>
</div>';
	return $__finalCompiled;
});