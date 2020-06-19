<?php

namespace XF\Alert;

use XF\Entity\UserAlert;
use XF\Mvc\Entity\Entity;

class User extends AbstractHandler
{
	public function canViewContent(Entity $entity, &$error = null)
	{
		return true;
	}

	public function getOptOutActions()
	{
		return ['following'];
	}

	public function getOptOutDisplayOrder()
	{
		return 30000;
	}
}