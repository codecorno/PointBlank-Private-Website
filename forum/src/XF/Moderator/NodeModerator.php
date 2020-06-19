<?php

namespace XF\Moderator;

class NodeModerator extends AbstractModerator
{
	protected $nodeTitleCache = null;

	/**
	 * Gets the option for the moderator add "choice" page.
	 * @see AbstractModerator::getAddModeratorOption()
	 */
	public function getAddModeratorOption($selectedContentId, $contentType)
	{
		/** @var \XF\Repository\Node $nodeRepo */
		$nodeRepo = \XF::repository('XF:Node');

		$options = [
			'choices' => $nodeRepo->getNodeOptionsData(false),
			'label' => \XF::phrase('forum_moderator') . ':',
			'name' => \XF::escapeString("type_id[$contentType]"),
			'value' => $selectedContentId
		];

		return $options;
	}

	/**
	 * Gets the titles of multiple pieces of content.
	 * @see AbstractModerator::getContentTitles()
	 */
	public function getContentTitles(array $ids)
	{
		if ($this->nodeTitleCache === null)
		{
			/** @var \XF\Repository\Node $nodeRepo */
			$nodeRepo = \XF::repository('XF:Node');

			$nodes = $nodeRepo->getFullNodeList()->toArray();
			$this->nodeTitleCache = [];
			foreach ($nodes AS $key => $node)
			{
				$this->nodeTitleCache[$key] = \XF::phrase('node_type.' . $node['node_type_id']) . " - $node[title]";
			}
		}

		$titles = [];
		foreach ($ids AS $key => $id)
		{
			if (isset($this->nodeTitleCache[$id]))
			{
				$titles[$key] = $this->nodeTitleCache[$id];
			}
		}

		return $titles;
	}
} 