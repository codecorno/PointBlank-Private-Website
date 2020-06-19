<?php
// FROM HASH: 9f9eba4b5da97451d57b723144c122a2
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Upgrade official XenForo add-ons');
	$__finalCompiled .= '

';
	$__compilerTemp1 = array();
	if ($__templater->isTraversable($__vars['availableUpdates'])) {
		foreach ($__vars['availableUpdates'] AS $__vars['addOnId'] => $__vars['availableUpdate']) {
			$__compilerTemp1[] = array(
				'name' => 'confirm_updates[' . $__vars['addOnId'] . ']',
				'value' => $__vars['availableUpdate']['version_id'],
				'label' => '
						' . $__templater->escape($__vars['addOns'][$__vars['addOnId']]['title']) . ' <span class="u-muted">' . $__templater->escape($__vars['availableUpdate']['version_string']) . '</span>
					',
				'_type' => 'option',
			);
		}
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formInfoRow('
				' . 'One or more updates are available for an official XenForo add-on that you have installed.<br />
<br />
Select the updates you want to install below. The selected updates will be downloaded and the upgrades will be applied immediately.' . '

				<div class="block-rowMessage block-rowMessage--important">
					<b>' . 'Note' . $__vars['xf']['language']['label_separator'] . '</b>
					' . 'It is strongly recommended that you backup your database and files before proceeding. This is not done automatically.' . '
				</div>
			', array(
	)) . '
			' . $__templater->formCheckBoxRow(array(
	), $__compilerTemp1, array(
		'label' => 'Available add-on upgrades',
	)) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'icon' => 'download',
		'submit' => 'Download and upgrade' . $__vars['xf']['language']['ellipsis'],
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('tools/upgrade-xf-add-on', ), false),
		'class' => 'block',
	));
	return $__finalCompiled;
});