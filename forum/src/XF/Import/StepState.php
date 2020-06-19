<?php

namespace XF\Import;

class StepState
{
	public $title = '';
	public $startAfter = 0;
	public $end = null;
	public $imported = 0;
	public $complete = false;

	public $startDate = null;
	public $completeDate = null;

	public $extra = [];

	public function getCompletionOutput()
	{
		if (is_numeric($this->end) && $this->end > 0)
		{
			$percentage = \XF::language()->numberFormat(($this->startAfter / $this->end) * 100, 2);
			$total = \XF::language()->numberFormat($this->imported);

			return "[{$total}] {$percentage}%";
		}
		else if ($this->startAfter)
		{
			return strval($this->startAfter);
		}
		else
		{
			return '';
		}
	}

	public function complete()
	{
		if (!$this->complete)
		{
			$this->completeDate = time();
		}
		$this->complete = true;

		return $this;
	}

	public function resumeIfNeeded()
	{
		if (!$this->complete)
		{
			if (is_int($this->startAfter) && is_int($this->end) && $this->startAfter >= $this->end)
			{
				$this->complete();
			}
		}

		return $this;
	}
}