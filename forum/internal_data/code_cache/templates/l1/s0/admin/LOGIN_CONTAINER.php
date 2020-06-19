<?php
// FROM HASH: 0818b1981bb37f63b44bfe8f211b0ff7
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<!DOCTYPE html>
<html id="XF" lang="' . $__templater->escape($__vars['xf']['language']['language_code']) . '" dir="' . $__templater->escape($__vars['xf']['language']['text_direction']) . '"
	class="has-no-js p-adminLogin ' . ($__vars['template'] ? ('template-' . $__templater->escape($__vars['template'])) : '') . '" data-template="' . $__templater->escape($__vars['template']) . '"
	data-app="admin"
	data-cookie-prefix="' . $__templater->escape($__vars['xf']['cookie']['prefix']) . '"
	' . ($__vars['xf']['runJobs'] ? ' data-run-jobs=""' : '') . '>
<head>
	<meta charset="utf-8" />
	<meta name="robots" content="noindex" />
	<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>' . 'Administrator login' . ' | ' . $__templater->escape($__vars['xf']['options']['boardTitle']) . '</title>
	';
	if ($__templater->isTraversable($__vars['head'])) {
		foreach ($__vars['head'] AS $__vars['head']) {
			$__finalCompiled .= '
		' . $__templater->escape($__vars['head']) . '
	';
		}
	}
	$__finalCompiled .= '

	' . $__templater->callMacro('public:helper_js_global', 'head', array(
		'app' => 'admin',
	), $__vars) . '
</head>
<body>

<div class="adminLogin-wrapper">
	<div class="adminLogin-content ' . ($__vars['loginWide'] ? 'adminLogin-content--wide' : '') . '">
		' . $__templater->filter($__vars['content'], array(array('raw', array()),), true) . '
		';
	$__compilerTemp1 = '';
	$__compilerTemp1 .= '
					' . $__templater->callMacro('public:debug_macros', 'debug', array(
		'controller' => $__vars['controller'],
		'action' => $__vars['actionMethod'],
		'template' => $__vars['template'],
	), $__vars) . '
				';
	if (strlen(trim($__compilerTemp1)) > 0) {
		$__finalCompiled .= '
			<div class="adminLogin-debug">
				' . $__compilerTemp1 . '
			</div>
		';
	}
	$__finalCompiled .= '
	</div>
</div>

' . $__templater->callMacro('admin:helper_js_global', 'body', array(
		'jsState' => $__vars['jsState'],
	), $__vars) . '

</body>
</html>';
	return $__finalCompiled;
});