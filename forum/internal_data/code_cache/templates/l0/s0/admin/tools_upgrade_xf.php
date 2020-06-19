<?php
// FROM HASH: 4d2630090318e239b2764726c6e2a988
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Upgrade XenForo');
	$__finalCompiled .= '

' . $__templater->form('
	<div class="block-container">
		<div class="block-body block-row">
			' . 'XenForo ' . $__templater->escape($__vars['availableUpdate']['version_string']) . ' is now available for installation.<br />
<br />
You may upgrade to this version from within your control panel. If you proceed, the new version will be downloaded, files copied, and the upgrade will be applied.<br />
<br />
Note that if a CLI upgrade is recommended, you will be given an opportunity to use that instead of the web upgrader.' . '

			<div class="block-rowMessage block-rowMessage--important">
				<b>' . 'Note' . $__vars['xf']['language']['label_separator'] . '</b>
				' . 'It is strongly recommended that you backup your database and files before proceeding. This is not done automatically.' . '
			</div>
		</div>
		' . $__templater->formSubmitRow(array(
		'icon' => 'download',
		'submit' => 'Download and upgrade' . $__vars['xf']['language']['ellipsis'],
	), array(
		'rowtype' => 'simple',
	)) . '
	</div>
	' . $__templater->formHiddenVal('confirm_version_id', $__vars['availableUpdate']['version_id'], array(
	)) . '
', array(
		'action' => $__templater->func('link', array('tools/upgrade-xf', ), false),
		'class' => 'block',
	));
	return $__finalCompiled;
});