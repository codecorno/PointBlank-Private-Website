<?php

namespace XF\Pub\View\Like;

use XF\Mvc\View;

class Like extends View
{
	public function renderJson()
	{
		$isLiked = $this->params['isLiked'];
		$count = $this->params['count'];
		$likes = $this->params['likes'];
		$listUrl = $this->params['listUrl'];

		if ($count)
		{
			$templater = $this->renderer->getTemplater();
			$html = $templater->func('likes', [$count, $likes, $isLiked, $listUrl]);
		}
		else
		{
			$html = '';
		}

		return [
			'isLiked' => $isLiked,
			'text' => $isLiked ? \XF::phrase('unlike') : \XF::phrase('like'),
			'html' => $this->renderer->getHtmlOutputStructure($html)
		];
	}
}