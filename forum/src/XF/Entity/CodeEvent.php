<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property string event_id
 * @property string description
 * @property string addon_id
 *
 * RELATIONS
 * @property \XF\Entity\AddOn AddOn
 */
class CodeEvent extends Entity
{
	protected function _postSave()
	{
		if ($this->isUpdate() && $this->isChanged('event_id'))
		{
			$this->db()->update('xf_code_event_listener', [
				'event_id' => $this->event_id
			], 'event_id = ?', $this->getExistingValue('event_id'));
			$this->rebuildListenerCache();
		}
	}

	protected function _postDelete()
	{
		$this->db()->delete('xf_code_event_listener', 'event_id = ?', $this->event_id);
		$this->rebuildListenerCache();
	}

	protected function rebuildListenerCache()
	{
		$repo = $this->getListenerRepo();

		\XF::runOnce('codeEventListenerCacheRebuild', function() use ($repo)
		{
			$repo->rebuildListenerCache();
		});
	}

	protected function _setupDefaults()
	{
		/** @var \XF\Repository\AddOn $addOnRepo */
		$addOnRepo = $this->_em->getRepository('XF:AddOn');
		$this->addon_id = $addOnRepo->getDefaultAddOnId();
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_code_event';
		$structure->shortName = 'XF:CodeEvent';
		$structure->primaryKey = 'event_id';
		$structure->columns = [
			'event_id' => ['type' => self::STR, 'maxLength' => 50,
				'required' => 'please_enter_valid_code_event_id',
				'unique' => 'code_event_ids_must_be_unique',
				'match' => 'alphanumeric'
			],
			'description' => ['type' => self::STR, 'default' => ''],
			'addon_id' => ['type' => self::BINARY, 'maxLength' => 50, 'required' => true]
		];
		$structure->behaviors = [
			'XF:DevOutputWritable' => []
		];
		$structure->getters = [];
		$structure->relations = [
			'AddOn' => [
				'entity' => 'XF:AddOn',
				'type' => self::TO_ONE,
				'conditions' => 'addon_id',
				'primary' => true
			]
		];
		$structure->options = [];

		return $structure;
	}

	/**
	 * @return \XF\Repository\CodeEventListener
	 */
	protected function getListenerRepo()
	{
		return $this->repository('XF:CodeEventListener');
	}
}