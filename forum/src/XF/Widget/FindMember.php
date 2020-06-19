<?php

namespace XF\Widget;

class FindMember extends AbstractWidget
{
	public function render()
	{
		return $this->renderer('widget_find_member');
	}

	public function getOptionsTemplate()
	{
		return null;
	}
}