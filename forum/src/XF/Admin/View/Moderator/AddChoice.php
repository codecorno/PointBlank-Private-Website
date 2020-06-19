<?php

namespace XF\Admin\View\Moderator;

class AddChoice extends \XF\Mvc\View
{
	public function renderHtml()
	{
		$this->params['typeChoices'] = [];

		if (!empty($this->params['typeHandlers']))
		{
			foreach ($this->params['typeHandlers'] AS $contentType => $handler)
			{
				$handlerClass = \XF::extendClass($handler);

				if (!class_exists($handlerClass))
				{
					continue;
				}

				/** @var \XF\Moderator\AbstractModerator $contentHandler */
				$contentHandler = new $handlerClass();

				$selectedContentId = (isset($this->params['typeId'][$contentType]) ? $this->params['typeId'][$contentType] : 0);
				$this->params['typeChoices'][$contentType] = $contentHandler->getAddModeratorOption($selectedContentId, $contentType);
			}
		}
	}
} 