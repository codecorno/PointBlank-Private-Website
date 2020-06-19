<?php

namespace XF\Cli\Command\Rebuild;

class RebuildUsers extends AbstractRebuildCommand
{
	protected function getRebuildName()
	{
		return 'users';
	}

	protected function getRebuildDescription()
	{
		return 'Rebuilds user counters and caches.';
	}

	protected function getRebuildClass()
	{
		return 'XF:User';
	}
}