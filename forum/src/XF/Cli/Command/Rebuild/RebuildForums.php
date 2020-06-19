<?php

namespace XF\Cli\Command\Rebuild;

class RebuildForums extends AbstractRebuildCommand
{
	protected function getRebuildName()
	{
		return 'forums';
	}

	protected function getRebuildDescription()
	{
		return 'Rebuilds forum counters.';
	}

	protected function getRebuildClass()
	{
		return 'XF:Forum';
	}
}