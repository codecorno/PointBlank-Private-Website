<?php

namespace XF\Widget;

class SharePage extends AbstractWidget
{
	protected $defaultOptions = [
		'iconic' => true
	];

	public function render()
	{
		return $this->renderer('widget_share_page');
	}

	public function verifyOptions(\XF\Http\Request $request, array &$options, &$error = null)
	{
		$options = $request->filter([
			'iconic' => 'bool'
		]);
		return true;
	}
}