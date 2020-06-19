<?php

namespace XF\Service\Banning\DiscouragedIps;

use XF\Service\Banning\AbstractIpXmlImport;

class Import extends AbstractIpXmlImport
{
	protected function getMethod()
	{
		return 'discourageIp';
	}
}