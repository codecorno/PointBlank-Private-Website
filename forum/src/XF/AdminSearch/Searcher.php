<?php

namespace XF\AdminSearch;

use XF\App;
use XF\Mvc\Entity\AbstractCollection;

class Searcher
{
	/**
	 * @var App
	 */
	protected $app;

	/**
	 * @var AbstractHandler[]
	 */
	protected $handlers = [];

	protected $typeLimit = 10;

	public function __construct(App $app, array $handlerClasses = [], $allowExclusions = true)
	{
		$this->app = $app;

		if ($allowExclusions)
		{
			foreach ($app->options()->acpSearchExclude AS $type => $value)
			{
				if (isset($handlerClasses[$type]))
				{
					unset($handlerClasses[$type]);
				}
			}
		}

		$this->addHandlerClasses($handlerClasses);
	}

	/**
	 * @param string $text
	 *
	 * @return TypeResultSet[]
	 */
	public function search($text)
	{
		if (!$text)
		{
			return [];
		}

		$matchedPhrases = $this->searchPhrases($text);

		$displayOrder = [];

		$resultTypes = [];
		foreach ($this->handlers AS $contentType => $handler)
		{
			if (!$handler->isSearchable())
			{
				continue;
			}

			$typePhraseIds = isset($matchedPhrases[$contentType]) ? $matchedPhrases[$contentType] : [];

			$results = $handler->search($text, $this->typeLimit, $typePhraseIds);
			if ($results && count($results))
			{
				if ($results instanceof AbstractCollection)
				{
					$results = $results->toArray();
				}

				$displayOrder[$contentType] = $handler->getDisplayOrder();
				$resultTypes[$contentType] = new TypeResultSet($contentType, $handler, $results);
			}
		}

		asort($displayOrder);

		$output = [];
		foreach (array_keys($displayOrder) AS $contentType)
		{
			$output[$contentType] = $resultTypes[$contentType];
		}

		return $output;
	}

	protected function searchPhrases($text)
	{
		$phraseGroupContentMap = [];

		foreach ($this->handlers AS $contentType => $handler)
		{
			if (!$handler->isSearchable())
			{
				continue;
			}

			foreach ($handler->getRelatedPhraseGroups() AS $phraseGroup)
			{
				$phraseGroupContentMap[$phraseGroup] = $contentType;
			}
		}

		$matchedPhraseIds = [];
		if ($phraseGroupContentMap)
		{
			$phraseFinder = $this->app->finder('XF:PhraseMap');

			$groupPossibilities = [];
			foreach (array_keys($phraseGroupContentMap) AS $phraseGroup)
			{
				$groupPossibilities[] = $phraseFinder->escapeLike($phraseGroup, '?.%');
			}

			$phraseFinder
				->where('language_id', \XF::language()->getId())
				->where('title', 'like', $groupPossibilities)
				->where('Phrase.phrase_text', 'like', $phraseFinder->escapeLike($text, '%?%'));

			foreach ($phraseFinder->fetchRaw(['fetchOnly' => ['title']]) AS $match)
			{
				$title = $match['title'];
				list($group, $id) = explode('.', $title, 2);

				if (isset($phraseGroupContentMap[$group]))
				{
					$contentType = $phraseGroupContentMap[$group];
					$matchedPhraseIds[$contentType][] = $id;
				}
			}
		}

		return $matchedPhraseIds;
	}

	public function addHandler($contentType, AbstractHandler $handler)
	{
		$this->handlers[$contentType] = $handler;
	}

	public function addHandlerClass($contentType, $handlerClass)
	{
		if (!class_exists($handlerClass))
		{
			return false;
		}

		$class = \XF::extendClass($handlerClass);
		$this->handlers[$contentType] = new $class($contentType, $this->app);
		return true;
	}

	public function addHandlerClasses(array $classes)
	{
		$output = [];
		foreach ($classes AS $contentType => $class)
		{
			$output[$contentType] = $this->addHandlerClass($contentType, $class);
		}

		return $output;
	}
}