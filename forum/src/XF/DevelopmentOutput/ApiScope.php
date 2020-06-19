<?php

namespace XF\DevelopmentOutput;

use XF\Mvc\Entity\Entity;
use XF\Util\Json;

class ApiScope extends AbstractHandler
{
	protected function getTypeDir()
	{
		return 'api_scopes';
	}
	
	public function export(Entity $scope)
	{
		if (!$this->isRelevant($scope))
		{
			return true;
		}

		$fileName = $this->getFileName($scope);

		$keys = [
			'api_scope_id',
		];
		$json = $this->pullEntityKeys($scope, $keys);

		return $this->developmentOutput->writeFile(
			$this->getTypeDir(), $scope->addon_id, $fileName, Json::jsonEncodePretty($json)
		);
	}

	public function import($name, $addOnId, $contents, array $metadata, array $options = [])
	{
		$json = json_decode($contents, true);

		$permission = $this->getEntityForImport($name, $addOnId, $json, $options);

		$permission->bulkSet($json);
		$permission->addon_id = $addOnId;
		$permission->save();
		// this will update the metadata itself

		return $permission;
	}

	/**
	 * @param $name
	 * @param $addOnId
	 * @param $json
	 * @param array $options
	 * @return null|Entity
	 */
	protected function getEntityForImport($name, $addOnId, $json, array $options)
	{
		$entity = \XF::em()->find($this->shortName, $json['api_scope_id']);
		if (!$entity)
		{
			$entity = \XF::em()->create($this->shortName);
		}

		$entity = $this->prepareEntityForImport($entity, $options);

		return $entity;
	}

	protected function getFileName(Entity $permission, $new = true)
	{
		$scopeId = $new ? $permission->getValue('api_scope_id') : $permission->getExistingValue('api_scope_id');
		$scopeId = str_replace(':', '-', $scopeId);

		return "{$scopeId}.json";
	}
}