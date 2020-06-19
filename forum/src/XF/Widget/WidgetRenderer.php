<?php

namespace XF\Widget;

use XF\Template\Templater;

class WidgetRenderer
{
	/**
	 * @var Templater
	 */
	protected $templater;

	protected $templateName;
	protected $viewParams = [];

	public function __construct(Templater $templater, $templateName = '', $viewParams = [])
	{
		$this->templater = $templater;

		$this->setTemplateName($templateName);
		$this->setViewParams($viewParams);
	}

	public function setTemplateName($templateName)
	{
		$this->templateName = $templateName;
	}

	public function getTemplateName()
	{
		return $this->templateName;
	}

	public function setViewParams(array $viewParams)
	{
		$this->viewParams = $viewParams;
	}

	public function setViewParam($key, $value)
	{
		$this->viewParams[$key] = $value;
	}

	public function getViewParams()
	{
		return $this->viewParams;
	}

	public function getViewParam($key)
	{
		return isset($this->viewParams[$key]) ? $this->viewParams[$key] : null;
	}

	public function render()
	{
		$templateName = $this->templateName;
		if (!$templateName)
		{
			return '';
		}
		return $this->templater->renderTemplate($templateName, $this->viewParams);
	}

	public function __toString()
	{
		return $this->render();
	}
}