<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class Editor extends Repository
{
	/**
	 * @return Finder
	 */
	public function findEditorDropdownsForList()
	{
		return $this->finder('XF:EditorDropdown')
			->setDefaultOrder('display_order');
	}

	public function getEditorDropdownCache()
	{
		$db = $this->db();

		$output = $db->fetchAllKeyed('
			SELECT cmd, icon, buttons
			FROM xf_editor_dropdown
			WHERE active = 1
			ORDER BY display_order
		', 'cmd');

		$output = array_map(function($row)
		{
			$row['buttons'] = (array)json_decode($row['buttons'], true);
			return $row;
		}, $output);

		return $output;
	}

	public function rebuildEditorDropdownCache()
	{
		$dropdownCache = $this->getEditorDropdownCache();

		/** @var Option $optionRepo */
		$optionRepo = $this->repository('XF:Option');
		$optionRepo->updateOption('editorDropdownConfig', $dropdownCache);
	}

	public function getToolbarTypes()
	{
		return [
			'toolbarButtons' => [
				'title' => \XF::phrase('large_toolbar'),
				'description' => \XF::phrase('large_toolbar_desc')
			],
			'toolbarButtonsMD' => [
				'title' => \XF::phrase('medium_toolbar'),
				'description' => \XF::phrase('medium_toolbar_desc')
			],
			'toolbarButtonsSM' => [
				'title' => \XF::phrase('small_toolbar'),
				'description' => \XF::phrase('small_toolbar_desc')
			],
			'toolbarButtonsXS' => [
				'title' => \XF::phrase('extra_small_toolbar'),
				'description' => \XF::phrase('extra_small_toolbar_desc')
			]
		];
	}
}