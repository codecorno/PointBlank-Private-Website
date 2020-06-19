<?php
// FROM HASH: 9688cd7c83878aacd024ea5ede463033
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Spam trigger log');
	$__finalCompiled .= '

<div class="block">
	<div class="block-container">
		<div class="block-body block-row">
			<ul class="listInline listInline--bullet">
				<li><span class="u-dimmed">' . 'Ação' . $__vars['xf']['language']['label_separator'] . '</span> ' . (($__vars['entry']['result'] == 'moderated') ? 'Moderated' : 'Rejeitado') . '</li>
				<li class="u-dimmed">' . $__templater->escape($__vars['entry']['details']) . '</li>
			</ul>
			<ul class="listInline listInline--bullet u-muted">
				<li class="u-muted">' . 'Gerado por' . $__vars['xf']['language']['label_separator'] . ' ' . ($__vars['entry']['User'] ? (((('<a href="' . $__templater->func('link', array('users/edit', $__vars['entry']['User'], ), true)) . '">') . $__templater->escape($__vars['entry']['User']['username'])) . '</a>') : 'Unknown account') . '</li>
				<li>' . $__templater->func('date_dynamic', array($__vars['entry']['log_date'], array(
	))) . '</li>
				<li>' . 'Conteúdo' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['entry']['content_type']) . '</li>
			</ul>
		</div>

		<h3 class="block-minorHeader">' . 'Request state' . '</h3>
		<div class="block-body block-body--contained block-row" dir="ltr">
			' . $__templater->func('dump_simple', array($__vars['entry']['request_state'], ), true) . '
		</div>
	</div>
</div>';
	return $__finalCompiled;
});