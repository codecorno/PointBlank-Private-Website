<?php

namespace XF\Install;

class Templater extends \XF\Template\Templater
{
	public function getTemplateFilePath($type, $name, $styleIdOverride = null)
	{
		return $this->compiledPath . '/' .preg_replace('/[^a-zA-Z0-9_.-]/', '', $name) . '.php';
	}

	/**
	 * @param string $template
	 * @param array $params
	 * @param bool $addDefaultParams
	 *
	 * @return string
	 */
	public function renderTemplate($template, array $params = [], $addDefaultParams = true)
	{
		$params['templater'] = $this;

		extract($params);
		list($type, $template) = $this->getTemplateTypeAndName($template);

		set_error_handler([$this, 'handleTemplateError']);

		ob_start();
		include($this->getTemplateFilePath($type, $template));
		$output = ob_get_clean();

		restore_error_handler();

		return $output;
	}

	public function setTitle($title)
	{
		$this->setPageParam('title', $title);
	}

	public function getJsUrl($js)
	{
		if (preg_match('#^(/|[a-z]+:)#i', $js))
		{
			return $js;
		}

		if (!strpos($js, '_v='))
		{
			$js = $js . (strpos($js, '?') ? '&' : '?') . $this->getJsCacheBuster();
		}

		$pather = $this->pather;
		return $pather("../js/$js", 'base');
	}
}