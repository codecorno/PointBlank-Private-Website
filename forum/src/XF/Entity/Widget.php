<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null widget_id
 * @property string widget_key
 * @property string definition_id
 * @property array positions
 * @property array options
 * @property string display_condition
 * @property string condition_expression
 *
 * GETTERS
 * @property \XF\Phrase|string title
 * @property \XF\Widget\AbstractWidget|null handler
 *
 * RELATIONS
 * @property \XF\Entity\Phrase MasterTitle
 * @property \XF\Entity\WidgetDefinition WidgetDefinition
 */
class Widget extends Entity
{
	public function isActive()
	{
		$widgetDefinition = $this->WidgetDefinition;
		return $widgetDefinition ? $widgetDefinition->isActive() : false;
	}

	public function renderOptions()
	{
		return $this->handler ? $this->handler->renderOptions() : '';
	}

	/**
	 * @return \XF\Phrase|string
	 */
	public function getTitle()
	{
		$widgetDefinition = $this->WidgetDefinition;
		$widgetPhrase = \XF::phrase('widget.' . $this->widget_key);
		return $widgetPhrase->render('html', ['nameOnInvalid' => false]) ?: ($widgetDefinition ? $widgetDefinition->title : '');
	}

	public function getMasterPhrase()
	{
		$phrase = $this->MasterTitle;
		if (!$phrase)
		{
			$phrase = $this->_em->create('XF:Phrase');
			$phrase->title = $this->_getDeferredValue(function() { return 'widget.' . $this->widget_key; });
			$phrase->language_id = 0;
			$phrase->addon_id = '';
		}

		return $phrase;
	}

	/**
	 * @return \XF\Widget\AbstractWidget|null
	 */
	public function getHandler()
	{
		$widgetDefinition = $this->WidgetDefinition;
		if (!$widgetDefinition)
		{
			return null;
		}
		$class = \XF::stringToClass($widgetDefinition->definition_class, '%s\Widget\%s');
		if (!class_exists($class))
		{
			return null;
		}
		$class = \XF::extendClass($class);
		
		$widgetConfig = \XF\Widget\WidgetConfig::create($this);
		return new $class($this->app(), $widgetConfig);
	}

	protected function _preSave()
	{
		if (!$this->getErrors())
		{
			$widgetCompiler = $this->getWidgetCompiler();
			$widgetCompiler->initializeCompilation();

			$compiled = $widgetCompiler->compileEntry($this);

			$this->condition_expression = $compiled->conditionExpression;
		}
	}

	protected function _postSave()
	{
		$this->rebuildWidgetCache();

		$this->compileWidget();
	}

	protected function _postDelete()
	{
		if ($this->MasterTitle)
		{
			$this->MasterTitle->delete();
		}
		if ($this->handler)
		{
			$this->handler->postDelete();
		}

		$this->rebuildWidgetCache();

		$this->compileWidget(true);
	}

	protected function rebuildWidgetCache()
	{
		\XF::runOnce('widgetCacheRebuild', function()
		{
			$this->getWidgetRepo()->rebuildWidgetCache();
		});
	}

	protected function compileWidget($delete = false)
	{
		$isRename = $this->isChanged('widget_key');

		\XF::runOnce('widgetCompile' . $this->widget_id, function() use ($delete, $isRename)
		{
			if ($delete)
			{
				// TODO: Delete compiled widget
			}
			else
			{
				/** @var \XF\Service\Widget\Compile $compileService */
				$compileService = $this->app()->service('XF:Widget\Compile', $this);
				if ($isRename)
				{
//					$compileService->renameFile($this->widget_key);
				}
				$compileService->compile();
			}
		});
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_widget';
		$structure->shortName = 'XF:Widget';
		$structure->primaryKey = 'widget_id';
		$structure->columns = [
			'widget_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'widget_key' => ['type' => self::STR, 'maxLength' => 50,
				'required' => 'please_enter_widget_key',
				'unique' => 'widget_keys_must_be_unique',
				'match' => 'alphanumeric'
			],
			'definition_id' => ['type' => self::STR, 'maxLength' => 25, 'match' => 'alphanumeric', 'required' => true],
			'positions' => ['type' => self::JSON_ARRAY, 'default' => []],
			'options' => ['type' => self::JSON_ARRAY, 'default' => []],
			'display_condition' => ['type' => self::STR, 'default' => ''],
			'condition_expression' => ['type' => self::BINARY, 'default' => ''],
		];
		$structure->getters = [
			'title' => true,
			'handler' => true
		];
		$structure->relations = [
			'MasterTitle' => [
				'entity' => 'XF:Phrase',
				'type' => self::TO_ONE,
				'conditions' => [
					['language_id', '=', 0],
					['title', '=', 'widget.', '$widget_key']
				]
			],
			'WidgetDefinition' => [
				'entity' => 'XF:WidgetDefinition',
				'type' => self::TO_ONE,
				'conditions' => 'definition_id',
				'primary' => true
			]
		];

		return $structure;
	}

	/**
	 * @return \XF\Repository\Widget
	 */
	protected function getWidgetRepo()
	{
		return $this->repository('XF:Widget');
	}

	/**
	 * @return \XF\Widget\WidgetCompiler
	 */
	protected function getWidgetCompiler()
	{
		return $this->app()->widget()->getWidgetCompiler();
	}
}