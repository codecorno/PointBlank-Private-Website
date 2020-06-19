<?php

namespace XF\Install\Upgrade;

class Version1052031 extends AbstractUpgrade
{
	public function getVersionName()
	{
		return '1.5.20 Beta 1';
	}

	public function step1()
	{
		// note this is deliberately missing as if you get this far, we'll add the new stuff in 2.0.6 anyway.
	}
}