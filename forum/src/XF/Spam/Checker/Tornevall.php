<?php

namespace XF\Spam\Checker;

class Tornevall extends AbstractDnsBl implements UserCheckerInterface
{
	public function getType()
	{
		return 'Tornevall';
	}

	public function check(\XF\Entity\User $user, array $extraParams = [])
	{
		$block = $this->checkIp('%s.dnsbl.tornevall.org');
		$this->processDecision($block, true);
	}

	public function submit(\XF\Entity\User $user, array $extraParams = [])
	{
		return;
	}
}