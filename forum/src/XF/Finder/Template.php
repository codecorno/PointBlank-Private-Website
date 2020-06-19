<?php

namespace XF\Finder;

use XF\Mvc\Entity\Finder;

class Template extends Finder
{
	public function fromAddOn($addOnId)
	{
		if ($addOnId == '_any')
		{
			return $this;
		}
		$this->where('addon_id', $addOnId);
		return $this;
	}

	public function searchTitle($match)
	{
		if ($match)
		{
			$this->where(
				$this->columnUtf8('title'),
				'LIKE',
				$this->escapeLike($match, '%?%')
			);
		}

		return $this;
	}

	public function searchTemplate($match, $caseSensitive = false)
	{
		if ($match)
		{
			$expression = 'template';
			if ($caseSensitive)
			{
				$expression = $this->expression('BINARY %s', $expression);
			}

			$this->where($expression, 'LIKE', $this->escapeLike($match, '%?%'));
		}

		return $this;
	}

	public function orderTitle($direction = 'ASC')
	{
		$expression = $this->columnUtf8('title');
		$this->order($expression, $direction);

		return $this;
	}
}