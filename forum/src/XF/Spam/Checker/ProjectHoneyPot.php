<?php

namespace XF\Spam\Checker;

class ProjectHoneyPot extends AbstractDnsBl implements UserCheckerInterface
{
	protected $dateCutOff = 31;
	protected $minThreatLevel = 10;

	public function getType()
	{
		return 'ProjectHoneyPot';
	}

	public function check(\XF\Entity\User $user, array $extraParams = [])
	{
		$key = $this->app()->options()->registrationCheckDnsBl['projectHoneyPotKey'];

		$block = $this->checkIp($key . '.%s.dnsbl.httpbl.org', false);
		if ($block)
		{
			$block = ($block[0] == '127'
				&& intval($block[1]) <= $this->dateCutOff
				&& intval($block[2]) >= $this->minThreatLevel
				&& intval($block[3])
			);
		}
		$this->processDecision($block, true);
	}

	public function submit(\XF\Entity\User $user, array $extraParams = [])
	{
		return;
	}

	public function setDateCutOff($cutOff)
	{
		$this->dateCutOff = $cutOff;
	}

	public function setMinThreatLevel($threatLevel)
	{
		$this->minThreatLevel = $threatLevel;
	}
}