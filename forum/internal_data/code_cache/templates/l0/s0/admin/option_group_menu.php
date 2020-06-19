<?php
// FROM HASH: ec49d645a06a0a6518fa8ec0d003ce4d
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<h3 class="menu-header">' . 'Options' . '</h3>
<ul class="listPlain listColumns listColumns--3 listColumns--together">
	';
	if ($__templater->isTraversable($__vars['groups'])) {
		foreach ($__vars['groups'] AS $__vars['groupId'] => $__vars['group']) {
			$__finalCompiled .= '
		<li>
			<a class="menu-linkRow" href="' . $__templater->func('link', array('options/groups', array('group_id' => $__vars['groupId'], ), ), true) . '">
				';
			if ($__vars['group']['icon']) {
				$__finalCompiled .= '
					' . $__templater->fontAwesome($__templater->escape($__vars['group']['icon']) . ' fa-fw', array(
				)) . '
				';
			} else {
				$__finalCompiled .= '
					' . $__templater->fontAwesome('fa-cogs fa-fw', array(
				)) . '
				';
			}
			$__finalCompiled .= '
				' . $__templater->escape($__vars['group']['title']) . '
			</a>
		</li>
	';
		}
	}
	$__finalCompiled .= '
</ul>';
	return $__finalCompiled;
});