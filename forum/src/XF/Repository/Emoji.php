<?php

namespace XF\Repository;

use XF\Mvc\Entity\Repository;

class Emoji extends Repository
{
	public function getMatchingEmojiByString($string, array $options = [])
	{
		$options = array_replace([
			'includeEmoji' => true,
			'includeSmilies' => true,
			'limit' => 5
		], $options);

		if ($options['includeEmoji'] && \XF::config('fullUnicode'))
		{
			/** @var \XF\Data\Emoji $emojiData */
			$emojiData = $this->app()->data('XF:Emoji');
			$emojiList = $emojiData->getEmojiListForDisplay();
		}
		else
		{
			$emojiList = [];
		}

		$strFormatter = $this->app()->stringFormatter();
		$emoFormatter = $strFormatter->getEmojiFormatter();

		$smilies = $this->getSmilies();

		// different priority results depending on where matched
		$resultsP1 = [];
		$resultsP2 = [];
		$resultsP3 = [];
		$resultsP4 = [];

		if ($options['includeSmilies'] && $smilies)
		{
			foreach ($smilies AS $smilieId => $smilie)
			{
				$shortname = $smilie->smilie_text_options[0];

				// normalize smilie data to match emoji data
				$data = [
					'shortname' => $shortname,
					'keywords' => [strtolower($smilie['title'])]
				];

				$data['html'] = $strFormatter->replaceSmiliesHtml($data['shortname']);

				foreach ($smilie->smilie_text_options AS $text)
				{
					if (stripos($text, $string) !== false)
					{
						$shortname = $data['shortname'] = $text; // may be more relevant
						$resultsP1[$shortname] = $data;
						break;
					}
				}

				if (stripos($smilie['title'], $string) !== false)
				{
					$resultsP1[$shortname] = $data;
				}
			}
		}

		foreach ($emojiList AS $shortname => $data)
		{
			$data['html'] = $emoFormatter->getImageFromShortname($shortname);

			if (stripos($data['shortname'], $string) !== false
				|| stripos($data['name'], $string) !== false
			)
			{
				$resultsP2[$shortname] = $data;
			}
			if ($data['shortname_alternates'] !== null)
			{
				foreach ((array)$data['shortname_alternates'] AS $alt)
				{
					if (stripos($alt, $string) !== false)
					{
						$resultsP3[$shortname] = $data;
						break;
					}
				}
			}
			if ($data['keywords'] !== null)
			{
				foreach ((array)$data['keywords'] AS $keyword)
				{
					if (stripos($keyword, $string) !== false)
					{
						$resultsP4[$shortname] = $data;
						break;
					}
				}
			}
		}

		$sorter = function($a, $b) { return (strlen($a['shortname']) > strlen($b['shortname'])); };

		uasort($resultsP1, $sorter);
		uasort($resultsP2, $sorter);
		uasort($resultsP3, $sorter);
		uasort($resultsP4, $sorter);

		$results = $resultsP1 + $resultsP2 + $resultsP3 + $resultsP4;

		return array_slice($results, 0, $options['limit'], true);
	}

	public function getSmilies($displayInEditorOnly = false)
	{
		/** @var \XF\Repository\Smilie $smilieRepo */
		$smilieRepo = $this->repository('XF:Smilie');
		return $smilieRepo->findSmiliesForList($displayInEditorOnly)->fetch();
	}
}