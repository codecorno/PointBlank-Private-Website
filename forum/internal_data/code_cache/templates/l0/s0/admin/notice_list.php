<?php
// FROM HASH: f35931e4216942d897ee875c6b640069
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Notices');
	$__finalCompiled .= '

';
	$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('Add notice', array(
		'href' => $__templater->func('link', array('notices/add', ), false),
		'icon' => 'add',
	), '', array(
	)) . '
');
	$__finalCompiled .= '

';
	if (!$__templater->test($__vars['notices'], 'empty', array())) {
		$__finalCompiled .= '
	';
		if ($__vars['invalidNotices']) {
			$__finalCompiled .= '
		<div class="blockMessage blockMessage--important">
			' . 'The following notice(s) may contain page criteria which is no longer matched' . $__vars['xf']['language']['label_separator'] . '
			<ul class="listInline listInline--comma">
				';
			if ($__templater->isTraversable($__vars['invalidNotices'])) {
				foreach ($__vars['invalidNotices'] AS $__vars['invalidNotice']) {
					$__finalCompiled .= '
					<li><a href="' . $__templater->func('link', array('notices/edit', $__vars['invalidNotice'], ), true) . '">' . $__templater->escape($__vars['invalidNotice']['title']) . '</a></li>
				';
				}
			}
			$__finalCompiled .= '
			</ul>
		</div>
	';
		}
		$__finalCompiled .= '

	';
		$__compilerTemp1 = '';
		if ($__templater->isTraversable($__vars['noticeTypes'])) {
			foreach ($__vars['noticeTypes'] AS $__vars['typeId'] => $__vars['phrase']) {
				if (!$__templater->test($__vars['notices'][$__vars['typeId']], 'empty', array())) {
					$__compilerTemp1 .= '
						<tbody class="dataList-rowGroup">
							' . $__templater->dataRow(array(
						'rowtype' => 'subsection',
						'rowclass' => 'dataList-row--noHover',
					), array(array(
						'colspan' => '3',
						'_type' => 'cell',
						'html' => $__templater->escape($__vars['phrase']),
					))) . '
							';
					if ($__templater->isTraversable($__vars['notices'][$__vars['typeId']])) {
						foreach ($__vars['notices'][$__vars['typeId']] AS $__vars['notice']) {
							$__compilerTemp1 .= '
								' . $__templater->dataRow(array(
								'label' => $__templater->escape($__vars['notice']['title']),
								'href' => $__templater->func('link', array('notices/edit', $__vars['notice'], ), false),
								'delete' => $__templater->func('link', array('notices/delete', $__vars['notice'], ), false),
							), array(array(
								'name' => 'active[' . $__vars['notice']['notice_id'] . ']',
								'selected' => $__vars['notice']['active'],
								'class' => 'dataList-cell--separated',
								'submit' => 'true',
								'tooltip' => 'Enable / disable \'' . $__vars['notice']['title'] . '\'',
								'_type' => 'toggle',
								'html' => '',
							))) . '
							';
						}
					}
					$__compilerTemp1 .= '
						</tbody>
					';
				}
			}
		}
		$__finalCompiled .= $__templater->form('
		<div class="block-outer">
			' . $__templater->callMacro('filter_macros', 'quick_filter', array(
			'key' => 'notices',
			'class' => 'block-outer-opposite',
		), $__vars) . '
		</div>
		<div class="block-container">
			<div class="block-body">
				' . $__templater->dataList('
					' . $__compilerTemp1 . '
				', array(
		)) . '
				<div class="block-footer">
					<span class="block-footer-counter">' . $__templater->func('display_totals', array($__vars['totalNotices'], ), true) . '</span>
				</div>
			</div>
		</div>
	', array(
			'action' => $__templater->func('link', array('notices/toggle', ), false),
			'class' => 'block',
			'ajax' => 'true',
		)) . '
';
	} else {
		$__finalCompiled .= '
	<div class="blockMessage">' . 'No items have been created yet.' . '</div>
';
	}
	$__finalCompiled .= '

' . $__templater->callMacro('option_macros', 'option_form_block', array(
		'options' => $__vars['options'],
	), $__vars);
	return $__finalCompiled;
});