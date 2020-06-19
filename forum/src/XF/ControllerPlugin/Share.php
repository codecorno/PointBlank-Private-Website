<?php

namespace XF\ControllerPlugin;

class Share extends AbstractPlugin
{
	public function actionTooltip($contentUrl, $contentTitle, $tooltipTitle = null, $contentDesc = null)
	{
		$viewParams = [
			'contentUrl' => $contentUrl,
			'contentTitle' => $contentTitle,
			'contentDesc' => $contentDesc,
			'tooltipTitle' => $tooltipTitle ?: \XF::phrase('share_this_content')
		];
		return $this->view('XF:Share\Tooltip', 'share_tooltip', $viewParams);
	}
}