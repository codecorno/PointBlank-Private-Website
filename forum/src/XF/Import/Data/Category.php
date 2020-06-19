<?php

namespace XF\Import\Data;

class Category extends AbstractNode
{
	public function getImportType()
	{
		return 'category';
	}

	public function getEntityShortName()
	{
		return 'XF:Category';
	}
}