<?php
// FROM HASH: 26c6680ec8c0cd85783976527837d14b
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<!DOCTYPE html>
<html lang="' . $__templater->escape($__vars['xf']['language']['language_code']) . '" dir="' . $__templater->escape($__vars['xf']['language']['text_direction']) . '">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<base href="' . $__templater->escape($__vars['xf']['options']['boardUrl']) . '/">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="format-detection" content="telephone=no">
	<title>' . $__templater->escape($__vars['subject']) . '</title>
</head>
<body dir="' . $__templater->escape($__vars['xf']['language']['text_direction']) . '" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<table id="bodyTable" border="0" width="100%" height="100%" cellpadding="0" cellspacing="0">
<tr>
	<td align="center" valign="top" id="bodyTableContainer">
		<table border="0" width="600" cellpadding="0" cellspacing="0" class="container" dir="' . $__templater->escape($__vars['xf']['language']['text_direction']) . '">
		<tr>
			<td class="header" align="center" valign="top">
				<a href="' . $__templater->func('link', array('canonical:index', ), true) . '">' . $__templater->escape($__vars['xf']['options']['boardTitle']) . '</a>
			</td>
		</tr>
		<tr>
			<td class="content" align="' . ($__vars['xf']['isRtl'] ? 'right' : 'left') . '" valign="top">

' . $__templater->filter($__vars['html'], array(array('raw', array()),), true) . '

			</td>
		</tr>
		<tr>
			<td class="footer" align="center" valign="top">
				<div>' . '<a href="' . $__templater->func('link', array('canonical:index', ), true) . '">Visit ' . $__templater->escape($__vars['xf']['options']['boardTitle']) . '</a>' . '</div>

				';
	$__compilerTemp1 = '';
	$__compilerTemp1 .= '';
	if (strlen(trim($__compilerTemp1)) > 0) {
		$__finalCompiled .= '
					<div class="footerExtra">' . $__compilerTemp1 . '</div>
				';
	}
	$__finalCompiled .= '
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>

</body>
</html>

<mail:text>
' . $__templater->escape($__vars['text']) . '

-----------------------------

' . 'Visit ' . $__templater->escape($__vars['xf']['options']['boardTitle']) . ': ' . $__templater->func('link', array('canonical:index', ), true) . '' . '

' . '' . '
</mail:text>';
	return $__finalCompiled;
});