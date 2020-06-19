<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null event_listener_id
 * @property string event_id
 * @property int execute_order
 * @property string description
 * @property string callback_class
 * @property string callback_method
 * @property bool active
 * @property string hint
 * @property string addon_id
 *
 * RELATIONS
 * @property \XF\Entity\AddOn AddOn
 * @property \XF\Entity\CodeEvent Event
 */
class CodeEventListener extends Entity
{
	public function getAddOnUniqueKey()
	{
		// this should match XF\AddOn\DataType\CodeEventListener::getAddOnUniqueKeyFromXml
		return "{$this->event_id}-{$this->callback_class}-{$this->callback_method}-{$this->hint}";
	}

	protected function _preSave()
	{
		if ($this->callback_class || $this->callback_method)
		{
			if (!\XF\Util\Php::validateCallbackPhrased($this->callback_class, $this->callback_method, $error))
			{
				$this->error($error, 'callback_method');
			}
		}

		if (
			$this->getOption('check_duplicate')
			&& $this->isChanged(['addon_id', 'event_id', 'callback_class', 'callback_method', 'hint'])
		)
		{
			$listener = \XF::em()->getFinder('XF:CodeEventListener')->where([
				'addon_id' => $this->addon_id,
				'event_id' => $this->event_id,
				'callback_class' => $this->callback_class,
				'callback_method' => $this->callback_method,
				'hint' => $this->hint
			])->fetchOne();
			if ($listener && $listener->event_listener_id != $this->event_listener_id)
			{
				$this->error(\XF::phrase('code_event_listener_definitions_must_be_unique'), 'callback_class');
			}
		}
	}

	protected function _postSave()
	{
		$this->rebuildListenerCache();
	}

	protected function _postDelete()
	{
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
		$structure->table = 'xf_code_event_listener';
		$structure->shortName = 'XF:CodeEventListener';
		$structure->primaryKey = 'event_listener_id';
		$structure->columns = [
			'event_listener_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'event_id' => ['type' => self::STR, 'maxLength' => 50,
				'required' => 'please_select_valid_code_event'
			],
			'execute_order' => ['type' => self::UINT, 'default' => 10],
			'description' => ['type' => self::STR, 'default' => ''],
			'callback_class' => ['type' => self::STR, 'maxLength' => 100,
				'required' => 'please_enter_valid_callback_class'
			],
			'callback_method' => ['type' => self::STR, 'maxLength' => 75,
				'required' => 'please_enter_valid_callback_method'
			],
			'active' => ['type' => self::BOOL, 'default' => 1],
			'hint' => ['type' => self::STR, 'maxLength' => 255, 'default' => ''],
			'addon_id' => ['type' => self::BINARY, 'maxLength' => 50, 'default' => '']
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
			],
			'Event' => [
				'entity' => 'XF:CodeEvent',
				'type' => self::TO_ONE,
				'conditions' => 'event_id',
				'primary' => true
			]
		];
		$structure->options = [
			'check_duplicate' => true
		];

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