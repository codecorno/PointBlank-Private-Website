<?php

namespace XF\Api\Docs;

class AnnotationParser
{
	const BLOCK_ROUTE = 'route';
	const BLOCK_TYPE = 'type';

	protected $currentBlockStyle;
	protected $currentClassName;

	/**
	 * @var ClassParser|null
	 */
	protected $classParser;

	public function setClassParser(ClassParser $classParser)
	{
		$this->classParser = $classParser;
	}

	/**
	 * @param string $blockStyle
	 * @param string $annotation
	 * @param string|null $className
	 *
	 * @return Annotation\AbstractBlock
	 */
	public function parse($blockStyle, $annotation, $className = null)
	{
		switch ($blockStyle)
		{
			case self::BLOCK_ROUTE:
				$block = new Annotation\RouteBlock();
				break;

			case self::BLOCK_TYPE:
				$block = new Annotation\TypeBlock();
				break;

			default:
				throw new \LogicException("Block style must be a defined BLOCK_xxx constant");
		}

		$originalBlockStyle = $this->currentBlockStyle;
		$this->currentBlockStyle = $blockStyle;

		$originalClassName = $this->currentClassName;
		$this->currentClassName = $className;

		$annotation = preg_replace('#^\s*/\*+#', '', $annotation);
		$annotation = preg_replace('#\*+/\s*$#', '', $annotation);
		$annotation = trim($annotation);
		$lines = explode("\n", $annotation);

		$totalLines = count($lines);
		for ($i = 0; $i < $totalLines; $i++)
		{
			$line = $lines[$i];
			$line = preg_replace('#^\*+\s*#', '', trim($line));
			if (!strlen($line))
			{
				continue;
			}

			if (preg_match('#^@api-([a-zA-Z0-9_-]+)#', $line, $startMatch))
			{
				$lineType = strtolower($startMatch[1]);
				$lineValue = $this->trimOffStartMatch($line, $startMatch[0]);

				do
				{
					$continuePeeking = false;

					if (isset($lines[$i + 1]))
					{
						$peekLine = trim($lines[$i + 1]);
						if (preg_match('#^\*[ \t](    |\t)\s*(?!@)(?=\S)#', $peekLine, $peekLineMatch))
						{
							$peekLine = trim(substr($peekLine, strlen($peekLineMatch[0])));
							$lineValue .= ' ' . $peekLine;
							$i++;
							$continuePeeking = true;
						}
					}
				}
				while ($continuePeeking);

				$lineResult = $this->parseLine($lineType, $lineValue);
				if ($lineResult)
				{
					if ($lineResult->applyToBlock($block) === false)
					{
						$block->addUnknownLine($line);
					}
				}
				else
				{
					$block->addUnknownLine($line);
				}
			}
		}

		$this->currentBlockStyle = $originalBlockStyle;
		$this->currentClassName = $originalClassName;

		return $block;
	}

	/**
	 * @param string $type
	 * @param string $value
	 *
	 * @return Annotation\AbstractLine|null
	 */
	protected function parseLine($type, $value)
	{
		switch ($type)
		{
			case 'route': return $this->parseLineRoute($value);
			case 'type': return $this->parseLineType($value);
			case 'desc': return $this->parseLineDescription($value);
			case 'group': return $this->parseLineGroup($value);
			case 'incomplete': return $this->parseLineIncomplete($value);
			case 'in': return $this->parseLineIn($value);
			case 'out': return $this->parseLineOut($value);
			case 'error': return $this->parseLineError($value);
			case 'see': return $this->parseLineSee($value);
			default: return null;
		}
	}

	protected function parseLineRoute($value)
	{
		$parts = preg_split('/\s+/', $value, 2);
		if (count($parts) < 2)
		{
			// if only 1 part, then assume to be the route
			$parts = [null, $value];
		}
		else
		{
			// make sure the method is always in caps
			$parts[0] = strtoupper($parts[0]);
		}

		return new Annotation\RouteLine($parts[0], $parts[1]);
	}

	protected function parseLineType($value)
	{
		$parts = preg_split('/\s+/', $value, 2);
		if (count($parts) <= 2)
		{
			$parts = [$value, ''];
		}

		return new Annotation\TypeLine($parts[0], $parts[1]);
	}

	protected function parseLineDescription($value)
	{
		return new Annotation\DescriptionLine($value);
	}

	protected function parseLineGroup($value)
	{
		return new Annotation\GroupLine($value);
	}

	protected function parseLineIncomplete($value)
	{
		return new Annotation\IncompleteLine();
	}

	protected function parseLineIn($value)
	{
		return $this->parseValueLine('In', $value);
	}

	protected function parseLineOut($value)
	{
		return $this->parseValueLine('Out', $value);
	}

	/**
	 * @param string $classType
	 * @param string $value
	 *
	 * @return Annotation\AbstractValueLine
	 */
	protected function parseValueLine($classType, $value)
	{
		if (preg_match('#^<([a-z0-9_\-\|]+)>#i', $value, $match))
		{
			$modifiers = explode('|', $match[1]);
			$value = $this->trimOffStartMatch($value, $match[0]);
		}
		else
		{
			$modifiers = [];
		}

		if (preg_match('#^([a-z0-9_\-\|\[\]<>]+)#i', $value, $match))
		{
			$type = $match[1];
			$value = $this->trimOffStartMatch($value, $match[0]);
		}
		else
		{
			$type = 'mixed';
		}

		if (preg_match('#^\$([a-z0-9_\-\|\[\]<>]+)#i', $value, $match))
		{
			$name = $match[1];
			$value = $this->trimOffStartMatch($value, $match[0]);
		}
		else
		{
			// name omitted, so assume the type is the name without the $ and make the type be mixed
			$name = $type;
			$type = 'mixed';
		}

		if (!$modifiers)
		{
			if (preg_match('#^<([a-z0-9_\-\|]+)>#i', $value, $match))
			{
				$modifiers = explode('|', $match[1]);
				$value = $this->trimOffStartMatch($value, $match[0]);
			}
			else
			{
				$modifiers = [];
			}
		}

		$description = trim($value);
		$types = explode('|', $type);

		$class = '\XF\Api\Docs\Annotation\\' . $classType . 'Line';
		return new $class($name, $description, $types, $modifiers);
	}

	protected function parseLineError($value)
	{
		$parts = preg_split('/\s+/', $value, 2);
		if (count($parts) < 2)
		{
			$parts = [$value, ''];
		}

		return new Annotation\ErrorLine($parts[0], $parts[1]);
	}

	protected function parseLineSee($value)
	{
		if (!$this->classParser)
		{
			throw new \LogicException("A class parser must be available to parse @api-see");
		}

		if (!preg_match('#^([a-z0-9_\\\\]+)::([a-z0-9_]+)(\(\))?$#i', $value, $match))
		{
			return null;
		}

		$class = $match[1];
		$method = $match[2];

		if ($class == 'self')
		{
			if (!$this->currentClassName)
			{
				return null;
			}

			$class = $this->currentClassName;
		}

		$result = $this->classParser->parseClassMethod($this->currentBlockStyle, $class, $method);
		if (!$result)
		{
			return null;
		}

		return new Annotation\SeeLine($result);
	}

	protected function trimOffStartMatch($line, $match)
	{
		if (is_int($match))
		{
			$length = $match;
		}
		else
		{
			$length = strlen($match);
		}

		return ltrim(strval(substr($line, $length)));
	}

}