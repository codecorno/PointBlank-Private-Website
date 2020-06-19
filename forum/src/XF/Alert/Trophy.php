<?php

namespace XF\Alert;

use XF\Mvc\Entity\Entity;

class Trophy extends AbstractHandler
{
	public function canViewContent(Entity $entity, &$error = null)
	{
		if (\XF::options()->enableTrophies)
		{
			return true;
		}
		return false;
	}

	public function getOptOutActions()
	{
		$optOuts = [];
		if (\XF::options()->enableTrophies)
		{
			$optOuts[] = 'award';
		}
		return $optOuts;
	}

	public function getOptOutDisplayOrder()
	{
		return 30005;
	}
}