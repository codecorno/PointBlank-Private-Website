<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class TemplateHistory extends Repository
{
	public function getHistoryForMerge(\XF\Entity\Template $template, \XF\Entity\Template $parentTemplate = null)
	{
		if (!$parentTemplate)
		{
			$parentTemplate = $template->ParentTemplate;
		}
		if (!$parentTemplate)
		{
			throw new \InvalidArgumentException("This template does not have a parent version, cannot be used for merge");
		}

		$templateHistoryFinder = $this->finder('XF:TemplateHistory');
		return $templateHistoryFinder
			->where('title', $template->title)
			->where('type', $template->type)
			->where('style_id', $parentTemplate->style_id)
			->where('edit_date', '<=', $template->last_edit_date)
			->order('edit_date', 'DESC')
			->fetchOne();
	}
}