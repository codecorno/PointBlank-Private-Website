<?php

namespace XF\Job;

class TemplatePartialCompile extends AbstractJob
{
	protected $defaultData = [
		'templateIds' => [],
		'position' => 0
	];

	public function run($maxRunTime)
	{
		$s = microtime(true);

		/** @var \XF\Service\Template\Compile $compileService */
		$compileService = $this->app->service('XF:Template\Compile');

		foreach ($this->data['templateIds'] AS $k => $templateId)
		{
			/** @var \XF\Entity\Template $template */
			$template = $this->app->find('XF:Template', $templateId);
			if (!$template)
			{
				continue;
			}

			$template->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', false);

			$needsSave = $template->reparseTemplate(true);
			if ($needsSave)
			{
				// this will recompile
				$template->save();
			}
			else
			{
				$compileService->recompile($template);
			}

			unset($this->data['templateIds'][$k]);

			if ($maxRunTime && microtime(true) - $s > $maxRunTime)
			{
				break;
			}
		}

		if (!$this->data['templateIds'])
		{
			return $this->complete();
		}
		else
		{
			$this->data['position']++;
			return $this->resume();
		}
	}

	public function getStatusMessage()
	{
		$actionPhrase = \XF::phrase('rebuilding');
		$typePhrase = \XF::phrase('templates');
		return sprintf('%s... %s %s', $actionPhrase, $typePhrase, str_repeat('. ', $this->data['position']));
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