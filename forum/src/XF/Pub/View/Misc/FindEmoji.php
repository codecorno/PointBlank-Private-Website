<?php

namespace XF\Pub\View\Misc;

use XF\Mvc\View;

class FindEmoji extends View
{
	public function renderJson()
	{
		$results = [];
		foreach ($this->params['results'] AS $result)
		{
			$results[] = [
				'id' => $result['shortname'],
				'iconHtml' => $result['html'],
				'html' => $result['html'],
				'text' => $result['shortname'],
				'desc' => isset($result['name']) ? $result['name'] : '',
				'q' => $this->params['q']
			];
		}

		return [
			'results' => $results,
			'q' => $this->params['q']
		];
	}
}