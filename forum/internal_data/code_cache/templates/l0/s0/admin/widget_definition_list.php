<?php
// FROM HASH: 22b7ed0b8c3ba5515c6298ac363ae256
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Widget definitions');
	$__finalCompiled .= '

';
	$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('Add widget definition', array(
		'href' => $__templater->func('link', array('widgets/definitions/add', ), false),
		'icon' => 'add',
	), '', array(
	)) . '
');
	$__finalCompiled .= '

';
	if (!$__templater->test($__vars['widgetDefinitions'], 'empty', array())) {
		$__finalCompiled .= '
	<div class="block">
		<div class="block-outer">
			' . $__templater->callMacro('filter_macros', 'quick_filter', array(
			'key' => 'widget-definitions',
			'class' => 'block-outer-opposite',
		), $__vars) . '
		</div>
		<div class="block-container">
			<div class="block-body">
				';
		$__compilerTemp1 = '';
		if ($__templater->isTraversable($__vars['widgetDefinitions'])) {
			foreach ($__vars['widgetDefinitions'] AS $__vars['widgetDefinition']) {
				$__compilerTemp1 .= '
						' . $__templater->dataRow(array(
					'label' => $__templater->escape($__vars['widgetDefinition']['title']),
					'hint' => $__templater->escape($__vars['widgetDefinition']['definition_class']),
					'explain' => $__templater->escape($__vars['widgetDefinition']['description']),
					'href' => $__templater->func('link', array('widgets/definitions/edit', $__vars['widgetDefinition'], ), false),
					'delete' => $__templater->func('link', array('widgets/definitions/delete', $__vars['widgetDefinition'], ), false),
				), array(array(
					'href' => $__templater->func('link', array('widgets/add', null, array('definition_id' => $__vars['widgetDefinition']['definition_id'], ), ), false),
					'_type' => 'action',
					'html' => '
								' . 'Add widget' . '
							',
				))) . '
					';
			}
		}
		$__finalCompiled .= $__templater->dataList('
					' . $__compilerTemp1 . '
				', array(
		)) . '
			</div>
			<div class="block-footer">
				<span class="block-footer-counter">' . $__templater->func('display_totals', array($__vars['widgetDefinitions'], ), true) . '</span>
			</div>
		</div>
	</div>
';
	} else {
		$__finalCompiled .= '
	<div class="blockMessage">' . 'No items have been created yet.' . '</div>
';
	}
	return $__finalCompiled;
});