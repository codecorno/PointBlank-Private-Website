<?php
// FROM HASH: 35cae571f501ce919bd564d46fbffa2b
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Promotion history');
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
				if ($__vars['entry']['promotion_state'] == 'manual') {
					$__compilerTemp2 .= '
									- ' . 'Manually applied' . '
								';
				} else if ($__vars['entry']['promotion_state'] == 'disabled') {
					$__compilerTemp2 .= '
									- ' . 'Promotion disabled' . '
								';
				} else {
					$__compilerTemp2 .= '
									- ' . 'Automatically promoted' . '
								';
				}
				$__compilerTemp1 .= $__templater->dataRow(array(
					'label' => $__templater->escape($__vars['entry']['User']['username']),
					'hint' => $__templater->escape($__vars['entry']['Promotion']['title']),
					'delete' => $__templater->func('link', array('user-group-promotions/demote', null, array('promotion_id' => $__vars['entry']['promotion_id'], 'user_id' => $__vars['entry']['user_id'], ), ), false),
					'explain' => '
								' . $__templater->func('date_dynamic', array($__vars['entry']['promotion_date'], array(
				))) . '
								' . $__compilerTemp2 . '
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
		</div>

		' . $__templater->func('page_nav', array(array(
			'page' => $__vars['page'],
			'total' => $__vars['totalEntries'],
			'link' => 'user-group-promotions/history',
			'params' => $__vars['linkParams'],
			'wrapperclass' => 'block-outer block-outer--after',
			'perPage' => $__vars['perPage'],
		))) . '
	</div>
';
	} else {
		$__finalCompiled .= '
	<div class="blockMessage">' . 'No promotion history meeting that criteria can be found.' . '</div>
';
	}
	return $__finalCompiled;
});