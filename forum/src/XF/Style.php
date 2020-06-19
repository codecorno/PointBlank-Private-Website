<?php

namespace XF;

class Style implements \ArrayAccess
{
	protected $id;

	protected $lastModified;

	protected $properties;

	protected $options = [
		'parent_id' => 0,
		'parent_list' => '',
		'title' => '',
		'description' => '',
		'user_selectable' => 1
	];

	public function __construct($id, array $properties, $lastModified = null, array $options = null)
	{
		if ($lastModified === null && $options === null)
		{
			$lastModified = $properties['last_modified_date'];

			$realProperties = $properties['properties'];
			$options = $properties;
			unset($options['last_modified_date'], $options['properties']);

			$properties = $realProperties;
		}

		$lastModified = intval($lastModified);
		if (!$lastModified)
		{
			$lastModified = \XF::$time;
		}
		if (!is_array($options))
		{
			$options = [];
		}

		$this->id = $id;
		$this->properties = $properties;
		$this->lastModified = $lastModified;
		$this->options = $options;
	}

	public function getId()
	{
		return $this->id;
	}

	public function setLastModified($lastModified)
	{
		$this->lastModified = (int)$lastModified;
	}

	public function getLastModified()
	{
		return $this->lastModified;
	}

	public function getProperty($name, $fallback = '')
	{
		if (!$this->properties)
		{
			return $fallback;
		}

		// most cases won't be a sub-property reference, so check for the simple case first
		if (isset($this->properties[$name]))
		{
			return $this->properties[$name];
		}

		// check for a sub.property style reference
		if (strpos($name, '--'))
		{
			list ($name, $subName) = explode('--', $name, 2);
			if (isset($this->properties[$name][$subName]))
			{
				return $this->properties[$name][$subName];
			}
		}

		return $fallback;
	}

	public function getCssProperty($name, $filters = null)
	{
		if (!$this->properties || !isset($this->properties[$name]) || !is_array($this->properties[$name]))
		{
			return '';
		}

		if (is_string($filters))
		{
			$filters = preg_split('/,\s*/', $filters);
		}
		if (!is_array($filters))
		{
			$filters = [];
		}

		return $this->compileCssPropertyValue($this->properties[$name], $filters);
	}

	public function compileCssPropertyValue(array $css, array $filters = [])
	{
		$include = [
			'text' => true,
			'background' => true,
			'border' => true,
			'border-radius' => true,
			'padding' => true,
			'extra' => true
		];
		$hasPositiveReset = false;
		foreach ($filters AS $filter)
		{
			if (isset($include[$filter]))
			{
				// positive match - remove everything on the first one, then add up
				if (!$hasPositiveReset)
				{
					foreach ($include AS &$included)
					{
						$included = false;
					}
					$hasPositiveReset = true;
				}
				$include[$filter] = true;
			}
			else if (substr($filter, 0, 3) == 'no-')
			{
				$noFilter = substr($filter, 3);
				if (isset($include[$noFilter]))
				{
					// negative match - just remove this one
					$include[$noFilter] = false;
				}
				else if (isset($css[$noFilter]))
				{
					unset($css[$noFilter]);
				}
			}
		}

		$output = [];

		$outputSimple = function($name) use(&$output, $css)
		{
			if (isset($css[$name]))
			{
				$output[] = "$name: " . $css[$name] . ';';
			}
		};

		if ($include['text'])
		{
			$outputSimple('font-size');
			$outputSimple('color');
			$outputSimple('font-weight');
			$outputSimple('font-style');
			$outputSimple('text-decoration');
		}

		if ($include['background'])
		{
			if (isset($css['background-color']) || isset($css['background-image']))
			{
				$output[] = 'background: '
					. trim(
						(isset($css['background-color']) ? $css['background-color'] : '')
						. ' '
						. (isset($css['background-image']) ? $css['background-image'] : '')
					)
					. ';';
			}
		}

		if ($include['border'])
		{
			$hasGeneralBorderStyle = false;

			if (isset($css['border-width'], $css['border-color']))
			{
				$output[] = 'border: ' . $css['border-width'] . ' solid ' . $css['border-color'] . ';';
				$hasGeneralBorderStyle = true;
			}
			else
			{
				$outputSimple('border-width');
				if (isset($css['border-width']))
				{
					$output[] = 'border-style: solid;';
					$hasGeneralBorderStyle = true;
				}
				$outputSimple('border-color');
			}

			$outputBorderSide = function($side) use (&$output, $css, $outputSimple, $hasGeneralBorderStyle)
			{
				$width = "border-{$side}-width";
				$color = "border-{$side}-color";
				$sideProperty = "border-{$side}";

				if (isset($css[$width], $css[$color]))
				{
					$output[$sideProperty] = $sideProperty . ': ' . $css[$width] . ' solid ' . $css[$color] . ';';
				}
				else
				{
					$outputSimple($width);
					if (isset($css[$width]) && !$hasGeneralBorderStyle)
					{
						$output[] = "border-{$side}-style: solid;";
					}
					$outputSimple($color);
				}
			};
			$outputBorderSide('top');
			$outputBorderSide('right');
			$outputBorderSide('bottom');
			$outputBorderSide('left');
		}

		if ($include['border-radius'])
		{
			$outputSimple('border-radius');
			$outputSimple('border-top-left-radius');
			$outputSimple('border-top-right-radius');
			$outputSimple('border-bottom-right-radius');
			$outputSimple('border-bottom-left-radius');
		}

		if ($include['padding'])
		{
			$outputSimple('padding');
			$outputSimple('padding-top');
			$outputSimple('padding-right');
			$outputSimple('padding-bottom');
			$outputSimple('padding-left');
		}

		if ($include['extra'] && isset($css['extra']))
		{
			$output[] = $css['extra'];
		}

		$return = trim(implode("\n", $output));
		$return = "\t" . str_replace("\n", "\n\t", $return);

		return $return;
	}

	public function getProperties()
	{
		return $this->properties ?: [];
	}

	public function setProperties(array $properties)
	{
		$this->properties = $properties;
	}

	public function offsetGet($key)
	{
		switch ($key)
		{
			case 'style_id': return $this->id;
			case 'last_modified_date': return $this->lastModified;
			default: return $this->options[$key];
		}
	}

	public function offsetExists($key)
	{
		switch ($key)
		{
			case 'style_id':
			case 'last_modified_date':
				return true;

			default:
				return isset($this->options[$key]);
		}
	}

	public function offsetSet($key, $value)
	{
		throw new \LogicException("Style object options cannot be written to.");
	}

	public function offsetUnset($key)
	{
		throw new \LogicException("Style object options cannot be written to.");
	}
}