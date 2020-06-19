<?php
// FROM HASH: 25e105386b9c29949be5cb1f033d2b09
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<!DOCTYPE html>
<html id="XF" lang="' . $__templater->escape($__vars['xf']['language']['language_code']) . '" dir="' . $__templater->escape($__vars['xf']['language']['text_direction']) . '" class="has-no-js p-runJob"
	data-app="admin">
<head>

	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>
		' . $__templater->func('page_title', array('%s | ' . $__vars['xf']['options']['boardTitle'] . ' - ' . 'Admin control panel', $__vars['xf']['options']['boardTitle'] . ' - ' . 'Admin control panel', null)) . '
	</title>
	';
	if ($__templater->isTraversable($__vars['head'])) {
		foreach ($__vars['head'] AS $__vars['headTag']) {
			$__finalCompiled .= '
		' . $__templater->escape($__vars['headTag']) . '
	';
		}
	}
	$__finalCompiled .= '

	' . $__templater->callMacro('public:helper_js_global', 'head', array(
		'app' => 'admin',
	), $__vars) . '
</head>
<body>

<main class="p-runJobContent">
	<div class="p-main-header">
		<div class="p-title">
			<h1 class="p-title-value">
				' . $__templater->func('page_h1', array($__vars['xf']['options']['boardTitle'])) . '
			</h1>
			';
	$__compilerTemp1 = '';
	$__compilerTemp1 .= (isset($__templater->pageParams['pageAction']) ? $__templater->pageParams['pageAction'] : '');
	if (strlen(trim($__compilerTemp1)) > 0) {
		$__finalCompiled .= '
				<div class="p-title-pageAction">' . $__compilerTemp1 . '</div>
			';
	}
	$__finalCompiled .= '
		</div>
	</div>

	' . $__templater->filter($__vars['content'], array(array('raw', array()),), true) . '
</main>


';
	$__compilerTemp2 = '';
	$__compilerTemp2 .= '
		' . $__templater->callMacro('public:debug_macros', 'debug', array(
		'controller' => $__vars['controller'],
		'action' => $__vars['actionMethod'],
		'template' => $__vars['template'],
		'link' => false,
	), $__vars) . '
	';
	if (strlen(trim($__compilerTemp2)) > 0) {
		$__finalCompiled .= '
	<div class="p-runJobDebug">
	' . $__compilerTemp2 . '
	</div>
';
	}
	$__finalCompiled .= '

' . $__templater->callMacro('admin:helper_js_global', 'body', array(
		'jsState' => $__vars['jsState'],
	), $__vars) . '

</body>
</html>';
	return $__finalCompiled;
});