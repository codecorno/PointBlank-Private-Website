<?php

namespace XF\Service\Language;

class Rebuild extends \XF\Service\AbstractService
{
	/**
	 * @var \XF\Tree
	 */
	protected $languageTree;

	protected function setupLanguageTree()
	{
		if ($this->languageTree)
		{
			return;
		}

		/** @var \XF\Repository\Language $repo */
		$repo = $this->app->em()->getRepository('XF:Language');
		$this->languageTree = $repo->getLanguageTree(false);
	}

	public function rebuildFullParentList()
	{
		$this->setupLanguageTree();

		$this->db()->beginTransaction();
		$this->_rebuildParentList(0, []);
		$this->db()->commit();
	}

	protected function _rebuildParentList($id, array $path)
	{
		array_unshift($path, $id);

		/** @var \XF\Entity\Language $language */
		$language = $this->languageTree->getData($id);
		if ($language)
		{
			if ($path != $language->parent_list)
			{
				$language->fastUpdate('parent_list', $path);
			}
		}

		foreach ($this->languageTree->childIds($id) AS $childId)
		{
			$this->_rebuildParentList($childId, $path);
		}
	}
}