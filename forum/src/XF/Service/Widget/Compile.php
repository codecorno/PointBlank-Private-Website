<?php

namespace XF\Service\Widget;

use XF\Entity\Widget;
use XF\Service\AbstractService;
use XF\Widget\WidgetCompiler;

class Compile extends AbstractService
{
	/**
	 * @var Widget
	 */
	protected $widget;

	/**
	 * @var WidgetCompiler
	 */
	protected $compiler;

	public function __construct(\XF\App $app, Widget $widget)
	{
		parent::__construct($app);

		$this->widget = $widget;
		$this->compiler = $this->app->widget()->getWidgetCompiler();
	}

	public function compile()
	{
		$widget = $this->widget;
		$compiler = $this->compiler;

		$code = $compiler->compile($widget);
		$contents = "<?php\n\n" . $code;

		$this->writeCode($contents);
	}

	public function writeCode($contents)
	{
		$widgetFile = $this->getWidgetFilename();
		\XF\Util\File::writeToAbstractedPath($widgetFile, $contents);
	}

	protected function getWidgetFilename()
	{
		return "code-cache://widgets/_{$this->widget->widget_id}_{$this->widget->widget_key}.php";
	}
}