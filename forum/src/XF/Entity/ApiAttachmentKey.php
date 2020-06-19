<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property string attachment_key
 * @property int create_date
 * @property int user_id
 * @property string temp_hash
 * @property string content_type
 * @property array context
 */
class ApiAttachmentKey extends Entity
{
	public function hasExpectedContext(array $expected, $allowExtra = false)
	{
		$context = $this->context;

		foreach ($expected AS $k => $v)
		{
			if (!isset($context[$k]) || strval($v) !== strval($context[$k]))
			{
				return false;
			}
		}

		if (!$allowExtra)
		{
			foreach ($context AS $k => $null)
			{
				if (!isset($expected[$k]))
				{
					return false;
				}
			}
		}

		return true;
	}

	protected function _preSave()
	{
		if ($this->isInsert())
		{
			$this->attachment_key = substr(\XF::$time . '-' . \XF::generateRandomString(32), 0, 32);
			$this->temp_hash = \XF::generateRandomString(32);

			if (!$this->isChanged('user_id'))
			{
				$this->user_id = \XF::visitor()->user_id;
			}
		}
		elseif ($this->isChanged(['attachment_key', 'temp_hash']))
		{
			throw new \LogicException("Can't change attachment_key or temp_hash");
		}
	}

	protected function _postDelete()
	{
		// TODO: delete any attachments with this temp hash?
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_api_attachment_key';
		$structure->shortName = 'XF:ApiAttachmentKey';
		$structure->primaryKey = 'attachment_key';
		$structure->columns = [
			'attachment_key' => ['type' => self::STR, 'maxlength' => 32, 'required' => true],
			'create_date' => ['type' => self::UINT, 'default' => \XF::$time],
			'user_id' => ['type' => self::UINT, 'required' => true],
			'temp_hash' => ['type' => self::STR, 'maxLength' => 32, 'required' => true],
			'content_type' => ['type' => self::STR, 'maxLength' => 25, 'required' => true],
			'context' => ['type' => self::JSON_ARRAY, 'default' => []],
		];
		$structure->options = [];

		return $structure;
	}
}