<?php

namespace XF\Service\Banning\DiscouragedIps;

class Export extends \XF\Service\Banning\Ips\Export
{
	public function getRootName()
	{
		return 'discouraged_ips';
	}
}