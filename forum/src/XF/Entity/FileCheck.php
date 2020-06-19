<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;
use XF\Util\File;
use XF\Util\Json;

/**
 * COLUMNS
 * @property int|null check_id
 * @property int check_date
 * @property string check_state
 * @property string check_hash
 * @property int total_missing
 * @property int total_inconsistent
 * @property int total_checked
 *
 * GETTERS
 * @property array|null results
 */
class FileCheck extends Entity
{
	public function getAbstractedCheckPath($temp = false)
	{
		$tempPrefix = $temp ? 'temp-' : '';

		return "internal-data://file_check/{$tempPrefix}file-check-{$this->check_id}.json";
	}

	/**
	 * @return array|null
	 */
	public function getResults()
	{
		if ($this->check_state == 'pending')
		{
			return null;
		}

		$fs = $this->app()->fs();
		$path = $this->getAbstractedCheckPath();

		if ($fs->has($path))
		{
			$contents = $fs->read($path);
			$json = @json_decode($contents, true);
			return is_array($json) ? $json : null;
		}
		else
		{
			return null;
		}
	}

	protected function _postSave()
	{
		if ($this->isUpdate() && $this->isChanged('check_state') && $this->getExistingValue('check_state') == 'pending')
		{
			// was pending, remove the temp file
			\XF\Util\File::deleteFromAbstractedPath($this->getAbstractedCheckPath(true));
		}
	}

	protected function _postDelete()
	{
		File::deleteFromAbstractedPath($this->getAbstractedCheckPath());
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_file_check';
		$structure->shortName = 'XF:FileCheck';
		$structure->primaryKey = 'check_id';
		$structure->columns = [
			'check_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'check_date' => ['type' => self::UINT, 'default' => \XF::$time],
			'check_state' => ['type' => self::STR, 'default' => 'pending',
				'allowedValues' => ['pending', 'success', 'failure']
			],
			'check_hash' => ['type' => self::STR, 'maxLength' => 64, 'default' => ''],
			'total_missing' => ['type' => self::UINT, 'default' => 0],
			'total_inconsistent' => ['type' => self::UINT, 'default' => 0],
			'total_checked' => ['type' => self::UINT, 'default' => 0]
		];
		$structure->getters = [
			'results' => true
		];
		$structure->relations = [];

		return $structure;
	}
} 