<?php

namespace XF\Cli\Command\Development;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportPermissions extends AbstractImportCommand
{
	protected function getContentTypeDetails()
	{
		return [
			'name' => 'permissions',
			'command' => 'permissions',
			'dir' => 'permissions',
			'entity' => 'XF:Permission'
		];
	}

	protected function getTitleIdMap($typeDir, $addOnId)
	{
		if ($typeDir == 'permission_interface_groups')
		{
			return \XF::db()->fetchPairs("
				SELECT interface_group_id, interface_group_id
				FROM xf_permission_interface_group
				WHERE addon_id = ?
			", $addOnId);
		}
		else
		{
			return \XF::db()->fetchPairs("
				SELECT CONCAT(permission_group_id, '-', permission_id), CONCAT(permission_group_id, '-', permission_id)
				FROM xf_permission
				WHERE addon_id = ?
			", $addOnId);
		}
	}

	protected function deleteRemaining($typeDir, array $map, $entity)
	{
		if ($map && $typeDir == 'permissions')
		{
			$map = array_map(function ($v)
			{
				list($groupId, $permissionId) = explode('-', $v);
				return [$groupId, $permissionId];
			}, $map);
		}

		parent::deleteRemaining($typeDir, $map, $entity);
	}

	public function importData($typeDir, $fileName, $path, $content, $addOnId, array $metadata)
	{
		$id = preg_replace('/\.json$/', '', $fileName);

		if ($typeDir == 'permission_interface_groups')
		{
			\XF::app()->developmentOutput()->import('XF:PermissionInterfaceGroup', $id, $addOnId, $content, $metadata, [
				'import' => true
			]);
		}
		else
		{
			\XF::app()->developmentOutput()->import('XF:Permission', $id, $addOnId, $content, $metadata, [
				'import' => true
			]);
		}

		return $id;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$returnCode = parent::execute($input, $output);
		if (!$returnCode)
		{
			// success
			$groupType = [
				'name' => 'permission interface groups',
				'dir' => 'permission_interface_groups',
				'entity' => 'XF:PermissionInterfaceGroup'
			];
			$this->executeType($groupType, $input, $output);
		}

		return $returnCode;
	}
}