<?php

namespace XF\Finder;

use XF\Mvc\Entity\Finder;

class SpamTriggerLog extends Finder
{
	public function forContent($contentType, $contentId)
	{
		$this->where('content_type', $contentType)
			->where('content_id', $contentId);

		return $this;
	}
}