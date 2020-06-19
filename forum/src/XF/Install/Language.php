<?php

namespace XF\Install;

class Language extends \XF\Language
{
	protected function loadPhrases()
	{
		try
		{
			parent::loadPhrases();
		}
		catch (\Exception $e)
		{
			$this->phrasesToLoad = [];
		}
	}

	protected function loadPhraseGroup($group)
	{
		return false;
	}
}