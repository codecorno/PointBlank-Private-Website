<?php
// FROM HASH: 9a1ce55af88afa0c5dfc9525713e3428
return array('macros' => array('file_check_list' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'fileChecks' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	$__compilerTemp1 = '';
	$__vars['i'] = 0;
	if ($__templater->isTraversable($__vars['fileChecks'])) {
		foreach ($__vars['fileChecks'] AS $__vars['fileCheck']) {
			$__vars['i']++;
			$__compilerTemp1 .= '
			';
			$__compilerTemp2 = '';
			if ($__vars['fileCheck']['check_state'] == 'success') {
				$__compilerTemp2 .= '
						' . 'Success' . '
					';
			} else if ($__vars['fileCheck']['check_state'] == 'failure') {
				$__compilerTemp2 .= '
						' . 'Failed' . '
					';
			} else if ($__vars['fileCheck']['check_state'] == 'pending') {
				$__compilerTemp2 .= '
						' . 'Pending' . '
					';
			}
			$__compilerTemp3 = array(array(
				'_type' => 'cell',
				'html' => '
					' . $__templater->func('date_dynamic', array($__vars['fileCheck']['check_date'], array(
			))) . '
				',
			)
,array(
				'_type' => 'cell',
				'html' => '
					' . $__compilerTemp2 . '
				',
			));
			if ($__vars['fileCheck']['check_state'] == 'pending') {
				$__compilerTemp3[] = array(
					'_type' => 'cell',
					'html' => 'N/A',
				);
				$__compilerTemp3[] = array(
					'_type' => 'cell',
					'html' => 'N/A',
				);
				$__compilerTemp3[] = array(
					'_type' => 'cell',
					'html' => 'N/A',
				);
				$__compilerTemp3[] = array(
					'_type' => 'action',
					'html' => '',
				);
			} else {
				$__compilerTemp3[] = array(
					'_type' => 'cell',
					'html' => $__templater->filter($__vars['fileCheck']['total_missing'], array(array('number', array()),), true),
				);
				$__compilerTemp3[] = array(
					'_type' => 'cell',
					'html' => $__templater->filter($__vars['fileCheck']['total_inconsistent'], array(array('number', array()),), true),
				);
				$__compilerTemp3[] = array(
					'_type' => 'cell',
					'html' => $__templater->filter($__vars['fileCheck']['total_checked'], array(array('number', array()),), true),
				);
				$__compilerTemp3[] = array(
					'href' => $__templater->func('link', array('tools/file-check/results', $__vars['fileCheck'], ), false),
					'_type' => 'action',
					'html' => 'Results',
				);
			}
			$__compilerTemp1 .= $__templater->dataRow(array(
				'rowclass' => (($__vars['i'] == 1) ? 'dataList-row--highlighted' : ''),
			), $__compilerTemp3) . '
		';
		}
	}
	$__finalCompiled .= $__templater->dataList('
		' . $__templater->dataRow(array(
		'rowtype' => 'header',
	), array(array(
		'_type' => 'cell',
		'html' => 'Check date',
	),
	array(
		'_type' => 'cell',
		'html' => 'Check state',
	),
	array(
		'_type' => 'cell',
		'html' => 'Missing',
	),
	array(
		'_type' => 'cell',
		'html' => 'Unexpected contents',
	),
	array(
		'_type' => 'cell',
		'html' => 'Total checked',
	),
	array(
		'_type' => 'cell',
		'html' => ' ',
	))) . '

		' . $__compilerTemp1 . '
	', array(
		'data-xf-init' => 'responsive-data-list',
	)) . '
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('File health check');
	$__finalCompiled .= '

' . $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formInfoRow('<p>This system will scan the files of your XenForo installation and its add-ons and identify any files that are missing, or whose contents do not match the expected contents of that file.</p>
				
<p>It can be useful for quickly checking that all files have been correctly uploaded.</p>
			
<p>Click the <b>' . 'Proceed' . '</b> button below to run the test.</p>', array(
		'rowtype' => 'close',
	)) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'submit' => 'Proceed' . $__vars['xf']['language']['ellipsis'],
	), array(
		'rowtype' => 'simple',
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('tools/file-check', ), false),
		'class' => 'block',
	)) . '

';
	if (!$__templater->test($__vars['fileChecks'], 'empty', array())) {
		$__finalCompiled .= '
	<div class="block">
		<div class="block-container">
			<h3 class="block-header">' . 'File health check results' . '</h3>
			<div class="block-body">
				' . $__templater->callMacro(null, 'file_check_list', array(
			'fileChecks' => $__vars['fileChecks'],
		), $__vars) . '
			</div>
			<div class="block-footer">
				<span class="block-footer-counter">' . $__templater->func('display_totals', array($__vars['fileChecks'], $__vars['total'], ), true) . '</span>
			</div>
		</div>
		' . $__templater->func('page_nav', array(array(
			'page' => $__vars['page'],
			'total' => $__vars['total'],
			'link' => 'tools/file-check',
			'wrapperclass' => 'block-outer block-outer--after',
			'perPage' => $__vars['perPage'],
		))) . '
	</div>
';
	}
	$__finalCompiled .= '

';
	return $__finalCompiled;
});