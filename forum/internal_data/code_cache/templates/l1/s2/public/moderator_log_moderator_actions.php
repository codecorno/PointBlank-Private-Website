<?php
// FROM HASH: 64ff9104af736ce44abe611684127513
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped(($__vars['title'] ? ($__templater->escape($__vars['title']) . ' - ') : '') . 'Moderator actions');
	$__finalCompiled .= '

';
	$__templater->breadcrumbs($__vars['breadcrumbs']);
	$__finalCompiled .= '

';
	if ($__vars['logs']) {
		$__finalCompiled .= '
	<div class="block">
		<div class="block-container js-logList-' . $__templater->escape($__vars['type']) . $__templater->escape($__vars['id']) . '">
			<div class="block-body">
				';
		$__compilerTemp1 = '';
		if ($__templater->isTraversable($__vars['logs'])) {
			foreach ($__vars['logs'] AS $__vars['entry']) {
				$__compilerTemp1 .= '
						' . $__templater->dataRow(array(
				), array(array(
					'_type' => 'cell',
					'html' => $__templater->func('username_link', array($__vars['entry']['User'], false, array(
				))),
				),
				array(
					'_type' => 'cell',
					'html' => $__templater->escape($__vars['entry']['action_text']),
				),
				array(
					'_type' => 'cell',
					'html' => $__templater->func('date_dynamic', array($__vars['entry']['log_date'], array(
				))),
				),
				array(
					'href' => $__templater->func('base_url', array($__vars['entry']['content_url'], ), false),
					'_type' => 'action',
					'html' => 'View',
				))) . '
					';
			}
		}
		$__finalCompiled .= $__templater->dataList('
					' . $__templater->dataRow(array(
			'rowtype' => 'header',
		), array(array(
			'_type' => 'cell',
			'html' => 'Moderator',
		),
		array(
			'_type' => 'cell',
			'html' => 'Action',
		),
		array(
			'_type' => 'cell',
			'html' => 'Date',
		),
		array(
			'_type' => 'cell',
			'html' => '&nbsp;',
		))) . '
					' . $__compilerTemp1 . '
				', array(
			'data-xf-init' => 'responsive-data-list',
		)) . '
			</div>
			<div class="block-footer">
				';
		if ($__vars['hasNext']) {
			$__finalCompiled .= '
					<span class="block-footer-controls">' . $__templater->button('
						' . 'Continue' . $__vars['xf']['language']['ellipsis'] . '
					', array(
				'href' => $__templater->func('link', array($__vars['linkRoute'], $__vars['linkData'], $__vars['linkParams'] + array('page' => $__vars['page'] + 1, ), ), false),
				'data-xf-click' => 'inserter',
				'data-replace' => '.js-logList-' . $__vars['type'] . $__vars['id'],
				'data-scroll-target' => '< .overlay',
			), '', array(
			)) . '</span>
				';
		} else {
			$__finalCompiled .= '
					' . 'Previous moderation action logs may have been removed.' . '
				';
		}
		$__finalCompiled .= '
			</div>
		</div>
	</div>
';
	} else {
		$__finalCompiled .= '
	<div class="blockMessage">' . 'No moderator actions have been logged.' . '</div>
';
	}
	return $__finalCompiled;
});