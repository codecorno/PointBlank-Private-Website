<?php

namespace XF\Job;

class TemplateMerge extends AbstractJob
{
	protected $defaultData = [
		'templateIds' => [],
		'success' => [],
		'skipped' => [],
		'count' => 0,
		'total' => null
	];

	public function run($maxRunTime)
	{
		$start = microtime(true);
		$templateIds = $this->data['templateIds'];

		if ($this->data['total'] === null)
		{
			$this->data['total'] = count($templateIds);
		}

		if (!$templateIds)
		{
			return $this->complete();
		}

		foreach ($templateIds AS $key => $templateId)
		{
			$merged = $this->mergeTemplate($templateId);
			if ($merged)
			{
				$this->data['success'][] = $templateId;
			}
			else
			{
				$this->data['skipped'][] = $templateId;
			}

			$this->data['count']++;
			unset($templateIds[$key]);

			if (microtime(true) - $start >= $maxRunTime)
			{
				break;
			}
		}

		if (!$templateIds)
		{
			return $this->complete();
		}

		$this->data['templateIds'] = $templateIds;
		return $this->resume();
	}

	protected function mergeTemplate($templateId)
	{
		$em = $this->app->em();

		/** @var \XF\Entity\Template $template */
		$template = $em->find('XF:Template', $templateId, 'Style');
		if (!$template->style_id)
		{
			return false;
		}

		$parentTemplate = $template->ParentTemplate;
		if (!$parentTemplate)
		{
			return false;
		}

		if (!$parentTemplate->last_edit_date || $parentTemplate->last_edit_date < $template->last_edit_date)
		{
			return false;
		}

		/** @var \XF\Repository\TemplateHistory $historyRepo */
		$historyRepo = $em->getRepository('XF:TemplateHistory');
		$previousVersion = $historyRepo->getHistoryForMerge($template, $parentTemplate);
		if (!$previousVersion)
		{
			return false;
		}

		$diff = new \XF\Diff3();

		$final = $diff->mergeToFinal(
			$template->template, $previousVersion->template, $parentTemplate->template
		);
		if ($final === false)
		{
			return false;
		}

		$template->template = $final;
		$template->last_edit_date = \XF::$time;

		// even if the template isn't changed we should
		// make the custom template the same version so
		// it is no longer outdated.
		if (!$template->isChanged('template'))
		{
			$template->updateVersionId();
		}

		return $template->save();
	}

	public function getStatusMessage()
	{
		$actionPhrase = \XF::phrase('merging');
		$typePhrase = \XF::phrase('templates');
		$total = $this->data['total'];
		if ($total)
		{
			return sprintf('%s... %s (%d/%d)', $actionPhrase, $typePhrase, $this->data['count'], $total);
		}
		else
		{
			return sprintf('%s... %s (%d)', $actionPhrase, $typePhrase, $this->data['count']);
		}
	}

	public function canCancel()
	{
		return false;
	}

	public function canTriggerByChoice()
	{
		return false;
	}
}