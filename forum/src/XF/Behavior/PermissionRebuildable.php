<?php

namespace XF\Behavior;

use XF\Mvc\Entity\Behavior;

/**
 * Class PermissionRebuildable
 *
 * @package XF\Behavior
 */
class PermissionRebuildable extends Behavior
{
	/**
	 * @return array
	 */
	protected function getDefaultConfig()
	{
		return [
			'permissionContentType' => null
		];
	}

	/**
	 * @return array
	 */
	protected function getDefaultOptions()
	{
		return [
			'rebuildCache' => true,
		];
	}

	/**
	 *
	 */
	public function postSave()
	{
		if ($this->getOption('rebuildCache'))
		{
			if (
				$this->config['permissionContentType']
				&& ($this->entity->isInsert())
			)
			{
				$this->app()->jobManager()->enqueueUnique('permissionRebuild', 'XF:PermissionRebuild');
			}
		}
	}

	/**
	 *
	 */
	public function postDelete()
	{
		if ($this->getOption('rebuildCache'))
		{
			if ($this->config['permissionContentType'])
			{
				$this->entity->db()->delete(
					'xf_permission_entry_content',
					'content_type = ? AND content_id = ?',
					[$this->config['permissionContentType'], $this->id()]
				);

				$this->app()->jobManager()->enqueueUnique('permissionRebuild', 'XF:PermissionRebuild');
			}
		}
	}
}