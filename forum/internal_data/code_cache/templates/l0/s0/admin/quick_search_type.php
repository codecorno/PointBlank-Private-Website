<?php
// FROM HASH: 4bbd6a5f2e47bebea92f03a76c077dad
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<dl class="p-quickSearchResultSet">
	<dt>' . $__templater->escape($__vars['typeName']) . '</dt>
	<dd>
		<ul class="p-quickSearchResultList">
		';
	if ($__templater->isTraversable($__vars['results'])) {
		foreach ($__vars['results'] AS $__vars['result']) {
			$__finalCompiled .= '
			<li>
				<a href="' . $__templater->escape($__vars['result']['link']) . '">
					' . $__templater->escape($__vars['result']['title']) . '
					';
			if (!$__templater->test($__vars['result']['extra'], 'empty', array())) {
				$__finalCompiled .= '<span>' . $__templater->escape($__vars['result']['extra']) . '</span>';
			}
			$__finalCompiled .= '
				</a>
			</li>
		';
		}
	}
	$__finalCompiled .= '
		</ul>
	</dd>
</dl>';
	return $__finalCompiled;
});