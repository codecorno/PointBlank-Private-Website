<?php

return function($__templater, array $__vars, array $__options = [])
{
	$__widget = \XF::app()->widget()->widget('member_wrapper_newest_members', $__options)->render();

	return $__widget;
};