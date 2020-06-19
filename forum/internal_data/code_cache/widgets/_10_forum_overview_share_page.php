<?php

return function($__templater, array $__vars, array $__options = [])
{
	$__widget = \XF::app()->widget()->widget('forum_overview_share_page', $__options)->render();

	return $__widget;
};