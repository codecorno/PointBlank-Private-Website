<?php

namespace XF\Widget;

class VisitorPanel extends AbstractWidget
{
	public function render()
	{
		return $this->renderer('widget_visitor_panel');
	}

	public function getOptionsTemplate()
	{
		return null;
	}
}