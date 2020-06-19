<?php

namespace XF\Admin\View\Tools;

class RunJob extends \XF\Mvc\View
{
	public function renderJson()
	{
		return [
			'html' => null,
			'jobRunner' => [
				'canCancel' => $this->params['canCancel'],
				'status' => $this->params['status'],
				'jobId' => $this->params['jobId'],
				'redirect' => $this->params['redirect'],
				'onlyIds' => $this->params['onlyIds'],
			]
		];
	}
}