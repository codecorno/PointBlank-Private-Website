<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

abstract class AbstractPrompt extends Repository
{
	abstract protected function getClassIdentifier();

	public function getDefaultGroup()
	{
		$promptGroup = $this->em->create($this->getClassIdentifier() . 'Group');
		$promptGroup->setTrusted('prompt_group_id', 0);
		$promptGroup->setTrusted('display_order', 0);
		$promptGroup->setReadOnly(true);

		return $promptGroup;
	}

	/**
	 * @param bool $getDefault
	 *
	 * @return Finder|\XF\Mvc\Entity\ArrayCollection
	 */
	public function findPromptGroups($getDefault = false)
	{
		$groups = $this->finder($this->getClassIdentifier() . 'Group')
			->order('display_order');

		if ($getDefault)
		{
			$groups = $groups->fetch();

			$defaultGroup = $this->getDefaultGroup();
			$promptGroups = $groups->toArray();
			$promptGroups = $promptGroups + [$defaultGroup];
			$groups = $this->em->getBasicCollection($promptGroups);
		}

		return $groups;
	}

	public function getPromptListData()
	{
		$prompts = $this->findPromptsForList()->fetch();
		$promptGroups = $this->findPromptGroups(true);

		return [
			'promptGroups' => $promptGroups,
			'promptsGrouped' => $prompts->groupBy('prompt_group_id'),
			'promptTotal' => count($prompts)
		];
	}

	/**
	 * @return Finder
	 */
	public function findPromptsForList()
	{
		return $this->finder($this->getClassIdentifier())
			->order(['materialized_order']);
	}

	/**
	 * Rebuilds the 'materialized_order' field in the prompt table,
	 * based on the canonical display_order data in the prompt and prompt_group tables.
	 */
	public function rebuildPromptMaterializedOrder()
	{
		$prompts = $this->finder($this->getClassIdentifier())
			->with('PromptGroup')
			->order([
				'PromptGroup.display_order',
				'display_order'
			]);

		$db = $this->db();
		$ungroupedPrompts = [];
		$updates = [];
		$i = 0;

		foreach ($prompts AS $promptId => $prompt)
		{
			if ($prompt->prompt_group_id)
			{
				if (++$i != $prompt->materialized_order)
				{
					$updates[$promptId] = 'WHEN ' . $db->quote($promptId) . ' THEN ' . $db->quote($i);
				}
			}
			else
			{
				$ungroupedPrompts[$promptId] = $prompt;
			}
		}

		foreach ($ungroupedPrompts AS $promptId => $prompt)
		{
			if (++$i != $prompt->materialized_order)
			{
				$updates[$promptId] = 'WHEN ' . $db->quote($promptId) . ' THEN ' . $db->quote($i);
			}
		}

		if (!empty($updates))
		{
			$structure = $this->em->getEntityStructure($this->getClassIdentifier());
			$table = $structure->table;

			$db->query('
				UPDATE `' . $table . '` SET materialized_order = CASE prompt_id
				' . implode(' ', $updates) . '
				END
				WHERE prompt_id IN(' . $db->quote(array_keys($updates)) . ')
			');
		}
	}
}