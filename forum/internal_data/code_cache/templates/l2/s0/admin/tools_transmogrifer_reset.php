<?php
// FROM HASH: 161c75a13199be769a961a4c89746675
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Transmogrification reset successful');
	$__finalCompiled .= '

<div class="blockMessage blockMessage--success blockMessage--iconic" style="text-align: center">
	' . 'A transmogrification reset imperative was generated and executed successfully. Things should be better now.' . '
	<div>
		<dl class="pairs pairs--inline">
			<dt>' . 'Transmogrification count' . '</dt>
			<dd>' . $__templater->filter($__vars['count'], array(array('number', array()),), true) . '</dd>
		</dl>
	</div>
</div>';
	return $__finalCompiled;
});