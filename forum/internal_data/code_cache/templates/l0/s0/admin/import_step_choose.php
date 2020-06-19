<?php
// FROM HASH: f8b24130d77aa0588c5b7838cd88de81
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Configure importer' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['title']));
	$__finalCompiled .= '

<div class="js-importConfigForm">

	';
	if ($__vars['isCoreImporter'] AND $__vars['canRetainIds']) {
		$__finalCompiled .= '
		<div class="blockMessage blockMessage--warning blockMessage--iconic">
			' . 'In order to avoid unexpected user merges, please ensure your <a href="' . $__templater->func('link', array('users/edit', $__vars['xf']['visitor'], ), true) . '" target="_blank">current user</a> is using a user name and email that is not used by any users in the source database!' . '
		</div>
	';
	}
	$__finalCompiled .= '

	';
	$__compilerTemp1 = array();
	if ($__templater->isTraversable($__vars['availableSteps'])) {
		foreach ($__vars['availableSteps'] AS $__vars['stepId'] => $__vars['stepInfo']) {
			$__compilerTemp1[] = array(
				'value' => $__vars['stepId'],
				'selected' => 1,
				'label' => $__templater->escape($__vars['stepInfo']['title']),
				'_type' => 'option',
			);
		}
	}
	$__compilerTemp2 = array();
	if ($__vars['canRetainIds']) {
		$__compilerTemp3 = '';
		if ($__vars['isCoreImporter']) {
			$__compilerTemp3 .= '<strong>' . 'Note: if selected, user ID 1 from the source database will be merged into user ID 1 in XenForo. The XenForo user\'s email and password will be maintained for security.' . '</strong>';
		}
		$__compilerTemp2[] = array(
			'name' => 'retain_ids',
			'selected' => 1,
			'label' => 'Retain content IDs',
			'hint' => '
								' . 'If selected, when possible, the same ID will be used in XenForo as was used in the source database. This can make it easier redirect old links into XenForo.' . '
								' . $__compilerTemp3 . '
							',
			'_type' => 'option',
		);
	} else {
		$__compilerTemp2[] = array(
			'disabled' => 'disabled',
			'label' => 'Retain content IDs',
			'hint' => '<b>Note</b>: content IDs cannot be retained when importing into a forum with existing data, as it would conflict with existing content. Imported content will receive new IDs and mapping will need to be done through the import log.',
			'_type' => 'option',
		);
	}
	$__finalCompiled .= $__templater->form('

		<div class="block-container">
			<div class="block-body">
				' . $__templater->formCheckBoxRow(array(
		'name' => 'steps[]',
		'listclass' => 'data',
	), $__compilerTemp1, array(
		'label' => 'Data to import',
		'hint' => '
						<br />
						' . $__templater->formCheckBox(array(
		'standalone' => 'true',
	), array(array(
		'check-all' => '.data',
		'label' => 'Select all',
		'_type' => 'option',
	))) . '
					',
	)) . '

				' . $__templater->formCheckBoxRow(array(
	), $__compilerTemp2, array(
		'label' => 'Content IDs',
	)) . '

				' . $__templater->formTextBoxRow(array(
		'name' => 'log_table',
		'required' => 'required',
		'value' => $__vars['logTable'],
	), array(
		'hint' => 'Required',
		'label' => 'Import log table name',
		'explain' => 'During the import an import log will be created which maps original content IDs to the newly imported IDs. It may be used to aid with URL redirections or additional imports. Only use a-z, 0-9 and underscore characters.',
	)) . '
			</div>
			' . $__templater->formSubmitRow(array(
		'submit' => 'Continue' . $__vars['xf']['language']['ellipsis'],
	), array(
	)) . '
		</div>

		' . $__templater->formHiddenVal('config', $__templater->filter($__vars['baseConfig'], array(array('json', array()),), false), array(
	)) . '
		' . $__templater->formHiddenVal('importer', $__vars['importerId'], array(
	)) . '
	', array(
		'action' => $__templater->func('link', array('import/step-config', ), false),
		'class' => 'block',
		'ajax' => 'true',
		'data-replace' => '.js-importConfigForm',
	)) . '
</div>';
	return $__finalCompiled;
});