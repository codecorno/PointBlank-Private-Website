<?php
// FROM HASH: 1723f5ea5388b963e9d5c0658b60612c
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Spam cleaner log');
	$__finalCompiled .= '

';
	if (!$__templater->test($__vars['entries'], 'empty', array())) {
		$__finalCompiled .= '
	<div class="block">
		<div class="block-container">
			<div class="block-body">
				';
		$__compilerTemp1 = '';
		if ($__templater->isTraversable($__vars['entries'])) {
			foreach ($__vars['entries'] AS $__vars['entry']) {
				$__compilerTemp1 .= '
						';
				$__compilerTemp2 = '';
				if ($__vars['entry']['restored_date']) {
					$__compilerTemp2 .= '
									' . $__templater->func('date_dynamic', array($__vars['entry']['restored_date'], array(
					))) . '
								';
				} else {
					$__compilerTemp2 .= '
									' . 'No' . '
								';
				}
				$__compilerTemp1 .= $__templater->dataRow(array(
				), array(array(
					'_type' => 'cell',
					'html' => '
								' . $__templater->func('username_link', array($__vars['entry']['User'], false, array(
					'defaultname' => $__vars['entry']['username'],
					'href' => ($__vars['entry']['User'] ? $__templater->func('link', array('users/edit', $__vars['entry']['User'], ), false) : null),
				))) . '
							',
				),
				array(
					'_type' => 'cell',
					'html' => '
								' . $__templater->func('username_link', array($__vars['entry']['ApplyingUser'], false, array(
					'defaultname' => $__vars['entry']['applying_username'],
					'href' => ($__vars['entry']['ApplyingUser'] ? $__templater->func('link_type', array('public', 'members', $__vars['entry']['ApplyingUser'], ), false) : null),
				))) . '
							',
				),
				array(
					'_type' => 'cell',
					'html' => '
								' . $__templater->func('date_dynamic', array($__vars['entry']['application_date'], array(
				))) . '
							',
				),
				array(
					'_type' => 'cell',
					'html' => '
								' . $__compilerTemp2 . '
							',
				),
				array(
					'href' => $__templater->func('link', array('logs/spam-cleaner/restore', $__vars['entry'], ), false),
					'overlay' => 'true',
					'class' => 'dataList-cell--action',
					'_type' => 'cell',
					'html' => '
								' . 'Restore' . '
							',
				))) . '
					';
			}
		}
		$__finalCompiled .= $__templater->dataList('
					' . $__templater->dataRow(array(
			'rowtype' => 'header',
		), array(array(
			'_type' => 'cell',
			'html' => 'User',
		),
		array(
			'_type' => 'cell',
			'html' => 'Applied by',
		),
		array(
			'_type' => 'cell',
			'html' => 'Application date',
		),
		array(
			'_type' => 'cell',
			'html' => 'Restored',
		),
		array(
			'class' => 'dataList-cell--min',
			'_type' => 'cell',
			'html' => '&nbsp;',
		))) . '

					' . $__compilerTemp1 . '
				', array(
			'data-xf-init' => 'responsive-data-list',
		)) . '
			</div>
			<div class="block-footer">
				<span class="block-footer-counter">' . $__templater->func('display_totals', array($__vars['entries'], $__vars['total'], ), true) . '</span>
			</div>
		</div>
		' . $__templater->func('page_nav', array(array(
			'page' => $__vars['page'],
			'total' => $__vars['total'],
			'link' => 'logs/spam-cleaner',
			'wrapperclass' => 'block-outer block-outer--after',
			'perPage' => $__vars['perPage'],
		))) . '
	</div>
';
	} else {
		$__finalCompiled .= '
	<div class="blockMessage">' . 'No entries have been logged.' . '</div>
';
	}
	return $__finalCompiled;
});