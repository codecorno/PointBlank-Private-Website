<?php

namespace XF\Import\Data;

class HelpPage extends AbstractEmulatedData
{
	protected $title = null;
	protected $description = null;
	protected $content = null;

	public function getImportType()
	{
		return 'help_page';
	}

	public function getEntityShortName()
	{
		return 'XF:HelpPage';
	}

	public function setTitle($title)
	{
		$this->title = $title;
	}

	public function setDescription($description)
	{
		$this->description = $description;
	}

	public function setContent($content)
	{
		$this->content = $content;
	}

	protected function preSave($oldId)
	{
		if ($this->title === null)
		{
			throw new \LogicException("Must call setTitle with a non-null value to save a help page");
		}

		if ($this->content === null)
		{
			throw new \LogicException("Must call setContent with a non-null value to save a help page");
		}
	}

	protected function postSave($oldId, $newId)
	{
		$this->insertMasterPhrase('help_page_title.' . $newId, $this->title);
		$this->insertMasterPhrase('help_page_desc.' . $newId, $this->description ?: '');
		$this->insertTemplate('_help_page_' . $newId, $this->content);
	}
}