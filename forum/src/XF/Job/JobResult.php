<?php

namespace XF\Job;

class JobResult
{
	public $completed;
	public $jobId;
	public $data;
	public $continueDate;
	public $statusMessage;
	public $canCancel;

	public function __construct($completed, $jobId, array $data = [], $statusMessage = '', $canCancel = false)
	{
		$this->completed = (bool)$completed;
		$this->jobId = $jobId;
		$this->data = $data;
		$this->statusMessage = $statusMessage;
		$this->canCancel = $canCancel;
	}
}