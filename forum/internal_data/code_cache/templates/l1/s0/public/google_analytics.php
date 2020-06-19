<?php
// FROM HASH: 502034aa7ca3aa8055e748de4126af65
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__vars['xf']['options']['googleAnalyticsWebPropertyId']) {
		$__finalCompiled .= '
	<script async src="https://www.googletagmanager.com/gtag/js?id=' . $__templater->escape($__vars['xf']['options']['googleAnalyticsWebPropertyId']) . '"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag(\'js\', new Date());
		gtag(\'config\', \'' . $__templater->filter($__vars['xf']['options']['googleAnalyticsWebPropertyId'], array(array('escape', array('js', )),), true) . '\', {
			// ' . '
			';
		if ($__vars['xf']['cookie']['domain']) {
			$__finalCompiled .= '
				\'cookie_domain\': \'' . $__templater->escape($__vars['xf']['cookie']['domain']) . '\',
			';
		}
		$__finalCompiled .= '
			';
		if ($__vars['xf']['options']['googleAnalyticsAnonymize']) {
			$__finalCompiled .= '
				\'anonymize_ip\': true,
			';
		}
		$__finalCompiled .= '
		});
	</script>
';
	}
	return $__finalCompiled;
});