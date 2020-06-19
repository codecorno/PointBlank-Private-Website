<?php
// FROM HASH: 0af9295cb1aa5514ed4159b36008c4c2
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped($__templater->escape($__vars['actionText']) . $__templater->escape($__vars['xf']['language']['ellipsis']));
	$__finalCompiled .= '
';
	$__templater->setPageParam('template', 'PAGE_RUN_JOB');
	$__finalCompiled .= '

<form action="' . $__templater->func('link', array($__vars['actionRoute'], $__vars['addOn'], ), true) . '" method="post"
	class="blockMessage"
	data-xf-init="auto-submit">

	<div>
		' . $__templater->escape($__vars['actionText']) . '
		<strong>' . $__templater->escape($__vars['addOn']['title']) . ' ' . $__templater->escape($__vars['addOn']['version_string']) . $__templater->func('repeat', array(' . ', $__vars['count'], ), true) . '</strong>
	</div>

	<div class="u-noJsOnly">' . $__templater->button('Proceed' . $__vars['xf']['language']['ellipsis'], array(
		'type' => 'submit',
	), '', array(
	)) . '</div>

	' . $__templater->formHiddenVal('_xfProcessing', $__vars['isProcessing'], array(
	)) . '

	' . $__templater->formHiddenVal('continue', '1', array(
	)) . '
	' . $__templater->formHiddenVal('confirm', '1', array(
	)) . '

	' . $__templater->formHiddenVal('params', $__templater->filter($__vars['params'], array(array('json', array()),), false), array(
	)) . '
	' . $__templater->formHiddenVal('count', $__vars['count'], array(
	)) . '
	' . $__templater->formHiddenVal('finished', ($__vars['finished'] ? 1 : 0), array(
	)) . '

	' . $__templater->func('csrf_input') . '
</form>';
	return $__finalCompiled;
});