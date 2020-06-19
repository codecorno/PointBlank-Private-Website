<?php

namespace XF;

class Diff3
{
	const UPDATE = 'u';
	const CONFLICT = 'c';
	const EQUAL  = 'e';

	const MINE = 'm';
	const YOURS = 'y';
	const BOTH = 'b';

	/**
	 * @var Diff
	 */
	protected $comparer;

	protected $currentOrig;
	protected $currentMine;
	protected $currentYours;

	protected $bigConflicts = true;

	public function __construct(Diff $comparer = null)
	{
		if (!$comparer)
		{
			$comparer = new Diff();
		}

		$this->comparer = $comparer;
	}

	protected function initBlock()
	{
		$this->currentOrig = $this->currentMine = $this->currentYours = [];
	}

	protected function appendOrig(array $blocks)
	{
		array_splice($this->currentOrig, count($this->currentOrig), 0, $blocks);
	}

	protected function appendMine(array $blocks)
	{
		array_splice($this->currentMine, count($this->currentMine), 0, $blocks);
	}

	protected function appendYours(array $blocks)
	{
		array_splice($this->currentYours, count($this->currentYours), 0, $blocks);
	}

	protected function finishBlock(array &$output, $init = true)
	{
		if ($this->currentMine || $this->currentYours || $this->currentOrig)
		{
			if ($this->currentMine == $this->currentYours)
			{
				if ($this->currentMine == $this->currentOrig)
				{
					$output[] = [self::EQUAL, $this->currentMine];
				}
				else
				{
					$output[] = [self::UPDATE, $this->currentMine, $this->currentOrig, self::BOTH];
				}
			}
			else if ($this->currentMine == $this->currentOrig)
			{
				$output[] = [self::UPDATE, $this->currentYours, $this->currentOrig, self::YOURS];
			}
			else if ($this->currentYours == $this->currentOrig)
			{
				$output[] = [self::UPDATE, $this->currentMine, $this->currentOrig, self::MINE];
			}
			else
			{
				// potential conflict - try to resolve prefixes
				$cM = $this->currentMine;
				$cY = $this->currentYours;
				$cO = $this->currentOrig;

				if ($init)
				{
					$childOutput = [];
				}
				else
				{
					$childOutput =& $output;
				}

				if ($i = $this->getMatchLength($this->currentMine, $this->currentYours))
				{
					$update = array_splice($this->currentMine, 0, $i);
					array_splice($this->currentYours, 0, $i);
					$childOutput[] = [self::UPDATE, $update, [], self::BOTH];
					$this->finishBlock($childOutput, false);
				}
				else if ($i = $this->getMatchLength($this->currentMine, $this->currentOrig))
				{
					$update = array_splice($this->currentMine, 0, $i);
					array_splice($this->currentOrig, 0, $i);
					$childOutput[] = [self::UPDATE, [], $update, self::YOURS];
					$this->finishBlock($childOutput, false);
				}
				else if ($i = $this->getMatchLength($this->currentYours, $this->currentOrig))
				{
					$update = array_splice($this->currentYours, 0, $i);
					array_splice($this->currentOrig, 0, $i);
					$childOutput[] = [self::UPDATE, [], $update, self::MINE];
					$this->finishBlock($childOutput, false);
				}
				else if ($i = $this->getEndMatchLength($this->currentMine, $this->currentYours))
				{
					$update = array_splice($this->currentMine, -$i);
					array_splice($this->currentYours, -$i);
					$this->finishBlock($childOutput, false);
					$childOutput[] = [self::UPDATE, $update, [], self::BOTH];
				}
				else if ($i = $this->getEndMatchLength($this->currentMine, $this->currentOrig))
				{
					$update = array_splice($this->currentMine, -$i);
					array_splice($this->currentOrig, -$i);
					$this->finishBlock($childOutput, false);
					$childOutput[] = [self::UPDATE, [], $update, self::YOURS];
				}
				else if ($i = $this->getEndMatchLength($this->currentYours, $this->currentOrig))
				{
					$update = array_splice($this->currentYours, -$i);
					array_splice($this->currentOrig, -$i);
					$this->finishBlock($childOutput, false);
					$childOutput[] = [self::UPDATE, [], $update, self::MINE];
				}
				else if (!$init)
				{
					$childOutput[] = [self::CONFLICT, $this->currentMine, $this->currentOrig, $this->currentYours];
				}

				if ($init)
				{
					if ($childOutput)
					{
						$hasConflict = false;
						foreach ($childOutput AS $child)
						{
							if ($child[0] == self::CONFLICT)
							{
								$hasConflict = true;
								break;
							}
						}
						if ($hasConflict && $this->bigConflicts)
						{
							// still have a conflict, just mark the whole thing as it originally was
							$output[] = [self::CONFLICT, $cM, $cO, $cY];
						}
						else
						{
							// no longer have a conflict or we want the small conflicts
							array_splice($output, count($output), 0, $childOutput);
						}
					}
					else
					{
						// couldn't find any matching bits
						$output[] = [self::CONFLICT, $this->currentMine, $this->currentOrig, $this->currentYours];
					}
				}
			}
		}

		if ($init)
		{
			$this->initBlock();
		}
	}

	protected function getMatchLength(array $blocks1, array $blocks2)
	{
		$i = 0;
		while (isset($blocks1[$i]) && isset($blocks2[$i]) && $blocks1[$i] === $blocks2[$i])
		{
			$i++;
		}

		return $i;
	}

	protected function getEndMatchLength(array $blocks1, array $blocks2)
	{
		$match = 0;
		$end1 = count($blocks1) - 1;
		$end2 = count($blocks2) - 1;
		while (isset($blocks1[$end1]) && isset($blocks2[$end2]) && $blocks1[$end1] === $blocks2[$end2])
		{
			$match++;
			$end1--;
			$end2--;
		}

		return $match;
	}

	public function findDifferences($mine, $original, $yours, $type = Diff::DIFF_TYPE_LINE)
	{
		$mineDiff = $this->comparer->findDifferences($original, $mine, $type);
		$yourDiff = $this->comparer->findDifferences($original, $yours, $type);

		$output = [];
		$this->initBlock();

		$m = reset($mineDiff);
		$y = reset($yourDiff);
		while ($m || $y)
		{
			if ($m && $y)
			{
				$mType = $m[0];
				$mBlocks =& $m[1];

				$yType = $y[0];
				$yBlocks =& $y[1];

				if ($mType == Diff::EQUAL && $yType == Diff::EQUAL)
				{
					$this->finishBlock($output);

					$i = $this->getMatchLength($mBlocks, $yBlocks);
					if (!$i)
					{
						throw new \LogicException("Both equal but no leading match?");
					}

					$matches = array_splice($mBlocks, 0, $i);
					$output[] = [self::EQUAL, $matches];
					array_splice($yBlocks, 0, $i);

					if (!$mBlocks)
					{
						$m = next($mineDiff);
					}
					if (!$yBlocks)
					{
						$y = next($yourDiff);
					}
				}
				else if ($mType == Diff::INSERT)
				{
					$this->appendMine($mBlocks);
					$m = next($mineDiff);
				}
				else if ($yType == Diff::INSERT)
				{
					$this->appendYours($yBlocks);
					$y = next($yourDiff);
				}
				else if ($mType == Diff::DELETE && $yType == Diff::DELETE)
				{
					$this->finishBlock($output);

					$i = $this->getMatchLength($mBlocks, $yBlocks);
					if (!$i)
					{
						throw new \LogicException("Both deletes but no leading match?");
					}

					$this->appendOrig(array_splice($mBlocks, 0, $i));
					array_splice($yBlocks, 0, $i);

					if (!$mBlocks)
					{
						$m = next($mineDiff);
					}
					if (!$yBlocks)
					{
						$y = next($yourDiff);
					}
				}
				else if ($mType == Diff::DELETE && $yType == Diff::EQUAL)
				{
					$min = min(count($mBlocks), count($yBlocks));

					array_splice($mBlocks, 0, $min); // removed
					$block = array_splice($yBlocks, 0, $min);
					$this->appendOrig($block);
					$this->appendYours($block);

					if (!$mBlocks)
					{
						$m = next($mineDiff);
					}
					if (!$yBlocks)
					{
						$y = next($yourDiff);
					}
				}
				else if ($yType == Diff::DELETE && $mType == Diff::EQUAL)
				{
					$min = min(count($mBlocks), count($yBlocks));

					array_splice($yBlocks, 0, $min); // removed
					$block = array_splice($mBlocks, 0, $min);
					$this->appendOrig($block);
					$this->appendMine($block);

					if (!$mBlocks)
					{
						$m = next($mineDiff);
					}
					if (!$yBlocks)
					{
						$y = next($yourDiff);
					}
				}
			}
			else if ($m)
			{
				if ($m[0] != Diff::INSERT)
				{
					throw new \LogicException("Had m only but wasn't insert");
				}

				$this->appendMine($m[1]);
				$m = next($mineDiff);
			}
			else if ($y)
			{
				if ($y[0] != Diff::INSERT)
				{
					throw new \LogicException("Had y only but wasn't insert");
				}

				$this->appendYours($y[1]);
				$y = next($yourDiff);
			}
		}

		$this->finishBlock($output);

		return $this->finalize($output);
	}

	protected function finalize(array $output)
	{
		$newOutput = [];
		$i = -1;
		$lastType = null;
		foreach ($output AS $hunk)
		{
			if ($hunk[0] == self::CONFLICT && $lastType === self::CONFLICT)
			{
				// back to back conflicts: merge
				$newOutput[$i][1] = array_merge($newOutput[$i][1], $hunk[1]);
				$newOutput[$i][2] = array_merge($newOutput[$i][2], $hunk[2]);
				$newOutput[$i][3] = array_merge($newOutput[$i][3], $hunk[3]);
			}
			else
			{
				$i++;
				$newOutput[$i] = $hunk;
				$lastType = $hunk[0];
			}
		}

		return $newOutput;
	}

	public function mergeToFinal($mine, $original, $yours, $type = Diff::DIFF_TYPE_LINE)
	{
		$diffs = $this->findDifferences($mine, $original, $yours, $type);
		$output = [];

		foreach ($diffs AS $diff)
		{
			if ($diff[0] == self::CONFLICT)
			{
				return false;
			}

			array_splice($output, count($output), 0, $diff[1]);
		}

		switch ($type)
		{
			case Diff::DIFF_TYPE_CHAR:
				$joiner = '';
				break;

			case Diff::DIFF_TYPE_WORD:
				$joiner = ' ';
				break;

			case Diff::DIFF_TYPE_LINE:
			default:
				$joiner = "\n";
		}

		return implode($joiner, $output);
	}
}