<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;
use XF\Template\Compiler\Ast;

/**
 * COLUMNS
 * @property int|null template_id
 * @property string type
 * @property string title
 * @property int style_id
 * @property string template
 * @property array|bool template_parsed
 * @property string addon_id
 * @property int version_id
 * @property string version_string
 * @property int last_edit_date
 *
 * GETTERS
 * @property string combined_title
 * @property array phrasesUsed
 * @property Style Style
 * @property Template|null ParentTemplate
 *
 * RELATIONS
 * @property \XF\Entity\AddOn AddOn
 * @property \XF\Mvc\Entity\AbstractCollection|\XF\Entity\TemplateHistory[] History
 * @property \XF\Entity\Style Style_
 */
class Template extends Entity
{
	protected $modificationStatuses = null;

	/**
	 * @return string
	 */
	public function getCombinedTitle()
	{
		return "{$this->type}:{$this->title}";
	}

	/**
	 * @return Style
	 */
	public function getStyle()
	{
		if ($this->style_id == 0)
		{
			return $this->getStyleRepo()->getMasterStyle();
		}
		else
		{
			return $this->getRelation('Style');
		}
	}

	/**
	 * @return Template|null
	 */
	public function getParentTemplate()
	{
		if (!$this->style_id)
		{
			return null;
		}

		$style = $this->Style;
		if (!$style)
		{
			return null;
		}

		$templateMap = $this->finder('XF:TemplateMap')
			->where([
				'style_id' => $style->parent_id,
				'type' => $this->type,
				'title' => $this->title
			])
			->with('Template', true)
			->with('Template.Style', false)
			->fetchOne();
		if (!$templateMap)
		{
			return null;
		}

		return $templateMap->Template;
	}

	public function getAbstractedCompiledTemplatePath($languageId, $styleId, $new = true)
	{
		$title = $new ? $this->getValue('title') : $this->getExistingValue('title');
		$type = $new ? $this->getValue('type') : $this->getExistingValue('type');

		return 'code-cache://templates/l' . $languageId . '/s' . $styleId . '/' . $type . '/' . $title . '.php';
	}

	public function getHash()
	{
		$contents = str_replace("\r", '', $this->template);
		return md5($contents);
	}

	/**
	 * @return array
	 */
	public function getPhrasesUsed()
	{
		return $this->db()->fetchAllColumn("
			SELECT phrase_title
			FROM xf_template_phrase
			WHERE template_id = ?
		", $this->template_id);
	}

	public function reparseTemplate($forceValid = false, &$error = null)
	{
		$valid = $this->validateTemplateText($this->template, $forceValid, $ast, $error);
		if ($valid && $ast != $this->template_parsed)
		{
			$this->template_parsed = $ast;
			return true;
		}
		if (!$valid)
		{
			$this->error($error, 'template');
			return true;
		}

		return false;
	}

	public function setTemplateUnchecked($template)
	{
		$template = strval($template);
		$this->_setInternal('template', $template);
		$this->validateTemplateText($template, true, $ast);

		$this->template_parsed = $ast;

		return true;
	}

	protected function verifyTitle($title)
	{
		if (substr($title, 0, 1) == '.' || strpos($title, '..') !== false)
		{
			$this->error(\XF::phrase('please_enter_title_using_only_alphanumeric_dot'), 'title');
			return false;
		}

		if (preg_match('/\.html$/i', $title))
		{
			$this->error(\XF::phrase('template_titles_may_not_end_in_html'), 'title');
			return false;
		}

		return true;
	}

	protected function verifyTemplate($template)
	{
		$valid = $this->validateTemplateText($template, false, $ast, $error);
		if (!$valid)
		{
			$this->error($error, 'template');
			return false;
		}

		$this->template_parsed = $ast;

		return true;
	}

	protected function validateTemplateText($template, $forceValid = false, &$ast = null, &$error = null)
	{
		$compiler = $this->app()->templateCompiler();

		/** @var \XF\Repository\TemplateModification $templateModRepo */
		$templateModRepo = $this->repository('XF:TemplateModification');
		$templateWithModifications = $templateModRepo->applyModificationsToTemplate(
			$this->type, $this->title, $template, $modificationStatuses
		);

		$standardParse = true;
		$error = null;

		if ($modificationStatuses)
		{
			try
			{
				$ast = $compiler->compileToAst($templateWithModifications);

				if ($this->getOption('test_compile'))
				{
					$compiler->compileAst($ast);
				}
				$standardParse = false;
			}
			catch (\XF\Template\Compiler\Exception $e)
			{
				$error = $e->getMessage() . ' - ' . \XF::phrase('template_modifications:') . ' ' . "{$this->type}:{$this->title}";
				foreach ($modificationStatuses AS &$status)
				{
					if (is_int($status))
					{
						$status = 'error_compile';
					}
				}
			}
		}

		if ($standardParse)
		{
			try
			{
				$ast = $compiler->compileToAst($template);

				if ($this->getOption('test_compile'))
				{
					$compiler->compileAst($ast);
				}

				if ($forceValid || !$this->getOption('report_modification_errors'))
				{
					$error = null;
				}
			}
			catch (\XF\Template\Compiler\Exception $e)
			{
				$error = $e->getMessage() . ' - ' . \XF::phrase('template_name:') . ' ' . "{$this->type}:{$this->title}";
			}
		}

		if ($forceValid && $error)
		{
			$error = null;
			$ast = $compiler->stringAst($template);
		}

		if ($error)
		{
			$ast = null;
			return false;
		}

		$this->modificationStatuses = $modificationStatuses;
		return true;
	}

	protected function updateTemplateHistoryLog()
	{
		if ($this->isChanged('template'))
		{
			if ($this->isUpdate())
			{
				$this->db()->insert('xf_template_history', [
					'type'		=> $this->type,
					'title'     => $this->title,
					'style_id'  => $this->style_id,
					'template'  => $this->getExistingValue('template'),
					'edit_date' => $this->getExistingValue('last_edit_date'),
					'log_date'  => \XF::$time
				]);
			}
			else if ($this->style_id > 0 && $this->Style)
			{
				// on an insert, if we find this template in the direct parent style, we should assume that
				// we're effectively customizing the template from that basis, so consider it like an update
				$template = $this->db()->fetchRow("
					SELECT template.*
					FROM xf_template_map AS map
					INNER JOIN xf_template AS template ON (template.template_id = map.template_id)
					WHERE map.style_id = ?
						AND map.type = ?
						AND map.title = ?
				", [$this->Style->parent_id, $this->type, $this->title]);
				if ($template)
				{
					$this->db()->insert('xf_template_history', [
						'type'		=> $this->type,
						'title'     => $this->title,
						'style_id'  => $this->style_id,
						'template'  => $template['template'],
						'edit_date' => $template['last_edit_date'],
						'log_date'  => \XF::$time
					]);
				}
			}
		}
	}

	protected function updateTemplateModificationLog()
	{
		$modificationStatuses = $this->modificationStatuses;

		if (is_array($modificationStatuses))
		{
			$inserts = [];
			$db = $this->db();

			foreach ($modificationStatuses AS $id => $status)
			{
				if (is_int($status))
				{
					$inserts[] = [
						'template_id' => $this->template_id,
						'modification_id' => $id,
						'status' => 'ok',
						'apply_count' => $status
					];
				}
				else
				{
					$inserts[] = [
						'template_id' => $this->template_id,
						'modification_id' => $id,
						'status' => $status,
						'apply_count' => 0
					];
				}
			}

			$db->delete('xf_template_modification_log', 'template_id = ?', $this->template_id);
			if ($inserts)
			{
				$db->insertBulk('xf_template_modification_log', $inserts);
			}
		}
	}

	protected function _preSave()
	{
		if ($this->isInsert() && !$this->isChanged('template'))
		{
			$this->error(\XF::phrase('template_value_has_not_been_set_properly'), 'template', false);
		}

		if (!$this->template_parsed || !($this->template_parsed instanceof Ast))
		{
			// prevent an error about this being a required field or not having what we expect
			$this->template_parsed = new Ast([]);
		}

		if ($this->isUpdate() && $this->isChanged('style_id'))
		{
			// note: this is more of a developer error, so this isn't phrased
			$this->error('Template style IDs cannot be changed after they\'ve been created.', 'style_id');
		}

		if ($this->style_id > 0 && $this->type == 'admin')
		{
			$this->error(\XF::phrase('admin_templates_may_only_be_created_in_master_style'));
		}

		if ($this->getOption('check_duplicate'))
		{
			if ($this->isChanged(['title', 'style_id', 'type']))
			{
				$template = $this->finder('XF:Template')
					->where([
						'type' => $this->type,
						'title' => $this->title,
						'style_id' => $this->style_id
					])->fetchOne();
				if ($template && $template != $this)
				{
					$this->error(\XF::phrase('template_titles_must_be_unique'), 'title');
				}
			}
		}

		if (!$this->isChanged('version_id')
			&& $this->isChanged(['type', 'title', 'template', 'addon_id'])
		)
		{
			$this->updateVersionId();
		}

		if (($this->isChanged('template') || $this->isChanged('version_id')) && !$this->isChanged('last_edit_date'))
		{
			$this->set('last_edit_date', \XF::$time);
		}
	}

	protected function _postSave()
	{
		$this->updateTemplateHistoryLog();
		$this->updateTemplateModificationLog();

		$rebuildService = $this->getRebuildTemplateService();
		$compileService = $this->getCompileService();

		if ($this->isUpdate() && $this->isChanged(['title', 'type']))
		{
			$compileService->deleteCompiled($this, false);

			if ($this->getOption('rebuild_map'))
			{
				$rebuildService->rebuildTemplateMapForTitle(
					$this->getExistingValue('type'),
					$this->getExistingValue('title')
				);
			}

			if ($this->getOption('recompile'))
			{
				$compileService->recompileByTitle(
					$this->getExistingValue('type'),
					$this->getExistingValue('title')
				);
			}
		}

		if ($this->getOption('rebuild_map')
			&& $this->isInsert() || $this->isChanged(['title', 'type'])
		)
		{
			$rebuildService->rebuildTemplateMapForTitle($this->type, $this->title);
		}

		if ($this->isChanged('template_parsed'))
		{
			$phrases = $compileService->updatePhrasesUsed($this);
			$this->_getterCache['phrasesUsed'] = $phrases;
		}

		if ($this->getOption('recompile'))
		{
			$compileService->recompile($this);
		}

		if (($this->isInsert() || $this->isChanged('addon_id')) && $this->style_id == 0)
		{
			// If we're inserting a new template into the master style or changing its addon_id then
			// update any other styles which have templates of the same name to have the correct addon_id.
			$this->db()->update('xf_template', [
				'addon_id' => $this->addon_id
			], 'type = ? AND title = ? AND style_id > 0', [$this->type, $this->title]);
		}
	}

	protected function _postDelete()
	{
		$db = $this->db();
		$id = $this->template_id;

		$db->delete('xf_template_phrase', 'template_id = ?', $id);
		$db->delete('xf_template_modification_log', 'template_id = ?', $id);

		$rebuildService = $this->getRebuildTemplateService();
		$compileService = $this->getCompileService();

		$compileService->deleteCompiled($this, false);

		if ($this->getOption('rebuild_map'))
		{
			$rebuildService->rebuildTemplateMapForTitle($this->type, $this->title);
		}

		if ($this->getOption('recompile'))
		{
			$compileService->recompileByTitle($this->type, $this->title);
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
		$structure->table = 'xf_template';
		$structure->shortName = 'XF:Template';
		$structure->primaryKey = 'template_id';
		$structure->columns = [
			'template_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'type' => ['type' => self::STR, 'required' => true,
				'allowedValues' => ['public', 'admin', 'email']
			],
			'title' => ['type' => self::STR, 'maxLength' => 100,
				'required' => 'please_enter_valid_title',
				'match' => 'alphanumeric_dot'
			],
			'style_id' => ['type' => self::UINT, 'required' => true],
			'template' => ['type' => self::STR, 'default' => ''],
			'template_parsed' => ['type' => self::SERIALIZED, 'required' => true],
			'addon_id' => ['type' => self::BINARY, 'maxLength' => 50, 'default' => ''],
			'version_id' => ['type' => self::UINT, 'default' => 0],
			'version_string' => ['type' => self::STR, 'maxLength' => 30, 'default' => ''],
			'last_edit_date' => ['type' => self::UINT, 'default' => 0]
		];
		$structure->behaviors = [
			'XF:DevOutputWritable' => [],
			'XF:DesignerOutputWritable' => []
		];
		$structure->getters = [
			'combined_title' => false,
			'phrasesUsed' => true,
			'Style' => true,
			'ParentTemplate' => true
		];
		$structure->relations = [
			'AddOn' => [
				'entity' => 'XF:AddOn',
				'type' => self::TO_ONE,
				'conditions' => 'addon_id',
				'primary' => true
			],
			'History' => [
				'type' => self::TO_MANY,
				'entity' => 'XF:TemplateHistory',
				'conditions' => [
					['type', '=', '$type'],
					['title', '=', '$title'],
					['style_id', '=', '$style_id']
				]
			],
			'Style' => [
				'type' => self::TO_ONE,
				'entity' => 'XF:Style',
				'conditions' => 'style_id',
				'primary' => true
			]
		];
		$structure->options = [
			'check_duplicate' => true,
			'recompile' => true,
			'rebuild_map' => true,
			'test_compile' => true,
			'report_modification_errors' => true
		];

		return $structure;
	}

	/**
	 * @return \XF\Service\Template\Rebuild
	 */
	protected function getRebuildTemplateService()
	{
		return $this->app()->service('XF:Template\Rebuild');
	}

	/**
	 * @return \XF\Service\Template\Compile
	 */
	protected function getCompileService()
	{
		return $this->app()->service('XF:Template\Compile', $this);
	}

	/**
	 * @return \XF\Repository\Template
	 */
	protected function getTemplateRepo()
	{
		return $this->repository('XF:Template');
	}

	/**
	 * @return \XF\Repository\Style
	 */
	protected function getStyleRepo()
	{
		return $this->repository('XF:Style');
	}
}