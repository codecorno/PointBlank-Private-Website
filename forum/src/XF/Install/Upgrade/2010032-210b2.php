<?php

namespace XF\Install\Upgrade;

use XF\Db\Schema\Alter;
use XF\Db\Schema\Create;

class Version2010032 extends AbstractUpgrade
{
	public function getVersionName()
	{
		return '2.1.0 Beta 2';
	}

	public function step1($position, array $stepData)
	{
		// Contents of this have been removed on the basis that they're only relevant if someone upgraded
		// to B1 before B2 was available. As this was a while ago, those affected should have already upgraded to
		// B2 or newer, so repetition of these steps is not necessary.
	}
}