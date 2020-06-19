<?php
// FROM HASH: 7f1e17ce29f3f5071fbac2c3c66d3238
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<div>
	' . $__templater->button('
		' . 'Cancel' . '
	', array(
		'href' => $__vars['endpoint'] . '?cmd=_manage-paylist',
		'target' => '_blank',
	), '', array(
	)) . '
</div>';
	return $__finalCompiled;
});