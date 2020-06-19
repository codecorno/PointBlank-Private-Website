<?php

namespace XF\ControllerPlugin;

use XF\Mvc\Entity\Entity;
use XF\Mvc\FormAction;

class Style extends AbstractPlugin
{
	public function getActiveStyleId()
	{
		$styleId = $this->request->getCookie('edit_style_id', null);
		if ($styleId === null)
		{
			$styleId = \XF::$developmentMode ? 0 : $this->options()->defaultStyleId;
		}
		$styleId = intval($styleId);

		if ($styleId == 0 && !\XF::$developmentMode)
		{
			$styleId = $this->options()->defaultStyleId;
		}

		return $styleId;
	}

	/**
	 * Gets the active editable style.
	 *
	 * @return \XF\Entity\Style
	 */
	public function getActiveEditStyle()
	{
		$styleId = $this->getActiveStyleId();

		if ($styleId == 0)
		{
			/** @var \XF\Repository\Style $styleRepo */
			$styleRepo = $this->repository('XF:Style');
			$style = $styleRepo->getMasterStyle();
		}
		else
		{
			$style = $this->em()->find('XF:Style', $styleId);
		}

		/** @var $style \XF\Entity\Style */
		if (!$style || !$style->canEdit())
		{
			$style = $this->em()->find('XF:Style', $this->options()->defaultStyleId);
		}

		return $style;
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return \XF\Entity\Style
	 */
	public function assertStyleExists($id, $with = null, $phraseKey = null)
	{
		if ($id === 0 || $id === "0")
		{
			/** @var \XF\Repository\Style $styleRepo */
			$styleRepo = $this->repository('XF:Style');
			return $styleRepo->getMasterStyle();
		}

		return $this->controller->assertRecordExists('XF:Style', $id, $with, $phraseKey);
	}
}