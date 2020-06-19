<?php

namespace XF;

class Phrase implements \JsonSerializable
{
	/**
	 * @var Language
	 */
	protected $language;

	protected $name;
	protected $params;

	protected $allowHtml = true;

	protected $options = [];

	public function __construct(Language $language, $name, array $params = [], $allowHtml = true)
	{
		$this->language = $language;
		$this->name = $name;
		$this->params = $params;
		$this->allowHtml = $allowHtml;
	}

	public function allowHtml($allowHtml)
	{
		$this->allowHtml = $allowHtml;

		return $this;
	}

	public function fallback($fallback, $raw = false)
	{
		$this->options['fallback'] = $fallback;
		$this->options['fallbackRaw'] = $raw;

		return $this;
	}

	public function render($context = 'html', array $options = [])
	{
		$options = array_replace($this->options, $options);

		if (!$this->allowHtml && $context == 'html')
		{
			$output = $this->language->renderPhrase($this->name, $this->params, 'raw', $options);
			return \XF::escapeString($output, 'html');
		}

		return $this->language->renderPhrase($this->name, $this->params, $context, $options);
	}

	public function __toString()
	{
		try
		{
			return $this->render();
		}
		catch (\Exception $e)
		{
			\XF::logException($e, false, 'Phrase rendering error: ');
			return htmlspecialchars($this->name);
		}
	}

	public function getName()
	{
		return $this->name;
	}

	public function getParams()
	{
		return $this->params;
	}

	public function setParams(array $params)
	{
		$this->params = $params;
	}

	public function jsonSerialize()
	{
		return $this->__toString();
	}
}