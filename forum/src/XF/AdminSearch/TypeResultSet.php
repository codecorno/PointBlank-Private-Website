<?php

namespace XF\AdminSearch;

use XF\Mvc\Entity\AbstractCollection;
use XF\Template\Templater;

class TypeResultSet
{
	protected $contentTypeId;
	protected $handler;
	protected $results;

	public function __construct($contentTypeId, AbstractHandler $handler, array $results)
	{
		$this->contentTypeId = $contentTypeId;
		$this->handler = $handler;
		$this->results = $results;
	}

	public function render(Templater $templater)
	{
		return $this->handler->renderType($this->results, $templater);
	}
}