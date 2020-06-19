<?php

namespace XF\Widget;

use XF\App;

abstract class AbstractWidget
{
	/**
	 * @var App
	 */
	protected $app;

	/**
	 * @var WidgetConfig
	 */
	protected $widgetConfig;

	protected $contextParams = [];
	
	protected $options;
	protected $defaultOptions = [];

	abstract public function render();

	public function __construct(App $app, WidgetConfig $widgetConfig, array $contextParams = [])
	{
		$this->app = $app;
		$this->widgetConfig = $widgetConfig;
		$this->contextParams = $contextParams;
		$this->options = $this->setupOptions($widgetConfig->options);
	}

	protected function setupOptions(array $options)
	{
		return array_replace($this->defaultOptions, $options);
	}

	public function renderOptions()
	{
		$templateName = $this->getOptionsTemplate();
		if (!$templateName)
		{
			return '';
		}
		return $this->app->templater()->renderTemplate(
			$templateName, $this->getDefaultTemplateParams('options')
		);
	}

	/**
	 * @return string|null
	 */
	public function getOptionsTemplate()
	{
		return 'admin:widget_def_options_' . $this->widgetConfig->definitionId;
	}

	public function verifyOptions(\XF\Http\Request $request, array &$options, &$error = null)
	{
		return true;
	}

	public function getWidgetConfig()
	{
		return $this->widgetConfig;
	}

	public function getTitle()
	{
		$widgetConfig = $this->widgetConfig;
		$title = '';
		if ($widgetConfig->title)
		{
			$title = $widgetConfig->title;
		}
		else
		{
			if ($widgetConfig->widgetKey)
			{
				$title = \XF::phrase('widget.' . $widgetConfig->widgetKey)->render('html', [
					'nameOnInvalid' => false
				]);
			}
		}
		return $title;
	}

	public function getDefaultTitle()
	{
		$widgetConfig = $this->widgetConfig;
		return \XF::phrase('widget_def.' . $widgetConfig->definitionId)->render();
	}

	/**
	 * @param string $templateName
	 * @param array $viewParams
	 *
	 * @return \XF\Widget\WidgetRenderer
	 */
	public function renderer($templateName = '', array $viewParams = [])
	{
		$app = $this->app;
		$class = $app->extendClass('XF\Widget\WidgetRenderer');

		$viewParams = array_replace($this->getDefaultTemplateParams('render'), $viewParams);
		return new $class($app->templater(), 'public:' . $templateName, $viewParams);
	}

	protected function getDefaultTemplateParams($context)
	{
		$widgetConfig = $this->widgetConfig;
		return [
			'title' => $this->getTitle() ?: $this->getDefaultTitle(),
			'widget' => [
				'id' => $widgetConfig->widgetId,
				'key' => $widgetConfig->widgetKey,
				'positions' => $widgetConfig->positions,
				'definition' => $widgetConfig->definitionId
			],
			'context' => $this->contextParams,
			'options' => $this->options
		];
	}

	public function postDelete()
	{
		return;
	}

	/**
	 * @return App
	 */
	public function app()
	{
		return $this->app;
	}

	/**
	 * @return \XF\Db\AbstractAdapter
	 */
	public function db()
	{
		return $this->app->db();
	}

	/**
	 * @return \XF\Mvc\Entity\Manager
	 */
	public function em()
	{
		return $this->app->em();
	}

	/**
	 * @param string $repository
	 *
	 * @return \XF\Mvc\Entity\Repository
	 */
	public function repository($repository)
	{
		return $this->app->repository($repository);
	}

	/**
	 * @param $finder
	 *
	 * @return \XF\Mvc\Entity\Finder
	 */
	public function finder($finder)
	{
		return $this->app->finder($finder);
	}

	/**
	 * @param string $finder
	 * @param array $where
	 * @param string|array $with
	 *
	 * @return null|\XF\Mvc\Entity\Entity
	 */
	public function findOne($finder, array $where, $with = null)
	{
		return $this->app->em()->findOne($finder, $where, $with);
	}

	/**
	 * @param string $class
	 *
	 * @return \XF\Service\AbstractService
	 */
	public function service($class)
	{
		return call_user_func_array([$this->app, 'service'], func_get_args());
	}
}