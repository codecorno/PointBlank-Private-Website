<?php

return function($__templater, array $__vars, array $__options = [])
{
	$__widget = \XF::app()->widget()->widget('member_wrapper_find_member', $__options)->render();

	return $__widget;
};