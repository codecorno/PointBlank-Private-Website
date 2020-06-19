<?php

namespace XF;

class InputFiltererArray
{
	/**
	 * @var InputFilterer
	 */
	protected $filterer;

	/**
	 * @var array
	 */
	protected $input;

	public function __construct(InputFilterer $filterer, array $input = [])
	{
		$this->filterer = $filterer;
		$this->input = $input;
	}

	public function getInput()
	{
		return $this->input;
	}

	public function setInput(array $input)
	{
		$this->input = $input;
	}

	public function get($key, $fallback = false)
	{
		if (array_key_exists($key, $this->input))
		{
			return $this->input[$key];
		}
		else
		{
			return $fallback;
		}
	}

	public function filter($key, $type = null, $default = null)
	{
		// TODO: this is roughly duplicated from Http\Request. Can likely do some sharing.
		// (Request supports more "x.y.z" sub-name format though.)

		if (is_array($key) && $type === null)
		{
			$output = [];
			foreach ($key AS $name => $value)
			{
				if (is_array($value))
				{
					$array = $this->get($name);
					if (!is_array($array))
					{
						$array = [];
					}
					$output[$name] = $this->filterer->filterArray($array, $value);
				}
				else
				{
					$output[$name] = $this->filter($name, $value);
				}
			}

			return $output;
		}
		else
		{
			$value = $this->get($key, $default);

			if (is_string($type) && $type[0] == '?')
			{
				if ($value === null)
				{
					return null;
				}

				$type = substr($type, 1);
			}

			if (is_array($type))
			{
				if (!is_array($value))
				{
					$value = [];
				}

				return $this->filterer->filterArray($value, $type);
			}
			else
			{
				return $this->filterer->filter($value, $type);
			}
		}
	}
}