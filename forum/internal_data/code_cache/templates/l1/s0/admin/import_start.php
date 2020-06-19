<?php
// FROM HASH: 450ebaabc18c9c5046e33a92eba209ab
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Import data' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['title']));
	$__finalCompiled .= '

' . $__templater->form('
	<div class="block-container">
		<h2 class="block-tabHeader tabs" data-xf-init="tabs" role="tablist">
			<a class="tabs-tab is-active" role="tab" tabindex="0" aria-controls="import-web">' . 'Browser importer' . '</a>
			<a class="tabs-tab" role="tab" tabindex="0" aria-controls="import-cli">' . 'CLI importer' . '</a>
		</h2>
		<ul class="tabPanes">
			<li class="is-active" role="tabpanel" id="import-web">
				<div class="block-body block-row">
					' . 'You\'re now ready to start the import. Once started, the import will continue to run while this browser window is open. Once all data has been imported, further instructions will be displayed.' . '
				</div>
				' . $__templater->formSubmitRow(array(
		'submit' => 'Start import',
	), array(
		'rowtype' => 'simple',
	)) . '
			</li>
			<li role="tabpanel" id="import-cli">
				<div class="block-body block-row">
					' . 'You may also import via the command line. This is recommended for large imports. Run this command from within the root XenForo directory and follow the on-screen instructions' . $__vars['xf']['language']['label_separator'] . '
					<pre style="margin: 1em 2em">php cmd.php xf:import</pre>
					' . 'Once this command completes, you will need to refresh this page to complete the import.' . '<br />
					<br />
					' . 'There are additional options available via the command line. Run the command with the <code>--help</code> option for more information.' . '
				</div>
			</li>
		</ul>
	</div>
', array(
		'action' => $__templater->func('link', array('import/run', ), false),
		'class' => 'block',
	));
	return $__finalCompiled;
});