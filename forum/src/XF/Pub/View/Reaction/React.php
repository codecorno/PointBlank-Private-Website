<?php

namespace XF\Pub\View\Reaction;

use XF\Mvc\View;

class React extends View
{
	public function renderJson()
	{
		$content = $this->params['content'];
		$link = $this->params['link'];

		$templater = $this->renderer->getTemplater();
		$html = $this->renderTemplate('public:reaction_react', $this->getParams());
		$reactionList = $templater->func('reactions', [$content, $link]);

		return [
			'html' => $this->renderer->getHtmlOutputStructure($html),
			'reactionList' => $this->renderer->getHtmlOutputStructure($reactionList)
		];
	}
}