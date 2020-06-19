<?php
// FROM HASH: b4c01481332d2342f35367d271f1a0b7
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Server error logs');
	$__finalCompiled .= '

';
	if (!$__templater->test($__vars['entries'], 'empty', array())) {
		$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('
		' . 'Clear' . '
	', array(
			'href' => $__templater->func('link', array('logs/server-errors/clear', ), false),
			'icon' => 'delete',
			'overlay' => 'true',
		), '', array(
		)) . '
');
	}
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
						' . $__templater->dataRow(array(
					'overlay' => 'true',
					'label' => ($__templater->escape($__vars['entry']['message']) ?: '&nbsp;'),
					'href' => $__templater->func('link', array('logs/server-errors', $__vars['entry'], ), false),
					'delete' => $__templater->func('link', array('logs/server-errors/delete', $__vars['entry'], ), false),
					'dir' => 'auto',
					'explain' => '
								<ul class="listInline listInline--bullet">
									<li>' . $__templater->func('date_dynamic', array($__vars['entry']['exception_date'], array(
				))) . '</li>
									<li>' . $__templater->escape($__vars['entry']['filename']) . ':' . $__templater->escape($__vars['entry']['line']) . '</li>
								</ul>
							',
				), array()) . '
					';
			}
		}
		$__finalCompiled .= $__templater->dataList('
					' . $__compilerTemp1 . '
				', array(
		)) . '
			</div>
			<div class="block-footer">
				<span class="block-footer-counter">' . $__templater->func('display_totals', array($__vars['entries'], $__vars['total'], ), true) . '</span>
			</div>
		</div>

		' . $__templater->func('page_nav', array(array(
			'page' => $__vars['page'],
			'total' => $__vars['total'],
			'link' => 'logs/server-error',
			'wrapperclass' => 'block-outer block-outer--after',
			'perPage' => $__vars['perPage'],
		))) . '
	</div>
';
	} else {
		$__finalCompiled .= '
	<div class="blockMessage">' . 'No errors have been logged.' . '</div>
';
	}
	return $__finalCompiled;
});