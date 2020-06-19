<?php
// FROM HASH: 51b783fc91a8844575fccf25a3c31262
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('API keys');
	$__finalCompiled .= '

';
	$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('Add API key', array(
		'href' => $__templater->func('link', array('api-keys/add', ), false),
		'icon' => 'add',
	), '', array(
	)) . '
');
	$__finalCompiled .= '

';
	if ($__vars['newKey']) {
		$__finalCompiled .= '
	<div class="blockMessage blockMessage--important blockMessage--iconic">
		' . 'The API key "' . $__templater->escape($__vars['newKey']['title']) . '" has been created or updated. The key to use with the API is as follows:' . '
		<div style="margin: 1em 0; text-align: center">' . $__templater->callMacro('api_key_macros', 'copy_key', array(
			'apiKey' => $__vars['newKey'],
		), $__vars) . '</div>
		' . $__templater->callMacro('api_key_macros', 'key_usage', array(
			'apiKey' => $__vars['newKey'],
		), $__vars) . '
	</div>
';
	}
	$__finalCompiled .= '

';
	if (!$__templater->test($__vars['apiKeys'], 'empty', array())) {
		$__finalCompiled .= '
	';
		$__compilerTemp1 = '';
		if ($__templater->isTraversable($__vars['apiKeys'])) {
			foreach ($__vars['apiKeys'] AS $__vars['apiKey']) {
				$__compilerTemp1 .= '
						';
				$__compilerTemp2 = '';
				if ($__vars['apiKey']['last_use_date']) {
					$__compilerTemp2 .= '
										<li>
											' . 'Last used' . ':
											' . $__templater->func('date_dynamic', array($__vars['apiKey']['last_use_date'], array(
					))) . '
										</li>
									';
				}
				$__compilerTemp1 .= $__templater->dataRow(array(
					'label' => $__templater->escape($__vars['apiKey']['title']),
					'href' => $__templater->func('link', array('api-keys/edit', $__vars['apiKey'], ), false),
					'delete' => $__templater->func('link', array('api-keys/delete', $__vars['apiKey'], ), false),
					'explain' => '
								<ul class="listInline listInline--bullet">
									<li>' . $__templater->escape($__vars['apiKey']['api_key_snippet']) . '</li>
									<li>
										' . $__templater->callMacro('api_key_macros', 'key_type', array(
					'apiKey' => $__vars['apiKey'],
				), $__vars) . '
									</li>
									<li>
										' . 'Created' . ':
										' . $__templater->func('date_dynamic', array($__vars['apiKey']['creation_date'], array(
				))) . '
									</li>
									' . $__compilerTemp2 . '
								</ul>
							',
				), array(array(
					'name' => 'active[' . $__vars['apiKey']['api_key_id'] . ']',
					'selected' => $__vars['apiKey']['active'],
					'class' => 'dataList-cell--separated',
					'submit' => 'true',
					'tooltip' => 'Enable / disable \'' . $__vars['apiKey']['title'] . '\'',
					'_type' => 'toggle',
					'html' => '',
				))) . '
					';
			}
		}
		$__finalCompiled .= $__templater->form('
		<div class="block-outer">
			' . $__templater->callMacro('filter_macros', 'quick_filter', array(
			'key' => 'apiKeys',
			'class' => 'block-outer-opposite',
		), $__vars) . '
		</div>
		<div class="block-container">
			<div class="block-body">
				' . $__templater->dataList('
					' . $__compilerTemp1 . '
				', array(
		)) . '
			</div>
		</div>
	', array(
			'action' => $__templater->func('link', array('api-keys/toggle', ), false),
			'class' => 'block',
			'ajax' => 'true',
		)) . '
';
	} else {
		$__finalCompiled .= '
	<div class="blockMessage">' . 'No items have been created yet.' . '</div>
';
	}
	return $__finalCompiled;
});