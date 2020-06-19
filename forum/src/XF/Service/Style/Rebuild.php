<?php

namespace XF\Service\Style;

class Rebuild extends \XF\Service\AbstractService
{
	/**
	 * @var \XF\Tree
	 */
	protected $styleTree;

	protected function setupStyleTree()
	{
		if ($this->styleTree)
		{
			return;
		}

		/** @var \XF\Repository\Style $repo */
		$repo = $this->app->em()->getRepository('XF:Style');
		$this->styleTree = $repo->getStyleTree(false);
	}

	public function rebuildFullParentList()
	{
		$this->setupStyleTree();

		$this->db()->beginTransaction();
		$this->_rebuildParentList(0, []);
		$this->db()->commit();
	}

	protected function _rebuildParentList($id, array $path)
	{
		array_unshift($path, $id);

		/** @var \XF\Entity\Style $style */
		$style = $this->styleTree->getData($id);
		if ($style)
		{
			if ($path != $style->parent_list)
			{
				$style->fastUpdate('parent_list', $path);
			}
		}

		foreach ($this->styleTree->childIds($id) AS $childId)
		{
			$this->_rebuildParentList($childId, $path);
		}
	}
}