<?php

namespace XF\AdminSearch;

class ThreadPrompt extends AbstractPrompt
{
	protected function getFinderName()
	{
		return 'XF:ThreadPrompt';
	}

	public function getRelatedPhraseGroups()
	{
		return ['thread_prompt'];
	}

	protected function getRouteName()
	{
		return 'thread-prompts/edit';
	}

	public function isSearchable()
	{
		return \XF::visitor()->hasAdminPermission('node');
	}
}