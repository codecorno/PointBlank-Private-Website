<?php

namespace XF\Import\Data;

class ThreadPrefix extends AbstractEmulatedData
{
	protected $title = '';

	protected $nodeIds = [];

	public function getImportType()
	{
		return 'thread_prefix';
	}

	public function getEntityShortName()
	{
		return 'XF:ThreadPrefix';
	}

	public function setTitle($title)
	{
		$this->title = $title;
	}

	public function setNodes(array $nodeIds)
	{
		$this->nodeIds = $nodeIds;
	}

	protected function postSave($oldId, $newId)
	{
		/** @var \XF\Entity\ThreadPrefix $prefix */
		$prefix = $this->em()->find('XF:ThreadPrefix', $newId);
		if ($prefix)
		{
			$this->insertMasterPhrase($prefix->getPhraseName(), $this->title);

			$this->em()->detachEntity($prefix);
		}

		if ($this->nodeIds)
		{
			$insert = [];
			foreach ($this->nodeIds AS $nodeId)
			{
				$insert[] = [
					'node_id' => $nodeId,
					'prefix_id' => $newId
				];
			}

			$this->db()->insertBulk('xf_forum_prefix', $insert, false, false, 'IGNORE');
		}

		/** @var \XF\Repository\ThreadPrefix $repo */
		$repo = $this->repository('XF:ThreadPrefix');

		\XF::runOnce('rebuildThreadPrefixImport', function() use ($repo)
		{
			$repo->rebuildPrefixMaterializedOrder();
			$repo->rebuildPrefixCache();
		});
	}
}