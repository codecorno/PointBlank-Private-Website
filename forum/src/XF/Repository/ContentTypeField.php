<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class ContentTypeField extends Repository
{
	/**
	 * @return Finder
	 */
	public function findContentTypeFieldsForList()
	{
		return $this->finder('XF:ContentTypeField')->order(['content_type', 'field_name']);
	}

	public function getContentTypeCacheData()
	{
		$fields = $this->finder('XF:ContentTypeField')->whereAddOnActive()->fetch();
		$output = [];
		foreach ($fields AS $field)
		{
			$output[$field->content_type][$field->field_name] = $field->field_value;
		}

		return $output;
	}

	public function rebuildContentTypeCache()
	{
		$cache = $this->getContentTypeCacheData();
		\XF::registry()->set('contentTypes', $cache);
		return $cache;
	}
}