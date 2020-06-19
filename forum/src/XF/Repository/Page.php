<?php

namespace XF\Repository;

use XF\Mvc\Entity\AbstractCollection;
use XF\Mvc\Entity\Repository;

class Page extends Repository
{
	public function logView(\XF\Entity\Page $page, \XF\Entity\User $user)
	{
		// TODO: update batching?
		$page->fastUpdate('view_count', $page->view_count + 1);
	}
}