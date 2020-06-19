<?php

namespace XF;

class Diff
{
	const DIFF_TYPE_LINE = 1;
	const DIFF_TYPE_CHAR = 2;
	const DIFF_TYPE_WORD = 3;

	const DELETE = 'd';
	const INSERT = 'i';
	const EQUAL  = 'e';

	public function findDifferences($oldText, $newText, $type = self::DIFF_TYPE_LINE)
	{
		$old = $this->split($oldText, $type);
		$new = $this->split($newText, $type);

		if ($this->preCompare($old, $new, $diffsPrefix, $diffsSuffix, $type))
		{
			// completed and whole diff is in prefix
			return $diffsPrefix;
		}

		$diffs = $this->compareGreedy($old, $new);
		$diffs = array_merge($diffsPrefix, $diffs, $diffsSuffix);
		$diffs = $this->cleanUp($diffs, $type);

		return $diffs;
	}

	protected function preCompare(array &$old, array &$new, &$diffsPrefix, &$diffsSuffix, $type)
	{
		$diffsPrefix = [];
		$diffsSuffix = [];

		if (!$old && !$new)
		{
			return true;
		}
		else if (!$old)
		{
			$diffsPrefix = [[self::INSERT, $new]];
			return true;
		}
		else if (!$new)
		{
			$diffsPrefix = [[self::DELETE, $old]];
			return true;
		}
		else if ($old == $new)
		{
			$diffsPrefix = [[self::EQUAL, $old]];
			return true;
		}

		// look for matching prefix
		$prefixMatch = 0;
		for ($i = 0; ; $i++)
		{
			if (!isset($old[$i]) || !isset($new[$i]) || $old[$i] != $new[$i])
			{
				$prefixMatch = $i;
				break;
			}
		}

		if ($prefixMatch)
		{
			$diffsPrefix = [
				[self::EQUAL, array_slice($old, 0, $prefixMatch)]
			];
			$old = array_slice($old, $prefixMatch);
			$new = array_slice($new, $prefixMatch);
		}

		// look for matching suffix
		$suffixMatch = 0;
		$oldLen = count($old);
		for ($x = $oldLen - 1, $y = count($new) - 1; isset($old[$x]) && isset($new[$y]); $x--, $y--)
		{
			if ($old[$x] != $new[$y])
			{
				$suffixMatch = $oldLen - 1 - $x;
				break;
			}
		}

		if ($suffixMatch)
		{
			$diffsSuffix = [
				[self::EQUAL, array_slice($old, -$suffixMatch)]
			];
			$old = array_slice($old, 0, -$suffixMatch);
			$new = array_slice($new, 0, -$suffixMatch);
		}

		return false;
	}

	protected function compareGreedy(array $old, array $new)
	{
		$n = count($old);
		$m = count($new);

		$max = $n + $m;
		$vOffset = 0;
		$v = [];
		$v[$vOffset + 1] = 0;
		$vSetsCompressed = [];

		for ($d = 0; $d <= $max; $d++)
		{
			for ($k = -$d; $k <= $d; $k += 2)
			{
				$kOffset = $k + $vOffset;
				$vKMinus = (isset($v[$kOffset - 1]) ? $v[$kOffset - 1] : -1);
				$vKPlus = (isset($v[$kOffset + 1]) ? $v[$kOffset + 1] : -1);

				$down = ($k == -$d || ($k != $d && $vKMinus < $vKPlus));

				if ($down)
				{
					$x = $vKPlus;
				}
				else
				{
					$x = $vKMinus + 1;
				}
				$y = $x - $k;

				while ($x < $n && $y < $m && $old[$x] === $new[$y])
				{
					$x++;
					$y++;
				}

				$v[$kOffset] = $x;
				if ($x >= $n && $y >= $m)
				{
					$vSetsCompressed[$d] = $this->compressSet($v);
					break 2;
				}
			}

			$vSetsCompressed[$d] = $this->compressSet($v);
		}

		$diffs = [];
		$x = $n;
		$y = $m;

		for ($d = count($vSetsCompressed) - 1; $d >= 0 && ($x > 0 || $y > 0); $d--)
		{
			$v = $this->decompressSet($vSetsCompressed[$d]);
			unset($vSetsCompressed[$d]);
			$k = $x - $y;
			$kOffset = $k + $vOffset;

			$xEnd = (isset($v[$kOffset]) ? $v[$kOffset] : -1);
			$yEnd = $x - $k;

			$vKMinus = (isset($v[$kOffset - 1]) ? $v[$kOffset - 1] : -1);
			$vKPlus = (isset($v[$kOffset + 1]) ? $v[$kOffset + 1] : -1);

			$down = ($k == -$d || ($k != $d && $vKMinus < $vKPlus));

			$kPrev = ($down ? $k + 1 : $k - 1);

			$xStart = (isset($v[$kPrev + $vOffset]) ? $v[$kPrev + $vOffset] : -1);
			$yStart = $xStart - $kPrev;

			$xMid = ($down ? $xStart : $xStart + 1);
			$yMid = $xMid - $k;

			$partial = $this->getDiffParts($old, $new, $xMid, $yMid, $xEnd, $yEnd);
			if ($partial)
			{
				$diffs = array_merge($diffs, $partial);
			}

			$partial = $this->getDiffParts($old, $new, $xStart, $yStart, $xMid, $yMid);
			if ($partial)
			{
				$diffs = array_merge($diffs, $partial);
			}

			$x = $xStart;
			$y = $yStart;
		}

		return array_reverse($diffs);
	}

	protected function compressSet(array $v)
	{
		return json_encode($v);
	}

	protected function decompressSet($v)
	{
		return json_decode($v, true);
	}

	protected function getDiffParts(array $old, array $new, $x1, $y1, $x2, $y2)
	{
		$x1 = max(0, $x1);
		$y1 = max(0, $y1);
		$x2 = max(0, $x2);
		$y2 = max(0, $y2);

		if ($x1 == $x2 && $y1 == $y2)
		{
			return false;
		}

		$xDiff = $x2 - $x1;
		$yDiff = $y2 - $y1;

		if ($xDiff == $yDiff)
		{
			return [
				[self::EQUAL, array_slice($old, $x1, $xDiff)]
			];
		}
		else
		{
			$diff = [];
			if ($xDiff)
			{
				$diff[] = [self::DELETE, array_slice($old, $x1, $xDiff)];
			}
			if ($yDiff)
			{
				$diff[] = [self::INSERT, array_slice($new, $y1, $yDiff)];
			}

			return $diff;
		}
	}

	protected function cleanUp(array $diffs, $type)
	{
		$keyMap = [];
		foreach ($diffs AS $key => $diff)
		{
			if ($key == 0)
			{
				$keyMap[$key] = $key;
				continue;
			}

			$newKey = $key;

			if ($diff[0] == self::EQUAL)
			{
				$previousKey = $keyMap[$key - 1];
				if ($diffs[$previousKey][0] == self::EQUAL)
				{
					$diffs[$previousKey][1] = array_merge($diffs[$previousKey][1], $diff[1]);
					unset($diffs[$key]);
					$newKey = $previousKey;
				}
			}
			else
			{
				$oppositeKeyType = ($diff[0] == self::INSERT ? self::DELETE : self::INSERT);

				$previousKey = $keyMap[$key - 1];

				if ($diffs[$previousKey][0] == $diff[0])
				{
					$diffs[$previousKey][1] = array_merge($diffs[$previousKey][1], $diff[1]);
					unset($diffs[$key]);
					$newKey = $previousKey;
				}
				else if ($diffs[$previousKey][0] == $oppositeKeyType && $key > 2)
				{
					$secondBackKey = $keyMap[$key - 2];
					if ($diffs[$secondBackKey][0] == $diff[0])
					{
						$diffs[$secondBackKey][1] = array_merge($diffs[$secondBackKey][1], $diff[1]);
						unset($diffs[$key]);
						$newKey = $secondBackKey;
					}
				}
			}

			$keyMap[$key] = $newKey;
		}

		return $diffs;
	}

	protected function split($string, $type)
	{
		if (is_array($string))
		{
			return $string;
		}

		if ($string === '')
		{
			return [];
		}

		switch ($type)
		{
			case self::DIFF_TYPE_CHAR:
				return $this->splitChars($string);

			case self::DIFF_TYPE_WORD:
				return $this->splitWords($string);

			case self::DIFF_TYPE_LINE:
			default:
				return $this->splitLines($string);

		}
	}

	protected function splitWords($string)
	{
		return preg_split('/(\s+)/', $string, 0, PREG_SPLIT_DELIM_CAPTURE);
	}

	protected function splitChars($string)
	{
		return preg_split('/(.)/', $string, 0, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
	}

	protected function splitLines($string)
	{
		return preg_split('/\r?\n/', $string);
	}
}