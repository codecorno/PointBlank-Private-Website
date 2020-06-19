<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property string entry_id
 * @property string cron_class
 * @property string cron_method
 * @property array run_rules
 * @property bool active
 * @property int next_run
 * @property string addon_id
 *
 * GETTERS
 * @property \XF\Phrase title
 *
 * RELATIONS
 * @property \XF\Entity\AddOn AddOn
 * @property \XF\Entity\Phrase MasterTitle
 */
class CronEntry extends Entity
{
	public function canEdit()
	{
		if (!$this->addon_id || $this->isInsert())
		{
			return true;
		}
		else
		{
			return \XF::$developmentMode;
		}
	}

	public function hasCallback()
	{
		return method_exists($this->cron_class, $this->cron_method);
	}

	/**
	 * @return \XF\Phrase
	 */
	public function getTitle()
	{
		return \XF::phrase($this->getPhraseName());
	}

	public function getPhraseName()
	{
		return 'cron_entry.' . $this->entry_id;
	}

	public function getMasterPhrase()
	{
		$phrase = $this->MasterTitle;
		if (!$phrase)
		{
			$phrase = $this->_em->create('XF:Phrase');
			$phrase->addon_id = $this->_getDeferredValue(function() { return $this->addon_id; });
			$phrase->title = $this->_getDeferredValue(function() { return $this->getPhraseName(); });
			$phrase->language_id = 0;
		}

		return $phrase;
	}

	public function calculateNextRun()
	{
		/** @var \XF\Service\CronEntry\CalculateNextRun $service */
		$service = $this->app()->service('XF:CronEntry\CalculateNextRun');
		return $service->calculateNextRunTime($this->run_rules);
	}

	protected function verifyRunRules(array &$rules)
	{
		$filterTypes = ['dom', 'dow', 'hours', 'minutes'];

		foreach ($filterTypes AS $type)
		{
			if (!isset($rules[$type]))
			{
				continue;
			}

			$typeRules = $rules[$type];
			if (!is_array($typeRules))
			{
				$typeRules = [];
			}

			$typeRules = array_map('intval', $typeRules);
			$typeRules = array_unique($typeRules);
			sort($typeRules, SORT_NUMERIC);

			$rules[$type] = $typeRules;
		}

		return true;
	}

	protected function _preSave()
	{
		if ($this->cron_class || $this->cron_method)
		{
			if (!\XF\Util\Php::validateCallbackPhrased($this->cron_class, $this->cron_method, $error))
			{
				$this->error($error, 'cron_method');
			}
		}

		if ($this->active)
		{
			if (!is_array($this->run_rules))
			{
				$this->run_rules = [];
			}

			$this->set('next_run', $this->calculateNextRun());
		}
		else
		{
			$this->set('next_run', 0x7FFFFFFF); // waay in future
		}
	}

	protected function _postSave()
	{
		$this->rebuildNextRunTime();

		if ($this->isUpdate())
		{
			if ($this->isChanged(['addon_id', 'entry_id']))
			{
				$phrase = $this->getExistingRelation('MasterTitle');
				if ($phrase)
				{
					$writeDevOutput = $this->getBehavior('XF:DevOutputWritable')->getOption('write_dev_output');
					$phrase->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', $writeDevOutput);

					$phrase->addon_id = $this->addon_id;
					$phrase->title = $this->getPhraseName();
					$phrase->save();
				}
			}
		}
	}

	protected function _postDelete()
	{
		$this->rebuildNextRunTime();

		$phrase = $this->MasterTitle;
		if ($phrase)
		{
			$writeDevOutput = $this->getBehavior('XF:DevOutputWritable')->getOption('write_dev_output');
			$phrase->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', $writeDevOutput);

			$phrase->delete();
		}
	}

	protected function rebuildNextRunTime()
	{
		/** @var \XF\Service\CronEntry\CalculateNextRun $runService */
		$runService = $this->app()->service('XF:CronEntry\CalculateNextRun');

		\XF::runOnce('cronNextRunTimeRebuild', function() use ($runService)
		{
			$runService->updateMinimumNextRunTime();
		});
	}

	protected function _setupDefaults()
	{
		/** @var \XF\Repository\AddOn $addOnRepo */
		$addOnRepo = $this->_em->getRepository('XF:AddOn');
		$this->addon_id = $addOnRepo->getDefaultAddOnId();

		$this->run_rules = [
			'day_type' => 'dom',
			'dom' => ['-1'],
			'hours' => ['0'],
			'minutes' => ['0']
		];
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_cron_entry';
		$structure->shortName = 'XF:CronEntry';
		$structure->primaryKey = 'entry_id';
		$structure->columns = [
			'entry_id' => ['type' => self::STR, 'maxLength' => 25,
				'required' => 'please_enter_valid_cron_entry_id',
				'unique' => 'cron_entry_ids_must_be_unique',
				'match' => 'alphanumeric'
			],
			'cron_class' => ['type' => self::STR, 'maxLength' => 100,
				'required' => 'please_enter_valid_callback_class'
			],
			'cron_method' => ['type' => self::STR, 'maxLength' => 75,
				'required' => 'please_enter_valid_callback_method'
			],
			'run_rules' => ['type' => self::JSON_ARRAY, 'required' => true],
			'active' => ['type' => self::BOOL, 'default' => true],
			'next_run' => ['type' => self::UINT, 'default' => 0],
			'addon_id' => ['type' => self::BINARY, 'maxLength' => 50, 'default' => '']
		];
		$structure->behaviors = [
			'XF:DevOutputWritable' => []
		];
		$structure->getters = [
			'title' => true
		];
		$structure->relations = [
			'AddOn' => [
				'entity' => 'XF:AddOn',
				'type' => self::TO_ONE,
				'conditions' => 'addon_id',
				'primary' => true
			],
			'MasterTitle' => [
				'entity' => 'XF:Phrase',
				'type' => self::TO_ONE,
				'conditions' => [
					['language_id', '=', 0],
					['title', '=', 'cron_entry.', '$entry_id']
				]
			]
		];
		$structure->options = [];

		return $structure;
	}
}