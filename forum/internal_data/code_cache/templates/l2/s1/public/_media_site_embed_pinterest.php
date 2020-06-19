<?php
// FROM HASH: 7ae2b1bc5a14f167df9075d0df02bafd
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->setPageParam('jsState.pinterest', true);
	$__finalCompiled .= '

<div class="bbMediaJustifier">
	<a data-pin-do="embedPin"
		data-pin-width="large"
		href="https://www.pinterest.com/pin/' . $__templater->escape($__vars['idDigits']) . '/">
		<i class="fab fa-pinterest-square" aria-hidden="true"></i> https://www.pinterest.com/pin/' . $__templater->escape($__vars['idDigits']) . '/</a>
</div>';
	return $__finalCompiled;
});