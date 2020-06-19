<?php
// FROM HASH: 3441bb4f76970c523ebe36fb398db396
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Test URL unfurling');
	$__finalCompiled .= '
';
	$__templater->pageParams['pageDescription'] = $__templater->preEscaped('This tool helps diagnose errors relating to the URL unfurl system including which metadata can be fetched.');
	$__templater->pageParams['pageDescriptionMeta'] = true;
	$__finalCompiled .= '

';
	if ($__vars['error']) {
		$__finalCompiled .= '
	<div class="blockMessage blockMessage--error">
		' . 'The following error occurred while fetching metadata from URL ' . $__templater->escape($__vars['url']) . '' . '<br />
		<pre>' . $__templater->escape($__vars['error']) . '</pre>
	</div>
';
	}
	$__finalCompiled .= '

' . $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formTextBoxRow(array(
		'name' => 'url',
		'type' => 'url',
		'value' => $__vars['url'],
	), array(
		'label' => 'URL',
	)) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'submit' => 'Run test',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('tools/test-url-unfurling', ), false),
		'class' => 'block',
	)) . '

';
	$__compilerTemp1 = '';
	$__compilerTemp1 .= '
			';
	if ($__vars['results']) {
		$__compilerTemp1 .= '
				<h2 class="block-header">' . 'Results' . '</h2>
				<div class="block-body">
					' . $__templater->formRow('
						' . ($__templater->escape($__vars['results']['title']) ?: 'N/A') . '
					', array(
			'label' => 'Title',
		)) . '

					' . $__templater->formRow('
						' . ($__templater->escape($__vars['results']['description']) ?: 'N/A') . '
					', array(
			'label' => 'Description',
		)) . '

					';
		$__compilerTemp2 = '';
		if ($__vars['results']['image_url']) {
			$__compilerTemp2 .= '
							<img src="' . $__templater->escape($__vars['results']['image_url']) . '" style="width: 100px" />
							<div class="formRow-explain">
								' . 'URL' . $__vars['xf']['language']['label_separator'] . ' <a href="' . $__templater->escape($__vars['results']['image_url']) . '" target="_blank">' . $__templater->escape($__vars['results']['image_url']) . '</a>
							</div>
						';
		} else {
			$__compilerTemp2 .= '
							' . 'N/A' . '
						';
		}
		$__compilerTemp1 .= $__templater->formRow('
						' . $__compilerTemp2 . '
					', array(
			'label' => 'Image',
		)) . '

					';
		$__compilerTemp3 = '';
		if ($__vars['results']['favicon_url']) {
			$__compilerTemp3 .= '
							<img src="' . $__templater->escape($__vars['results']['favicon_url']) . '" style="width: 32px" />
							<div class="formRow-explain">
								' . 'URL' . $__vars['xf']['language']['label_separator'] . ' <a href="' . $__templater->escape($__vars['results']['favicon_url']) . '" target="_blank">' . $__templater->escape($__vars['results']['favicon_url']) . '</a>
							</div>
						';
		} else {
			$__compilerTemp3 .= '
							' . 'N/A' . '
						';
		}
		$__compilerTemp1 .= $__templater->formRow('
						' . $__compilerTemp3 . '
					', array(
			'label' => 'Icon',
		)) . '
				</div>
			';
	}
	$__compilerTemp1 .= '

			';
	if ($__vars['body']) {
		$__compilerTemp1 .= '
				';
		if ($__vars['results']) {
			$__compilerTemp1 .= '
					<h2 class="block-minorHeader">
						<span class="collapseTrigger collapseTrigger--block" data-xf-click="toggle" data-target="< :up :next">
							' . 'Raw response' . '
						</span>
					</h2>
				';
		} else {
			$__compilerTemp1 .= '
					<h2 class="block-header">' . 'Raw response' . '</h2>
				';
		}
		$__compilerTemp1 .= '
				<div class="block-body ' . ($__vars['results'] ? 'block-body--collapsible' : '') . '">
					' . $__templater->callMacro('public:prism_macros', 'setup', array(), $__vars) . '

					' . $__templater->formRow('
						' . 'The following HTML is what was fetched within the default time and size constraints. If some or all of the metadata is missing then it likely means that the HTML we fetched does not contain the metadata.' . '

						<div class="bbCodeBlock bbCodeBlock--screenLimited bbCodeBlock--code">
							<div class="bbCodeBlock-content" dir="ltr">
								<pre class="bbCodeCode" dir="ltr"><code>' . $__templater->escape($__vars['body']) . '</code></pre>
							</div>
						</div>
					', array(
			'label' => 'HTML',
			'rowtype' => 'fullWidth noLabel',
		)) . '
				</div>
			';
	}
	$__compilerTemp1 .= '
		';
	if (strlen(trim($__compilerTemp1)) > 0) {
		$__finalCompiled .= '
	<div class="block">
		<div class="block-container">
		' . $__compilerTemp1 . '
		</div>
	</div>
';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
});