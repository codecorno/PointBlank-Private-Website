<?php
// FROM HASH: bae0d2d2eb42fcbb9fc58b3d934296a1
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->method($__vars['routeFilter'], 'isInsert', array())) {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Add route filter');
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Edit route filter' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['routeFilter']['find_route']));
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	if ($__templater->method($__vars['routeFilter'], 'isUpdate', array())) {
		$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('', array(
			'href' => $__templater->func('link', array('route-filters/delete', $__vars['routeFilter'], ), false),
			'icon' => 'delete',
			'overlay' => 'true',
		), '', array(
		)) . '
');
	}
	$__finalCompiled .= '

' . $__templater->form('
	<div class="block-container">
		<div class="block-body">

			' . $__templater->formTextBoxRow(array(
		'name' => 'find_route',
		'value' => $__vars['routeFilter']['find_route_readable'],
		'maxlength' => $__templater->func('max_length', array($__vars['routeFilter'], 'find_route', ), false),
		'dir' => 'ltr',
	), array(
		'label' => 'Find route',
		'explain' => 'You should only enter the route part of the URL here. For example, if you want to change the URL ' . $__templater->escape($__vars['fullThreadLink']) . ', you should only enter ' . $__templater->escape($__vars['routeValue']) . '.',
	)) . '

			' . $__templater->formTextBoxRow(array(
		'name' => 'replace_route',
		'value' => $__vars['routeFilter']['replace_route_readable'],
		'maxlength' => $__templater->func('max_length', array($__vars['routeFilter'], 'replace_route', ), false),
		'dir' => 'ltr',
	), array(
		'label' => 'Replace with',
		'explain' => 'The find and replace fields support wildcards in the format of {name}, with a unique name. The same wildcards should be found in both fields. To limit the wildcard to digits, use {name:digit}; to limit to a string, use {name:string}; {name} will match anything but a forward slash.',
	)) . '

			' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'enabled',
		'value' => '1',
		'selected' => $__vars['routeFilter']['enabled'],
		'label' => 'Enabled',
		'_type' => 'option',
	),
	array(
		'name' => 'url_to_route_only',
		'value' => '1',
		'selected' => $__vars['routeFilter']['url_to_route_only'],
		'label' => 'Incoming URL conversion only',
		'hint' => 'If selected, this filter will only be used to change incoming URLs, not outgoing route URLs.',
		'_type' => 'option',
	)), array(
	)) . '
		</div>

		' . $__templater->formSubmitRow(array(
		'sticy' => 'true',
		'icon' => 'save',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('route-filters/save', $__vars['routeFilter'], ), false),
		'ajax' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});