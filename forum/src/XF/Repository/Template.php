<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class Template extends Repository
{
	/**
	 * @param \XF\Entity\Style $style
	 * @param string|null $type
	 *
	 * @return \XF\Finder\TemplateMap
	 */
	public function findEffectiveTemplatesInStyle(\XF\Entity\Style $style, $type = null)
	{
		/** @var \XF\Finder\TemplateMap $finder */
		$finder = $this->finder('XF:TemplateMap');
		$finder
			->where('style_id', $style->style_id)
			->with('Template', true)
			->orderTitle()
			->pluckFrom('Template', 'template_id');

		if ($type !== null)
		{
			$finder->where('type', $type);
		}

		return $finder;
	}

	/**
	 * @param \XF\Entity\Style $style
	 * @param string           $title
	 * @param string           $type
	 *
	 * @return \XF\Finder\TemplateMap
	 */
	public function findEffectiveTemplateInStyle(\XF\Entity\Style $style, $title, $type)
	{
		/** @var \XF\Finder\TemplateMap $finder */
		$finder = $this->finder('XF:TemplateMap');
		$finder
			->where('style_id', $style->style_id)
			->where('type', $type)
			->where('title', $title)
			->pluckFrom('Template', 'template_id')
		;

		return $finder;
	}

	/**
	 * @param \XF\Entity\Style $style
	 * @param string|null $type
	 *
	 * @return \XF\Finder\Template
	 */
	public function findTemplatesInStyle(\XF\Entity\Style $style, $type = null)
	{
		/** @var \XF\Finder\Template $templateFinder */
		$templateFinder = $this->finder('XF:Template');
		$templateFinder
			->where('style_id', $style->style_id)
			->order('type')
			->orderTitle();

		if ($type !== null)
		{
			$templateFinder->where('type', $type);
		}

		return $templateFinder;
	}

	public function countOutdatedTemplates()
	{
		return count($this->getBaseOutdatedTemplateData());
	}

	public function getOutdatedTemplates()
	{
		$data = $this->getBaseOutdatedTemplateData();
		$templateIds = array_keys($data);

		if (!$templateIds)
		{
			return [];
		}

		$templates = $this->em->findByIds('XF:Template', $templateIds);

		$output = [];
		foreach ($data AS $templateId => $outdated)
		{
			if (!isset($templates[$templateId]))
			{
				continue;
			}

			$outdated['template'] = $templates[$templateId];
			$output[$templateId] = $outdated;
		}

		return $output;
	}

	protected function getBaseOutdatedTemplateData()
	{
		$db = $this->db();

		return $db->fetchAllKeyed('
			SELECT template.template_id,
				parent.version_string AS parent_version_string,
				parent.last_edit_date AS parent_last_edit_date,
				IF(parent.version_id > template.version_id, 1, 0) AS outdated_by_version,
				IF(parent.last_edit_date > 0 AND parent.last_edit_date >= template.last_edit_date, 1, 0) AS outdated_by_date
			FROM xf_template AS template
			INNER JOIN xf_style AS style ON (style.style_id = template.style_id)
			INNER JOIN xf_template_map AS map ON (
				map.style_id = style.parent_id
				AND map.type = template.type
				AND map.title = template.title
			)
			INNER JOIN xf_template AS parent ON (map.template_id = parent.template_id
				AND (
					(parent.last_edit_date > 0 AND parent.last_edit_date >= template.last_edit_date)
					OR parent.version_id > template.version_id
				)
			)
			WHERE template.style_id > 0
			ORDER BY template.title
		', 'template_id');
	}

	public function getTemplateTypes(\XF\Entity\Style $style = null)
	{
		$types = [
			'public' => \XF::phrase('public'),
			'email' => \XF::phrase('email')
		];
		if (($style && !$style->style_id) || (!$style && \XF::$developmentMode))
		{
			$types['admin'] = \XF::phrase('admin');
		}

		return $types;
	}

	public function pruneEditHistory($cutOff = null)
	{
		if ($cutOff === null)
		{
			$logLength = $this->options()->templateHistoryLength;
			if (!$logLength)
			{
				return 0;
			}

			$cutOff = \XF::$time - 86400 * $logLength;
		}

		return $this->db()->delete('xf_template_history', 'log_date < ?', $cutOff);
	}
}