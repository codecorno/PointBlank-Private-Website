<?php

namespace XF\AdminSearch;

use XF\Mvc\Entity\AbstractCollection;
use XF\Mvc\Entity\Entity;
use XF\Template\Templater;

class EmailTemplate extends PublicTemplate
{
	protected function getSearchTemplateType()
	{
		return 'email';
	}
}