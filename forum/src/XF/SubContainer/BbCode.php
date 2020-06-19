<?php

namespace XF\SubContainer;

use XF\Container;

class BbCode extends AbstractSubContainer
{
	public function initialize()
	{
		$container = $this->container;

		$container['parser'] = function(Container $c)
		{
			$class = $this->extendClass('XF\BbCode\Parser');
			return new $class();
		};

		$container->factory('rules', function($context, array $params, Container $c)
		{
			$parts = explode(':', $context, 2);
			if (count($parts) == 2)
			{
				$context = $parts[0];
				$subContext = $parts[1];
			}
			else
			{
				$subContext = null;
			}

			$class = $this->extendClass('XF\BbCode\RuleSet');
			/** @var \XF\BbCode\RuleSet $ruleSet */
			$ruleSet = new $class($context, $subContext);
			foreach ($c['custom'] AS $tag => $customBbCode)
			{
				$ruleSet->addTag($tag, $ruleSet->getCustomTagConfig($customBbCode));
			}

			$this->app->fire('bb_code_rules', [$ruleSet, $context, $subContext], $context);

			return $ruleSet;
		});

		$container->factory('renderer', function($type, array $params, Container $c)
		{
			$originalType = $type;

			$map = $c['rendererMap'];
			if (isset($map[$type]))
			{
				$type = $map[$type];
			}

			$class = \XF::stringToClass($type, '%s\BbCode\Renderer\%s');
			$class = $this->extendClass($class);

			if (!class_exists($class))
			{
				throw new \InvalidArgumentException("Unknown renderer class '$class'");
			}

			/** @var \XF\BbCode\Renderer\AbstractRenderer $renderer */
			$renderer = $class::factory($this->app);

			foreach ($c['custom'] AS $tag => $customBbCode)
			{
				$renderer->addTag($tag, $renderer->getCustomTagConfig($customBbCode));
			}

			$this->app->fire('bb_code_renderer', [$renderer, $originalType], $originalType);

			return $renderer;
		});

		$container['rendererMap'] = function(Container $c)
		{
			$rendererMap = [
				'bbCodeClean' => 'XF:BbCodeClean',
				'editorHtml' => 'XF:EditorHtml',
				'emailHtml' => 'XF:EmailHtml',
				'html' => 'XF:Html',
				'simpleHtml' => 'XF:SimpleHtml'
			];

			$this->app->fire('bb_code_renderer_map', [&$rendererMap]);

			return $rendererMap;
		};

		$container->set('processor', function(Container $c)
		{
			return new \XF\BbCode\Processor();
		}, false);

		$container->factory('processorAction', function($type, array $params, Container $c)
		{
			$map = $c['processorActionMap'];
			if (isset($map[$type]))
			{
				$type = $map[$type];
			}

			$class = \XF::stringToClass($type, '%s\BbCode\ProcessorAction\%s');
			$class = $this->extendClass($class);

			if (is_callable([$class, 'factory']))
			{
				array_unshift($params, $this->app);
				return call_user_func_array([$class, 'factory'], $params);
			}
			else
			{
				return $c->createObject($class, $params);
			}
		}, false);

		$container['processorActionMap'] = function()
		{
			$processorActionMap = [
				'usage' => 'XF:AnalyzeUsage',
				'autolink' => 'XF:AutoLink',
				'censor' => 'XF:Censor',
				'mentions' => 'XF:MentionUsers',
				'quotes' => 'XF:StripQuotes',
				'limit' => 'XF:LimitTags',
				'markdown' => 'XF:Markdown',
				'shortToEmoji' => 'XF:ShortToEmoji',
				'structuredText' => 'XF:StructuredText'
			];

			$this->app->fire('bb_code_processor_action_map', [&$processorActionMap]);

			return $processorActionMap;
		};

		$container['custom'] = $this->fromRegistry('bbCodeCustom',
			function(Container $c) { return $this->app['em']->getRepository('XF:BbCode')->rebuildBbCodeCache(); }
		);
		$container['media'] = $this->fromRegistry('bbCodeMedia',
			function(Container $c) { return $this->app['em']->getRepository('XF:BbCodeMediaSite')->rebuildBbCodeMediaSiteCache(); }
		);
	}

	/**
	 * @return \XF\BbCode\Parser
	 */
	public function parser()
	{
		return $this->container['parser'];
	}

	/**
	 * @param string $context
	 *
	 * @return \XF\BbCode\RuleSet
	 */
	public function rules($context)
	{
		return $this->container->create('rules', $context);
	}

	/**
	 * @param string $type
	 *
	 * @return \XF\BbCode\Renderer\AbstractRenderer
	 */
	public function renderer($type)
	{
		return $this->container->create('renderer', $type);
	}

	/**
	 * @return \XF\BbCode\Processor
	 */
	public function processor()
	{
		return $this->container['processor'];
	}

	/**
	 * @param string $type
	 *
	 * @return \XF\BbCode\ProcessorAction\AnalyzerInterface|\XF\BbCode\ProcessorAction\FiltererInterface
	 */
	public function processorAction($type)
	{
		$args = func_get_args();
		unset($args[0]);

		return $this->container->create('processorAction', $type, $args);
	}

	/**
	 * @param string $string String to render
	 * @param string $type Type of renderer
	 * @param string $context Context of rendering
	 * @param mixed $content The content that is being rendered; generally an entity if available, or a user, or null if nothing available
	 * @param array $options
	 *
	 * @return string
	 */
	public function render($string, $type, $context, $content, $options = [])
	{
		$parser = $this->parser();
		$rules = $this->rules($context);
		$renderer = $this->renderer($type);

		if (is_array($content) && func_num_args() == 4)
		{
			\XF::logError('XF\SubContainer\BbCode::render() called with 4th argument as array and no options, pass $content explicitly');
			$options = $content;
		}
		else
		{
			$options = $this->getFullRenderOptions($content, $context, $type, $options);
		}

		return $renderer->render($string, $parser, $rules, $options);
	}

	public function getFullRenderOptions($content, $context, $type, array $options = [])
	{
		$options = array_replace($this->getDefaultRenderOptions($content, $context, $type), $options);

		if ($content instanceof \XF\BbCode\RenderableContentInterface)
		{
			$options = array_replace($content->getBbCodeRenderOptions($context, $type), $options);
		}
		else if ($content instanceof \XF\Entity\User)
		{
			$options['user'] = $content;
		}
		else if ($content instanceof \XF\Mvc\Entity\Entity)
		{
			$options['entity'] = $content;
		}

		return $options;
	}

	protected function getDefaultRenderOptions($content, $context, $type)
	{
		if ($context == 'user:signature')
		{
			return ['allowUnfurl' => false];
		}

		return ['allowUnfurl' => \XF::options()->urlToRichPreview];
	}
}