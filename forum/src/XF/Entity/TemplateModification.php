<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null modification_id
 * @property string addon_id
 * @property string type
 * @property string template
 * @property string modification_key
 * @property int execution_order
 * @property string description
 * @property bool enabled
 * @property string action
 * @property string find
 * @property string replace
 *
 * GETTERS
 * @property array log_summary
 *
 * RELATIONS
 * @property \XF\Entity\AddOn AddOn
 * @property \XF\Entity\Template Template
 * @property \XF\Mvc\Entity\AbstractCollection|\XF\Entity\TemplateModificationLog[] Logs
 */
class TemplateModification extends Entity
{
	/**
	 * @return array
	 */
	public function getLogSummary()
	{
		$summary = [
			'ok' => 0,
			'not_found' => 0,
			'error' => 0
		];

		foreach ($this->Logs AS $log)
		{
			$summary['ok'] += $log->apply_count;
			if ($log->status === 'ok' && $log->apply_count === 0)
			{
				$summary['not_found']++;
			}
			if (strpos($log->status, 'error') !== false)
			{
				$summary['error']++;
			}
		}

		return $summary;
	}

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

	protected function reparseModification($useCurrent = true)
	{
		$method = $useCurrent ? 'getValue' : 'getExistingValue';

		$type = $this->$method('type');
		$title = $this->$method('template');

		$templates = $this->finder('XF:Template')
			->where('title', $title)
			->where('type', $type)
			->fetch();

		if ($templates->count())
		{
			/** @var Template $template */
			foreach ($templates AS $template)
			{
				$devOutput = $template->getBehavior('XF:DevOutputWritable');
				$designerOutput = $template->getBehavior('XF:DesignerOutputWritable');

				$devOutput->setOption('write_dev_output', false);
				$designerOutput->setOption('write_designer_output', false);

				$template->reparseTemplate($this->getOption('hide_errors'));
				$template->save();

				$devOutput->resetOptions();
				$designerOutput->resetOptions();
			}
		}
	}

	protected function _preSave()
	{
		if (($this->action == 'preg_replace' || $this->action == 'callback') && $this->find)
		{
			if (preg_match('/\W[\s\w]*e[\s\w]*$/', $this->find))
			{
				// can't run a /e regex
				$this->error(\XF::phrase('please_enter_valid_regular_expression'), 'find');
			}
			else
			{
				try
				{
					preg_replace($this->find, '', '');
				}
				catch (\ErrorException $e)
				{
					$this->error(\XF::phrase('please_enter_valid_regular_expression'), 'find');
				}
			}
		}

		if ($this->action == 'callback' && ($this->isChanged(['replace', 'action']) || $this->isChanged('action')))
		{
			if (preg_match('/^([a-z0-9_\\\\]+)::([a-z0-9_]+)$/i', $this->replace, $match))
			{
				if (!\XF\Util\Php::validateCallbackPhrased($match[1], $match[2], $errorPhrase))
				{
					$this->error($errorPhrase, 'replace');
				}
			}
			else
			{
				$this->error(\XF::phrase('please_enter_valid_callback_method'), 'replace');
			}
		}
	}

	protected function _postSave()
	{
		if ($this->getOption('reparse_template'))
		{
			$this->reparseModification();

			if ($this->isChanged(['type', 'template']))
			{
				$this->reparseModification(false);
			}
		}
	}

	protected function _postDelete()
	{
		if ($this->Logs)
		{
			foreach ($this->Logs AS $log)
			{
				$log->delete();
			}
		}

		if ($this->getOption('reparse_template'))
		{
			$this->setOption('hide_errors', true);
			$this->reparseModification();
		}
	}

	protected function _setupDefaults()
	{
		/** @var \XF\Repository\AddOn $addOnRepo */
		$addOnRepo = $this->_em->getRepository('XF:AddOn');
		$this->addon_id = $addOnRepo->getDefaultAddOnId();
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_template_modification';
		$structure->shortName = 'XF:TemplateModification';
		$structure->primaryKey = 'modification_id';
		$structure->columns = [
			'modification_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'addon_id' => ['type' => self::BINARY, 'maxLength' => 50, 'default' => ''],
			'type' => ['type' => self::STR, 'required' => true,
				'allowedValues' => ['public', 'admin', 'email']
			],
			'template' => ['type' => self::STR, 'maxLength' => 100,
				'required' => 'please_enter_valid_template_title'
			],
			'modification_key' => ['type' => self::STR, 'maxLength' => 50,
				'required' => 'please_enter_modification_key',
				'unique' => 'template_modification_keys_must_be_unique',
				'match' => 'alphanumeric'
			],
			'execution_order' => ['type' => self::UINT, 'default' => 10],
			'description' => ['type' => self::STR, 'default' => ''],
			'enabled' => ['type' => self::BOOL, 'default' => true],
			'action' => ['type' => self::STR, 'default' => 'str_replace',
				'allowedValues' => ['str_replace', 'preg_replace', 'callback'],
				'required' => true
			],
			'find' => ['type' => self::STR,
				'required' => 'please_enter_search_text'
			],
			'replace' => ['type' => self::STR, 'default' => '']
		];
		$structure->behaviors = [
			'XF:DevOutputWritable' => []
		];
		$structure->getters = [
			'log_summary' => true
		];
		$structure->relations = [
			'AddOn' => [
				'entity' => 'XF:AddOn',
				'type' => self::TO_ONE,
				'conditions' => 'addon_id',
				'primary' => true
			],
			'Template' => [
				'entity' => 'XF:Template',
				'type' => self::TO_ONE,
				'conditions' => [
					['style_id', '=', '0'],
					['title', '=', '$template'],
					['type', '=', '$type']
				]
			],
			'Logs' => [
				'entity' => 'XF:TemplateModificationLog',
				'type' => self::TO_MANY,
				'conditions' => 'modification_id'
			]
		];
		$structure->options = [
			'reparse_template' => true,
			'hide_errors' => false
		];

		return $structure;
	}
}