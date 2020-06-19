<?php
// FROM HASH: 949cf0034c56f1b97994a53a693c3456
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Administrators');
	$__finalCompiled .= '

';
	$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('Add administrator', array(
		'href' => $__templater->func('link', array('admins/add', ), false),
		'icon' => 'add',
	), '', array(
	)) . '
');
	$__finalCompiled .= '

<div class="block">
	<div class="block-container">
		<div class="block-body">
			';
	$__compilerTemp1 = '';
	if ($__templater->isTraversable($__vars['admins'])) {
		foreach ($__vars['admins'] AS $__vars['admin']) {
			$__compilerTemp1 .= '
					';
			$__compilerTemp2 = '';
			if ($__vars['admin']['is_super_admin']) {
				$__compilerTemp2 .= '
										<li>' . 'Super administrator' . '</li>
									';
			}
			$__compilerTemp1 .= $__templater->dataRow(array(
			), array(array(
				'class' => 'dataList-cell--min dataList-cell--image dataList-cell--imageSmall',
				'href' => $__templater->func('link', array('admins/edit', $__vars['admin'], ), false),
				'_type' => 'cell',
				'html' => '
							' . $__templater->func('avatar', array($__vars['admin']['User'], 's', false, array(
				'href' => '',
			))) . '
						',
			),
			array(
				'href' => $__templater->func('link', array('admins/edit', $__vars['admin'], ), false),
				'label' => $__templater->escape($__vars['admin']['User']['username']),
				'hint' => '
								<ul class="listInline listInline--bullet listInline--selfInline">
									<li>' . 'Last login' . $__vars['xf']['language']['label_separator'] . ' ' . ($__vars['admin']['last_login'] ? $__templater->func('date_dynamic', array($__vars['admin']['last_login'], ), true) : 'N/A') . '</li>
									' . $__compilerTemp2 . '
								</ul>
							',
				'_type' => 'main',
				'html' => '',
			),
			array(
				'href' => $__templater->func('link', array('users/edit', $__vars['admin']['User'], ), false),
				'_type' => 'action',
				'html' => 'User info',
			),
			array(
				'href' => $__templater->func('link', array('admins/delete', $__vars['admin'], ), false),
				'overlay' => 'true',
				'_type' => 'delete',
				'html' => '',
			))) . '
				';
		}
	}
	$__finalCompiled .= $__templater->dataList('
				' . $__compilerTemp1 . '
			', array(
	)) . '
		</div>
	</div>
</div>';
	return $__finalCompiled;
});