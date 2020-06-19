<?php
// FROM HASH: 18d7a8170e4fa460403d6bc6f7f7ec9a
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<mail:subject>
	' . 'File healthcheck warnings on ' . $__templater->escape($__vars['xf']['options']['boardTitle']) . '' . '
</mail:subject>

<p>' . 'An automatic file health check was started on ' . $__templater->escape($__vars['xf']['options']['boardTitle']) . ' at ' . $__templater->func('date_time', array($__vars['fileCheck']['check_date'], ), true) . ' and some problems were found that should be resolved as soon as possible.' . '</p>

';
	if ($__vars['fileCheck']['total_missing'] AND $__vars['fileCheck']['total_inconsistent']) {
		$__finalCompiled .= '
	<p>' . 'Após verificar os arquivos ' . $__templater->filter($__vars['fileCheck']['total_checked'], array(array('number', array()),), true) . ', encontramos ' . $__templater->filter($__vars['fileCheck']['total_missing'], array(array('number', array()),), true) . ' arquivos ausentes e ' . $__templater->filter($__vars['fileCheck']['total_inconsistent'], array(array('number', array()),), true) . ' arquivos que continham conteúdo inesperado.' . '</p>
';
	} else if ($__vars['fileCheck']['total_missing']) {
		$__finalCompiled .= '
	<p>' . 'Depois de verificar os arquivos ' . $__templater->filter($__vars['fileCheck']['total_checked'], array(array('number', array()),), true) . ', encontramos ' . $__templater->filter($__vars['fileCheck']['total_missing'], array(array('number', array()),), true) . ' arquivos em falta.' . '</p>
';
	} else if ($__vars['fileCheck']['total_inconsistent']) {
		$__finalCompiled .= '
	<p>' . 'Após verificar os arquivos ' . $__templater->filter($__vars['fileCheck']['total_checked'], array(array('number', array()),), true) . ', encontramos arquivos ' . $__templater->filter($__vars['fileCheck']['total_inconsistent'], array(array('number', array()),), true) . ' que continham conteúdo inesperado.' . '</p>
';
	}
	$__finalCompiled .= '

<table cellpadding="10" cellspacing="0" border="0" width="100%" class="linkBar">
	<tr>
		<td>
			<a href="' . $__templater->func('link_type', array('admin', 'canonical:tools/file-check/results', $__vars['fileCheck'], ), true) . '" class="button">' . 'Revise os arquivos' . '</a>
		</td>
	</tr>
</table>';
	return $__finalCompiled;
});