<?php

namespace XF\Import\Data;

class Page extends AbstractNode
{
	protected $content = null;

	public function getImportType()
	{
		return 'page';
	}

	public function getEntityShortName()
	{
		return 'XF:Page';
	}

	public function setContent($content)
	{
		$this->content = $content;
	}

	protected function preSave($oldId)
	{
		if ($this->content === null)
		{
			throw new \LogicException("Must call setContent with a non-null value to save a page");
		}
	}

	protected function postSave($oldId, $newId)
	{
		$this->insertTemplate('_page_node.' . $newId, $this->content);
	}
}