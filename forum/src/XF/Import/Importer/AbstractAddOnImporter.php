<?php

namespace XF\Import\Importer;

abstract class AbstractAddOnImporter extends AbstractImporter
{
	/**
	 * @var \XF\Db\Mysqli\Adapter
	 */
	protected $sourceDb;

	/**
	 * @var \XF\Import\Log
	 */
	protected $forumLog;

	/**
	 * Add-on imports have two import logs. One for the initial
	 * forum import and a second for this import.
	 *
	 * This method should be defined and should return false
	 * if the $importType provided is specific to this add-on
	 * import rather than the forum import.
	 *
	 * Either match against a specific prefix, or a whitelist of types.
	 *
	 * @param $importType
	 *
	 * @return bool
	 */
	abstract protected function isForumType($importType);

	public function typeMap($type)
	{
		if ($this->isForumType($type))
		{
			return $this->forumLog->typeMap($type);
		}
		else
		{
			return $this->dataManager->typeMap($type);
		}
	}

	public function lookup($type, $oldIds)
	{
		if ($this->isForumType($type))
		{
			return $this->forumLog->lookup($type, $oldIds);
		}
		else
		{
			return $this->dataManager->lookup($type, $oldIds);
		}
	}

	public function lookupId($type, $id, $default = false)
	{
		if ($this->isForumType($type))
		{
			return $this->forumLog->lookupId($type, $id, $default);
		}
		else
		{
			return $this->dataManager->lookupId($type, $id, $default);
		}
	}
}