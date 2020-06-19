<?php

namespace XF\Search\Source;

use XF\Search\IndexRecord;
use XF\Search\Query;
use XF\Util\Arr;

abstract class AbstractSource
{
	protected $bulkIndexing = false;

	abstract public function isRelevanceSupported();

	abstract public function index(IndexRecord $record);

	abstract protected function flushBulkIndexing();

	abstract public function delete($type, $ids);

	abstract public function truncate($type = null);

	abstract public function search(Query\Query $query, $maxResults);

	abstract protected function finalizeParsedKeywords(array $parsed);

	public function enableBulkIndexing()
	{
		$this->bulkIndexing = true;
	}

	public function disableBulkIndexing()
	{
		if ($this->bulkIndexing)
		{
			$this->flushBulkIndexing();
		}

		$this->bulkIndexing = false;
	}

	public function reassignContent($oldUserId, $newUserId)
	{
		\XF::app()->jobManager()->enqueue('XF:SearchUserChange', ['user_id' => $oldUserId]);
	}

	public function getStopWords()
	{
		return [];
	}

	public function getMinWordLength()
	{
		return 1;
	}

	public function getWordSplitRange()
	{
		return '\x00-\x21\x28\x29\x2C-\x2F\x3A-\x40\x5B-\x5E\x60\x7B\x7D-\x7F';
	}

	public function parseKeywords($keywords, &$error = null, &$warning = null)
	{
		$splitRange = $this->getWordSplitRange();
		$stopWords = $this->getStopWords();
		$minWordLength = $this->getMinWordLength();

		$output = [];
		$i = 0;

		$haveWords = false;
		$invalidWords = [];

		foreach ($this->tokenizeKeywords($keywords) AS $match)
		{
			if ($match['modifier'] == '|' && $i > 0 && $output[$i - 1][0] == '')
			{
				$output[$i - 1][0] = '|';
			}
			else if ($match['modifier'] == '|' && $i == 0)
			{
				$match['modifier'] = '';
			}

			if (!empty($match['quoteTerm']))
			{
				$term = preg_replace('/[' . $splitRange . ']/', ' ', $match['quoteTerm']);
				$quoted = true;
			}
			else
			{
				$term = str_replace('"', ' ', $match['term']); // unmatched quotes
				$term = preg_replace('/^(AND|OR|NOT)$/', '', $term); // words may have special meaning
				$quoted = false;
			}

			$term = trim($term);
			$words = $this->splitWords($term);

			foreach ($words AS $word)
			{
				if ($word === '')
				{
					continue;
				}

				if (utf8_strlen($word) < $minWordLength)
				{
					$invalidWords[] = $word;
				}
				else if (in_array($word, $stopWords))
				{
					$invalidWords[] = $word;
				}
				else
				{
					$haveWords = true;
				}
			}

			$output[$i] = [$match['modifier'], ($quoted ? "\"$term\"" : $term)];
			$i++;
		}

		$error = null;
		$warning = null;

		if (!$haveWords)
		{
			if ($invalidWords)
			{
				$error = \XF::phrase('search_could_not_be_completed_because_search_keywords_were_too');
			}
		}
		else if ($invalidWords)
		{
			$warning = \XF::phrase(
				'following_words_were_not_included_in_your_search_x',
				['words' => implode(', ', $invalidWords)]
			);
		}

		return $this->finalizeParsedKeywords($output);
	}

	protected function tokenizeKeywords($keywords)
	{
		$keywords = str_replace(['(', ')'], '', trim($keywords)); // don't support grouping yet

		$splitRange = $this->getWordSplitRange();

		preg_match_all('/
			(?<=[' . $splitRange .'\-\+\|]|^)
			(?P<modifier>
				  (?<!\-|\+|\|)\-
				| (?<!\-|\+|\|)\+
				| (?<!\-|\+|\|)\|\s+
				|
			)
			(?P<term>"(?P<quoteTerm>[^"]+)"|[^' . $splitRange .'\-\+\|]+)
		/ix', $keywords, $matches, PREG_SET_ORDER);

		foreach ($matches AS &$match)
		{
			if ($match['modifier'])
			{
				$match['modifier'] = trim($match['modifier']);
				$match[1] = trim($match[1]);
			}
		}

		return $matches;
	}

	protected function splitWords($words)
	{
		return Arr::stringToArray($words, '/[' . $this->getWordSplitRange() . ']/');
	}
}