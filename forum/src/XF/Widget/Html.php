<?php

namespace XF\Widget;

class Html extends AbstractWidget
{
	protected $defaultOptions = [
		'advanced_mode' => false,
		'template_title' => ''
	];

	public function render()
	{
		$template = $this->app->templater()->renderTemplate(
			'public:' . $this->options['template_title'],
			$this->getDefaultTemplateParams('render')
		);
		return $this->renderer('widget_html', ['template' => $template]);
	}

	public function verifyOptions(\XF\Http\Request $request, array &$options, &$error = null)
	{
		$widgetConfig = $this->widgetConfig;

		if (!$widgetConfig->widgetKey)
		{
			return false;
		}

		$templateTitle = '_widget_' . $widgetConfig->widgetKey;

		if (!$widgetConfig->widgetId)
		{
			$templateEnt = $this->em()->create('XF:Template');
			$templateEnt->type = 'public';
			$templateEnt->title = $templateTitle;
			$templateEnt->style_id = 0;
			$templateEnt->addon_id = '';
		}
		else
		{
			$templateEnt = $this->getMasterTemplate();

			// Template name change
			if ($templateEnt->title !== $templateTitle)
			{
				$templateEnt->title = $templateTitle;
			}
		}

		$templateEnt->template = $request->filter('template', 'str');

		$templateEnt->save();

		$options = $request->filter([
			'advanced_mode' => 'bool'
		]);
		$options['template_title'] = $templateTitle;

		return true;
	}

	protected function getDefaultTemplateParams($context)
	{
		$params = parent::getDefaultTemplateParams($context);
		if ($context == 'options')
		{
			$params['template'] = $this->getMasterTemplate();
		}
		return $params;
	}

	public function postDelete()
	{
		$template = $this->getMasterTemplate();
		if ($template)
		{
			$template->delete(false, false);
		}
	}

	protected function getMasterTemplate()
	{
		if (isset($this->options['template_title']))
		{
			return $this->em()->findOne('XF:Template', [
				'style_id' => 0,
				'type' => 'public',
				'title' => $this->options['template_title']
			]);
		}
		return null;
	}
}