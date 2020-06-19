<?php
// FROM HASH: ff899b2eabdbaaffe7ce0692b513c19f
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= 'The forum is currently closed. Only administrators may access the forum.' . '<br />
<a href="' . $__templater->func('link_type', array('admin', 'options/groups', array('group_id' => 'boardActive', ), ), true) . '">' . 'Reopen via admin control panel' . '</a>';
	return $__finalCompiled;
});