<?php
// FROM HASH: 38d8cba02dd37a705317a751ae80f215
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Preview feed entry');
	$__finalCompiled .= '
';
	$__templater->pageParams['pageH1'] = $__templater->preEscaped('
	' . 'Preview feed entry' . $__vars['xf']['language']['label_separator'] . ' <a href="' . $__templater->escape($__vars['feed']['url']) . '" target="_blank">' . $__templater->escape($__vars['feed']['title']) . '</a>
');
	$__finalCompiled .= '

<div class="block">
	<div class="block-container">
		<div class="block-body">
			<div class="block-row block-row--separated">
				<h3 class="block-textHeader">' . $__templater->escape($__vars['entry']['title']) . '</h3>
				' . $__templater->func('bb_code', array($__vars['entry']['message'], 'post', $__vars['feed']['User'], ), true) . '
			</div>
			<div class="block-row block-row--separated">
				<dl class="pairs pairs--columns pairs--fluidSmall">
					<dt>' . 'Author' . '</dt>
					<dd>' . $__templater->escape($__vars['entry']['author']) . '</dd>
				</dl>
				<dl class="pairs pairs--columns pairs--fluidSmall">
					<dt>' . 'Last modified' . '</dt>
					<dd>' . $__templater->func('date_dynamic', array($__vars['entry']['date_modified'], array(
	))) . '</dd>
				</dl>
			</div>
		</div>
	</div>
</div>';
	return $__finalCompiled;
});