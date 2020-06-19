<?php

namespace XF\BbCode;

abstract class Traverser
{
	/**
	 * @var RuleSet
	 */
	protected $rules;

	abstract public function renderTag(array $tag, array $options);
	abstract public function renderString($string, array $options);

	public function render($string, Parser $parser, RuleSet $rules, array $options = [])
	{
		$string = $this->setupParse($string, $parser, $rules, $options);

		$ast = $parser->parse($string, $rules);
		return $this->renderAst($ast, $rules, $options);
	}

	protected function setupParse($string, Parser $parser, RuleSet $rules, array &$options)
	{
		return $string;
	}

	public function renderAst(array $ast, RuleSet $rules, array $options = [])
	{
		$this->rules = $rules;
		$options = $options + $this->getDefaultOptions();
		$options['stack'] = [];

		$this->setupRenderOptions($ast, $options);
		$this->setupRender($ast, $options);

		return $this->filterFinalOutput($this->renderSubTree($ast, $options));
	}

	public function getDefaultOptions()
	{
		return [];
	}

	protected function setupRenderOptions(array $ast, array &$options)
	{
	}

	protected function setupRender(array $ast, array $options)
	{
	}

	public function renderSubTree(array $tree, array $options)
	{
		$output = '';
		foreach ($tree AS $element)
		{
			if (is_array($element))
			{
				$options['stack'][] = $element;
				$output .= $this->renderTag($element, $options);
			}
			else
			{
				$output .= $this->renderString($element, $options);
			}
		}

		return $output;
	}

	public function renderSubTreePlain(array $tree)
	{
		$output = '';
		foreach ($tree AS $element)
		{
			if (is_array($element))
			{
				$output .= $element['original'][0]
					. $this->renderSubTreePlain($element['children'])
					. $element['original'][1];
			}
			else
			{
				$output .= $element;
			}
		}

		return $output;
	}

	public function renderUnparsedTag(array $tag, array $options)
	{
		return $this->renderString($tag['original'][0], $options)
			. $this->renderSubTree($tag['children'], $options)
			. $this->renderString($tag['original'][1], $options);
	}

	public function filterFinalOutput($output)
	{
		return $output;
	}

	public function getRules()
	{
		return $this->rules;
	}
}