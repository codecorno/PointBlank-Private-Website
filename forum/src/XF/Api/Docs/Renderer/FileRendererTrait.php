<?php

namespace XF\Api\Docs\Renderer;

trait FileRendererTrait
{
	protected $target;

	public function setTarget($target, &$error = null, $force = false)
	{
		if (!$target)
		{
			$this->target = null;
			return true;
		}

		if (file_exists($target))
		{
			if (is_dir($target))
			{
				$error = 'Target is a directory. Please enter a file name.';
				return false;
			}

			if (!$force)
			{
				$error = 'Target file already exists. Cannot override without force option.';
				return false;
			}

			if (!is_writable($target))
			{
				$error = 'Target file is not writable.';
				return false;
			}

			$this->target = $target;
			return true;
		}

		$dir = dirname($target);
		if (!file_exists($dir) || !is_dir($dir))
		{
			$error = 'Target\'s parent directly does not exist or is not valid.';
			return false;
		}

		if (!is_writable($dir))
		{
			$error = 'Target\'s parent directory is not writable.';
			return false;
		}

		$this->target = $target;
		return true;
	}

	public function render(array $routeGroupings, array $types)
	{
		$result = $this->renderInternal($routeGroupings, $types);

		if ($this->target)
		{
			file_put_contents($this->target, $result);
			return "Written to {$this->target} successfully.";
		}
		else
		{
			return $result;
		}
	}

	abstract public function renderInternal(array $routeGroupings, array $types);
}