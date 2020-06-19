<?php

namespace XF\BbCode\ProcessorAction;

class AnalyzerHooks
{
	/**
	 * @var AnalyzerInterface
	 */
	protected $analyzer;

	/**
	 * @var callable[]
	 */
	protected $parseHooks = [];

	/**
	 * @var callable[]
	 */
	protected $setupHooks = [];

	/**
	 * @var callable[]
	 */
	protected $globalTagHooks = [];

	/**
	 * @var callable[][]
	 */
	protected $tagHooks = [];

	/**
	 * @var callable[]
	 */
	protected $stringHooks = [];

	/**
	 * @var callable[]
	 */
	protected $finalHooks = [];

	public function __construct(AnalyzerInterface $analyzer)
	{
		$this->analyzer = $analyzer;
	}

	protected function appendHook($function, &$target)
	{
		if (is_string($function))
		{
			$function = [$this->analyzer, $function];
		}

		if (!is_callable($function))
		{
			if (is_array($function) && isset($function[0]) && isset($function[1]))
			{
				$o = is_object($function[0]) ? get_class($function[0]) : $function[0];
				$f = $function[1];
				throw new \InvalidArgumentException("Non-callable function $o->$f provided");
			}
			else
			{
				throw new \InvalidArgumentException("Non-callable function provided");
			}
		}

		$target[] = $function;

		return $this;
	}

	public function addParseHook($function)
	{
		return $this->appendHook($function, $this->parseHooks);
	}

	public function addSetupHook($function)
	{
		return $this->appendHook($function, $this->setupHooks);
	}

	public function addGlobalTagHook($function)
	{
		return $this->appendHook($function, $this->globalTagHooks);
	}

	public function addTagHook($tag, $function)
	{
		if (!isset($this->tagHooks[$tag]))
		{
			$this->tagHooks[$tag] = [];
		}

		return $this->appendHook($function, $this->tagHooks[$tag]);
	}

	public function addStringHook($function)
	{
		return $this->appendHook($function, $this->stringHooks);
	}

	public function addFinalHook($function)
	{
		return $this->appendHook($function, $this->finalHooks);
	}

	/**
	 * @return callable[]
	 */
	public function getParseHooks()
	{
		return $this->parseHooks;
	}

	/**
	 * @return callable[]
	 */
	public function getSetupHooks()
	{
		return $this->setupHooks;
	}

	/**
	 * @return callable[]
	 */
	public function getGlobalTagHooks()
	{
		return $this->globalTagHooks;
	}

	/**
	 * @return callable[][]
	 */
	public function getTagHooks()
	{
		return $this->tagHooks;
	}

	/**
	 * @return callable[]
	 */
	public function getStringHooks()
	{
		return $this->stringHooks;
	}

	/**
	 * @return callable[]
	 */
	public function getFinalHooks()
	{
		return $this->finalHooks;
	}
}