<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class ErrorLog extends Repository
{
	public function clearErrorLog()
	{
		$this->db()->emptyTable('xf_error_log');
	}
}