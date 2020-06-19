<?php

namespace XF\Service\Template;

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

	public function rebuildFullTemplateMap()
	{
		$this->setupStyleTree();

		$templatesGrouped = [];
		$templateRes = $this->db()->query("
			SELECT template_id, type, title, style_id
			FROM xf_template
		");
		while ($template = $templateRes->fetch())
		{
			$templatesGrouped[$template['style_id']][$template['type']][$template['title']] = $template['template_id'];
		}

		$this->db()->beginTransaction();
		$this->db()->delete('xf_template_map', null); // not using emptyTable for transaction safety
		$this->_rebuildTemplateMap(0, [], $templatesGrouped);
		$this->db()->commit();
	}

	public function rebuildTemplateMapForTitle($type, $title)
	{
		$this->setupStyleTree();

		$templatesGrouped = [];
		$templateRes = $this->db()->query("
			SELECT template_id, type, title, style_id
			FROM xf_template
			WHERE type = ? AND title = ?
		", [$type, $title]);
		while ($template = $templateRes->fetch())
		{
			$templatesGrouped[$template['style_id']][$template['type']][$template['title']] = $template['template_id'];
		}

		$this->db()->beginTransaction();
		$this->db()->delete('xf_template_map', 'type = ? AND title = ?', [$type, $title]);
		$this->_rebuildTemplateMap(0, [], $templatesGrouped);
		$this->db()->commit();
	}

	protected function _rebuildTemplateMap($id, array $map, array $templateList)
	{
		if (isset($templateList[$id]))
		{
			foreach ($templateList[$id] AS $type => $templates)
			{
				foreach ($templates AS $title => $templateId)
				{
					$map[$type][$title] = $templateId;
				}
			}
		}

		$sql = [];
		foreach ($map AS $type => $templates)
		{
			foreach ($templates AS $title => $templateId)
			{
				$sql[] = [
					'type' => $type,
					'title' => $title,
					'style_id' => $id,
					'template_id' => $templateId
				];
			}
		}
		if ($sql)
		{
			$this->db()->insertBulk('xf_template_map', $sql);
		}

		foreach ($this->styleTree->childIds($id) AS $childId)
		{
			$this->_rebuildTemplateMap($childId, $map, $templateList);
		}
	}
}