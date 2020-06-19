<?php
// FROM HASH: 7b517886030eef576fc37c76fa4cc29d
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Moderators');
	$__finalCompiled .= '

';
	$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('Add moderator', array(
		'href' => $__templater->func('link', array('moderators/add', ), false),
		'icon' => 'add',
	), '', array(
	)) . '
');
	$__finalCompiled .= '

';
	if ($__vars['superModerators'] OR $__vars['contentModerators']) {
		$__finalCompiled .= '
	';
		if ($__vars['superModerators']) {
			$__finalCompiled .= '
		<div class="block">
			<div class="block-container">
				<h2 class="block-header">' . 'Super moderators' . '</h2>
				<div class="block-body">
					';
			$__compilerTemp1 = '';
			if ($__templater->isTraversable($__vars['superModerators'])) {
				foreach ($__vars['superModerators'] AS $__vars['superMod']) {
					$__compilerTemp1 .= '
							' . $__templater->dataRow(array(
					), array(array(
						'class' => 'dataList-cell--min dataList-cell--image dataList-cell--imageSmall',
						'href' => $__templater->func('link', array('moderators/super/edit', $__vars['superMod'], ), false),
						'_type' => 'cell',
						'html' => '
									' . $__templater->func('avatar', array($__vars['superMod']['User'], 's', false, array(
						'href' => '',
					))) . '
								',
					),
					array(
						'href' => $__templater->func('link', array('moderators/super/edit', $__vars['superMod'], ), false),
						'label' => $__templater->escape($__vars['superMod']['User']['username']),
						'_type' => 'main',
						'html' => '',
					),
					array(
						'href' => $__templater->func('link', array('users/edit', $__vars['superMod']['User'], ), false),
						'_type' => 'action',
						'html' => 'User info',
					),
					array(
						'href' => $__templater->func('link', array('moderators/super/delete', $__vars['superMod'], ), false),
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
		</div>
	';
		}
		$__finalCompiled .= '

	';
		if ($__vars['contentModerators']) {
			$__finalCompiled .= '
		<div class="block">
			<div class="block-container">
				<h2 class="block-header">' . 'Content moderators' . '</h2>
				<div class="block-body">
					';
			$__compilerTemp2 = '';
			if ($__templater->isTraversable($__vars['users'])) {
				foreach ($__vars['users'] AS $__vars['userId'] => $__vars['user']) {
					$__compilerTemp2 .= '
							';
					if ($__vars['contentModerators'][$__vars['userId']]) {
						$__compilerTemp2 .= '
								' . $__templater->dataRow(array(
							'rowtype' => 'subsection',
							'rowclass' => 'dataList-row--noHover',
						), array(array(
							'colspan' => '3',
							'_type' => 'cell',
							'html' => $__templater->escape($__vars['user']['username']),
						))) . '
								';
						if ($__templater->isTraversable($__vars['contentModerators'][$__vars['userId']])) {
							foreach ($__vars['contentModerators'][$__vars['userId']] AS $__vars['contentMod']) {
								$__compilerTemp2 .= '
									' . $__templater->dataRow(array(
								), array(array(
									'class' => 'dataList-cell--min',
									'_type' => 'cell',
									'html' => '',
								),
								array(
									'href' => $__templater->func('link', array('moderators/content/edit', $__vars['contentMod'], ), false),
									'label' => $__templater->escape($__templater->method($__vars['contentMod'], 'getContentTitle', array())),
									'_type' => 'main',
									'html' => '',
								),
								array(
									'href' => $__templater->func('link', array('moderators/content/delete', $__vars['contentMod'], ), false),
									'overlay' => 'true',
									'_type' => 'delete',
									'html' => '',
								))) . '
								';
							}
						}
						$__compilerTemp2 .= '
							';
					}
					$__compilerTemp2 .= '
						';
				}
			}
			$__finalCompiled .= $__templater->dataList('
						' . $__compilerTemp2 . '
					', array(
			)) . '
				</div>
			</div>
		</div>
	';
		}
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	<div class="blockMessage">' . 'There are currently no moderators. Use the link above to create one.' . '</div>
';
	}
	return $__finalCompiled;
});