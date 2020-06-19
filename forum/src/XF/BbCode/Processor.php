<?php

namespace XF\BbCode;

use XF\BbCode\ProcessorAction\AnalyzerInterface;
use XF\BbCode\ProcessorAction\FiltererInterface;
use XF\BbCode\ProcessorAction\ProcessorAwareInterface;

class Processor extends Traverser
{
	/**
	 * @var AnalyzerInterface[]
	 */
	protected $analyzers = [];

	protected $analyzeParseCallbacks = [];

	protected $analyzeSetupCallbacks = [];

	protected $analyzeGlobalTagCallbacks = [];

	protected $analyzeTagCallbacks = [];

	protected $analyzeStringCallbacks = [];

	protected $analyzeFinalCallbacks = [];

	/**
	 * @var FiltererInterface
	 */
	protected $filterers = [];

	protected $filterParseCallbacks = [];

	protected $filterSetupCallbacks = [];

	protected $filterGlobalTagCallbacks = [];

	protected $filterTagCallbacks = [];

	protected $filterStringCallbacks = [];

	protected $filterTagOptionCallbacks = [];

	protected $filterFinalCallbacks = [];

	public function __construct()
	{
	}

	public function addProcessorAction($name, $processor)
	{
		$valid = false;

		if ($processor instanceof AnalyzerInterface)
		{
			$this->addAnalyzer($name, $processor);
			$valid = true;
		}
		if ($processor instanceof FiltererInterface)
		{
			$this->addFilterer($name, $processor);
			$valid = true;
		}

		if (!$valid)
		{
			throw new \InvalidArgumentException("Process provided must implement AnalyzerInterface or FiltererInterface");
		}

		return $this;
	}

	public function addAnalyzer($name, AnalyzerInterface $analyzer)
	{
		if (isset($this->analyzers[$name]))
		{
			throw new \LogicException("Analyzer '$name' has already been registered");
		}

		if ($analyzer instanceof ProcessorAwareInterface)
		{
			$analyzer->setProcessor($this);
		}

		$hooks = new ProcessorAction\AnalyzerHooks($analyzer);
		$analyzer->addAnalysisHooks($hooks);

		$parse = $hooks->getParseHooks();
		$this->analyzeParseCallbacks = array_merge($this->analyzeParseCallbacks, $parse);

		$setup = $hooks->getSetupHooks();
		$this->analyzeSetupCallbacks = array_merge($this->analyzeSetupCallbacks, $setup);

		$string = $hooks->getStringHooks();
		$this->analyzeStringCallbacks = array_merge($this->analyzeStringCallbacks, $string);

		$final = $hooks->getFinalHooks();
		$this->analyzeFinalCallbacks = array_merge($this->analyzeFinalCallbacks, $final);

		$globalTags = $hooks->getGlobalTagHooks();
		$this->analyzeGlobalTagCallbacks = array_merge($this->analyzeGlobalTagCallbacks, $globalTags);

		$tags = $hooks->getTagHooks();
		foreach ($tags AS $tag => $hooks)
		{
			if (isset($this->analyzeTagCallbacks[$tag]))
			{
				$this->analyzeTagCallbacks[$tag] = array_merge($this->analyzeTagCallbacks[$tag], $hooks);
			}
			else
			{
				$this->analyzeTagCallbacks[$tag] = $hooks;
			}
		}

		$this->analyzers[$name] = $analyzer;

		return $this;
	}

	public function getAnalyzer($name)
	{
		return isset($this->analyzers[$name]) ? $this->analyzers[$name] : null;
	}

	public function addFilterer($name, FiltererInterface $filterer)
	{
		if (isset($this->filterers[$name]))
		{
			throw new \LogicException("Filterer '$name' has already been registered");
		}

		if ($filterer instanceof ProcessorAwareInterface)
		{
			$filterer->setProcessor($this);
		}

		$hooks = new ProcessorAction\FiltererHooks($filterer);
		$filterer->addFiltererHooks($hooks);

		$parse = $hooks->getParseHooks();
		$this->filterParseCallbacks = array_merge($this->filterParseCallbacks, $parse);

		$setup = $hooks->getSetupHooks();
		$this->filterSetupCallbacks = array_merge($this->filterSetupCallbacks, $setup);

		$string = $hooks->getStringHooks();
		$this->filterStringCallbacks = array_merge($this->filterStringCallbacks, $string);

		$option = $hooks->getTagOptionHooks();
		$this->filterTagOptionCallbacks = array_merge($this->filterTagOptionCallbacks, $option);

		$final = $hooks->getFinalHooks();
		$this->filterFinalCallbacks = array_merge($this->filterFinalCallbacks, $final);

		$globalTags = $hooks->getGlobalTagHooks();
		$this->filterGlobalTagCallbacks = array_merge($this->filterGlobalTagCallbacks, $globalTags);

		$tags = $hooks->getTagHooks();
		foreach ($tags AS $tag => $hooks)
		{
			if (isset($this->filterTagCallbacks[$tag]))
			{
				$this->filterTagCallbacks[$tag] = array_merge($this->filterTagCallbacks[$tag], $hooks);
			}
			else
			{
				$this->filterTagCallbacks[$tag] = $hooks;
			}
		}

		$this->filterers[$name] = $filterer;

		return $this;
	}

	public function getFilterer($name)
	{
		return isset($this->filterers[$name]) ? $this->filterers[$name] : null;
	}

	public function getDefaultOptions()
	{
		return [
			'stopSmilies' => 0,
			'stopAutoLink' => 0,
			'plain' => false,
		];
	}

	protected function setupParse($string, Parser $parser, RuleSet $rules, array &$options)
	{
		$string = parent::setupParse($string, $parser, $rules, $options);

		foreach ($this->filterParseCallbacks AS $callback)
		{
			$string = call_user_func_array($callback, [$string, $parser, $rules, &$options]);
		}

		foreach ($this->analyzeParseCallbacks AS $callback)
		{
			$string = call_user_func_array($callback, [$string, $parser, $rules, &$options]);
		}

		return $string;
	}

	protected function setupRender(array $ast, array $options)
	{
		parent::setupRender($ast, $options);

		foreach ($this->analyzeSetupCallbacks AS $callback)
		{
			call_user_func($callback, $ast, $options, $this);
		}

		foreach ($this->filterSetupCallbacks AS $callback)
		{
			call_user_func($callback, $ast, $options, $this);
		}
	}

	public function filterFinalOutput($output)
	{
		$output = parent::filterFinalOutput($output);

		foreach ($this->filterFinalCallbacks AS $callback)
		{
			$output = call_user_func($callback, $output, $this);
		}

		foreach ($this->analyzeFinalCallbacks AS $callback)
		{
			call_user_func($callback, $output, $this);
		}

		return $output;
	}

	public function renderTag(array $tag, array $options)
	{
		$tagName = $tag['tag'];
		$rule = $this->rules->getTag($tagName);

		if ($rule)
		{
			if (!empty($rule['stopSmilies']))
			{
				$options['stopSmilies']++;
			}
			if (!empty($rule['stopAutoLink']))
			{
				$options['stopAutoLink']++;
			}
			if (!empty($rule['plain']))
			{
				$options['plain'] = true;
			}
		}

		$output = $this->filterTag($tag, $options);
		$this->analyzeTag($tag, $options, $output);
		return $output;
	}

	protected function filterTag(array $tag, array $options)
	{
		$filter = function($callback) use ($tag, $options)
		{
			$output = call_user_func($callback, $tag, $options, $this);
			if ($output === true)
			{
				return $this->renderValidTag($tag, $options);
			}
			else if ($output === false)
			{
				return $this->renderSubTree($tag['children'], $options);
			}
			else if (is_string($output))
			{
				return $output;
			}
			else
			{
				return null;
			}
		};

		foreach ($this->filterGlobalTagCallbacks AS $callback)
		{
			$output = $filter($callback);
			if (is_string($output))
			{
				return $output;
			}
		}

		$tagName = $tag['tag'];

		if (!empty($this->filterTagCallbacks[$tagName]))
		{
			foreach ($this->filterTagCallbacks[$tagName] AS $callback)
			{
				$output = $filter($callback);
				if (is_string($output))
				{
					return $output;
				}
			}
		}

		return $this->renderValidTag($tag, $options);
	}

	public function renderValidTag(array $tag, array $options)
	{
		if ($tag['option'] && $this->filterTagOptionCallbacks)
		{
			$optionValue = $tag['option'];
			foreach ($this->filterTagOptionCallbacks AS $callback)
			{
				$optionValue = call_user_func($callback, $optionValue, $tag, $options, $this);
			}

			$open = $this->replaceOptionInTagOpen($tag['original'][0], $optionValue);
		}
		else
		{
			$open = $tag['original'][0];
		}

		return $open
			. $this->renderSubTree($tag['children'], $options)
			. $tag['original'][1];
	}

	protected function analyzeTag(array $tag, array $options, $finalOutput)
	{
		foreach ($this->analyzeGlobalTagCallbacks AS $callback)
		{
			call_user_func($callback, $tag, $options, $finalOutput, $this);
		}

		$tagName = $tag['tag'];

		if (!empty($this->analyzeTagCallbacks[$tagName]))
		{
			foreach ($this->analyzeTagCallbacks[$tagName] AS $callback)
			{
				call_user_func($callback, $tag, $options, $finalOutput, $this);
			}
		}
	}

	public function renderString($string, array $options)
	{
		$output = $this->filterString($string, $options);
		$this->analyzeString($string, $options, $output);
		return $output;
	}

	protected function analyzeString($string, array $options, $finalOutput)
	{
		foreach ($this->analyzeStringCallbacks AS $callback)
		{
			call_user_func($callback, $string, $options, $finalOutput, $this);
		}
	}

	protected function filterString($string, array $options)
	{
		foreach ($this->filterStringCallbacks AS $callback)
		{
			$string = call_user_func($callback, $string, $options, $this);
		}

		return $string;
	}

	public function replaceOptionInTagOpen($open, $newValue)
	{
		if (is_array($newValue))
		{
			foreach ($newValue AS $k => $v)
			{
				$open = preg_replace_callback(
					'#' . $k . '=("|\').*("|\')#U',
					function ($match) use ($k, $v)
					{
						return "$k={$match[1]}{$v}{$match[2]}";
					},
					$open
				);
			}

			return $open;
		}
		else
		{
			return preg_replace_callback(
				'#=("|\'|).*\]$#s',
				function ($match) use ($newValue)
				{
					if (strpos($newValue, '"') !== false)
					{
						$newValue = "'$newValue'";
					}
					else if (strpos($newValue, "'") !== false)
					{
						$newValue = "\"$newValue\"";
					}
					else
					{
						$newValue = $match[1] . $newValue . $match[1];
					}
					return '=' . $newValue . ']';
				},
				$open
			);
		}
	}
}