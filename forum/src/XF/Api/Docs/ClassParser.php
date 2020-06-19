<?php

namespace XF\Api\Docs;

use XF\Api\Docs\Annotation\OutLine;
use XF\Api\Docs\Annotation\RouteBlock;
use XF\Mvc\Entity\Entity;

class ClassParser
{
	/**
	 * @var AnnotationParser
	 */
	protected $annotationParser;

	public function __construct(AnnotationParser $annotationParser)
	{
		$this->annotationParser = $annotationParser;
	}

	public function parseControllerClass($shortName, $baseRoute = '')
	{
		$className = \XF::stringToClass($shortName, '%s\%s\Controller\%s', 'Api');

		$reflection = new \ReflectionClass($className);
		if (!$reflection || $reflection->isAbstract())
		{
			return null;
		}

		$defaultGroup = null;

		$classDocBlock = $reflection->getDocComment();
		if ($classDocBlock)
		{
			$classBlock = $this->parseRouteBlock($classDocBlock, $className);
			if ($classBlock->group)
			{
				$defaultGroup = $classBlock->group;
			}
		}

		if (!$defaultGroup)
		{
			$defaultGroup = preg_replace('#/.*$#', '', $baseRoute);
		}

		$routes = [];
		$baseRoute = rtrim($baseRoute, '/');

		foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) AS $method)
		{
			if (!preg_match('#^action(Get|Post|Delete|Put|Patch)(.*)$#i', $method->name, $nameMatch))
			{
				continue;
			}

			$docComment = $method->getDocComment();
			if ($docComment)
			{
				$block = $this->parseRouteBlock($docComment, $className);
			}
			else
			{
				// no docs, but we can use the route and action info to at least mention it
				$block = new RouteBlock();
				$block->incomplete = true;
			}

			if (!$block->method)
			{
				$block->method = strtoupper($nameMatch[1]);
			}
			if (!$block->route)
			{
				$block->route = $baseRoute . '/' . $this->convertActionToRoute($nameMatch[2]);
			}
			if (!$block->group)
			{
				$block->group = $defaultGroup;
			}

			$routes["{$block->method} {$block->route}"] = $block;
		}

		return $routes;
	}

	protected function convertActionToRoute($string)
	{
		return strtolower(preg_replace(['/([a-z\d])([A-Z])/', '/([^\-])([A-Z][a-z])/'], '$1-$2', $string));
	}

	public function parseEntityClass($shortName)
	{
		$className = \XF::stringToClass($shortName, '%s\Entity\%s');

		$reflection = new \ReflectionClass($className);
		if (!$reflection || $reflection->isAbstract() || !$reflection->hasMethod('setupApiResultData'))
		{
			return null;
		}

		$method = $reflection->getMethod('setupApiResultData');
		if ($method->class == 'XF\Mvc\Entity\Entity')
		{
			// method hasn't been overridden, so ignore
			return null;
		}

		$docComment = $method->getDocComment();
		if (!$docComment)
		{
			return null;
		}

		$block = $this->parseTypeBlock($docComment, $className);
		$this->addToTypeFromEntityStructure($block, $shortName);

		if (!$block->structure)
		{
			return null;
		}

		if (!$block->type)
		{
			$block->type = $this->getTypeNameFromShortName($shortName);
		}

		return $block;
	}

	protected function addToTypeFromEntityStructure(Annotation\TypeBlock $block, $entityName)
	{
		$structure = \XF::em()->getEntityStructure($entityName);

		foreach ($structure->columns AS $name => $column)
		{
			if (isset($block->structure[$name]))
			{
				continue;
			}

			if (empty($column['api']) && empty($column['autoIncrement']))
			{
				continue;
			}

			switch ($column['type'])
			{
				case Entity::INT:
				case Entity::UINT:
					$type = 'int';
					break;

				case Entity::FLOAT:
					$type = 'float';
					break;

				case Entity::BOOL:
					$type = 'bool';
					break;

				case Entity::STR:
				case Entity::BINARY:
					$type = 'str';
					break;

				case Entity::SERIALIZED:
				case Entity::JSON:
					$type = 'mixed';
					break;

				case Entity::JSON_ARRAY:
				case Entity::LIST_LINES:
				case Entity::LIST_COMMA:
				case Entity::SERIALIZED_ARRAY:
					$type = 'array';
					break;

				default:
					$type = 'mixed';
			}

			$block->structure[$name] = new OutLine($name, '', [$type]);
		}

		foreach ($structure->relations AS $name => $relation)
		{
			if (isset($block->structure[$name]))
			{
				continue;
			}

			if (empty($relation['api']))
			{
				continue;
			}

			$subType = $this->getTypeNameFromShortName($relation['entity']);

			if ($relation['type'] == Entity::TO_MANY)
			{
				$subType .= '[]';
			}

			$block->structure[$name] = new OutLine($name, '', [$subType]);
		}
	}

	protected function getTypeNameFromShortName($shortName)
	{
		$nameParts = explode(':', $shortName, 2);
		if ($nameParts[0] == 'XF')
		{
			// XF is just the entity name
			return $nameParts[1];
		}
		else
		{
			return str_replace('\\', '_', $nameParts[0]) . '_' . $nameParts[1];
		}
	}

	public function parseClassMethod($commentType, $className, $method)
	{
		$reflection = new \ReflectionClass($className);
		if (!$reflection)
		{
			return null;
		}

		if (!$reflection->hasMethod($method))
		{
			return null;
		}

		$methodReflection = $reflection->getMethod($method);
		if (!$methodReflection)
		{
			return null;
		}

		$docComment = $methodReflection->getDocComment();
		if (!$docComment)
		{
			return null;
		}

		switch ($commentType)
		{
			case AnnotationParser::BLOCK_ROUTE:
				return $this->parseRouteBlock($docComment, $className);

			case AnnotationParser::BLOCK_TYPE:
				return $this->parseTypeBlock($docComment, $className);

			default:
				throw new \LogicException("Unknown block type encountered (" . $commentType . ')');
		}
	}

	/**
	 * @param string $comment
	 * @param string|null $className
	 *
	 * @return Annotation\TypeBlock
	 */
	public function parseTypeBlock($comment, $className = null)
	{
		return $this->annotationParser->parse(AnnotationParser::BLOCK_TYPE, $comment, $className);
	}

	/**
	 * @param string $comment
	 * @param string|null $className
	 *
	 * @return Annotation\RouteBlock
	 */
	public function parseRouteBlock($comment, $className = null)
	{
		return $this->annotationParser->parse(AnnotationParser::BLOCK_ROUTE, $comment, $className);
	}
}