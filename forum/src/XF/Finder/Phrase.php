<?php

namespace XF\Finder;

use XF\Mvc\Entity\Finder;

class Phrase extends Finder
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

	public function searchTitle($match, $prefixMatch = false)
	{
		if ($match)
		{
			$this->where(
				$this->columnUtf8('title'),
				'LIKE',
				$this->escapeLike($match, $prefixMatch ? '?%' : '%?%')
			);
		}

		return $this;
	}

	public function searchText($match, $caseSensitive = false)
	{
		if ($match)
		{
			$expression = 'phrase_text';
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