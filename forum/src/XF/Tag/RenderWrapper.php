<?php

namespace XF\Tag;

use XF\Mvc\Entity\Entity;

class RenderWrapper implements \XF\PreEscapedInterface
{
	/**
	 * @var AbstractHandler
	 */
	protected $handler;

	/**
	 * @var Entity
	 */
	protected $result;

	protected $options;

	public function __construct(AbstractHandler $handler, Entity $result, array $options = [])
	{
		$this->handler = $handler;
		$this->result = $result;
		$this->options = $options;
	}

	public function render(array $extraOptions = [])
	{
		return $this->handler->renderResult($this->result, array_merge($this->options, $extraOptions));
	}

	public function getPreEscapeType()
	{
		return 'html';
	}

	public function __toString()
	{
		try
		{
			return $this->render();
		}
		catch (\Exception $e)
		{
			\XF::logException($e, false, "Search render error: ");
			return '';
		}
	}

	/**
	 * @return AbstractHandler
	 */
	public function getHandler()
	{
		return $this->handler;
	}

	/**
	 * @return Entity
	 */
	public function getResult()
	{
		return $this->result;
	}

	public function getOptions()
	{
		return $this->options;
	}

	public function mergeOptions(array $options)
	{
		$this->options = array_merge($this->options, $options);
	}
}