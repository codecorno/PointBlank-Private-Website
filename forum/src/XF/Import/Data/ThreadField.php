<?php

namespace XF\Import\Data;

class ThreadField extends AbstractField
{
	protected $nodeIds = [];

	public function getImportType()
	{
		return 'thread_field';
	}

	public function getEntityShortName()
	{
		return 'XF:ThreadField';
	}

	public function setNodes(array $nodeIds)
	{
		$this->nodeIds = $nodeIds;
	}

	protected function postSave($oldId, $newId)
	{
		parent::postSave($oldId, $newId);

		if ($this->nodeIds)
		{
			$insert = [];
			foreach ($this->nodeIds AS $nodeId)
			{
				$insert[] = [
					'node_id' => $nodeId,
					'field_id' => $newId
				];
			}

			$this->db()->insertBulk('xf_forum_field', $insert, false, false, 'IGNORE');
		}
	}
}