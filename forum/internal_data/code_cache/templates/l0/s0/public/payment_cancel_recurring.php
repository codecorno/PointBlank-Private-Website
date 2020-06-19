<?php
// FROM HASH: 4409e348abcd6cdc366b2466d3700a91
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<div>
	' . $__templater->button('
		' . 'Cancel' . '
	', array(
		'href' => $__templater->func('link', array('purchase/cancel-recurring', null, array('request_key' => $__vars['purchaseRequest']['request_key'], ), ), false),
		'overlay' => 'true',
		'target' => '_blank',
	), '', array(
	)) . '
</div>';
	return $__finalCompiled;
});