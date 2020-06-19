<?php

namespace XF\Service\Phrase;

use XF\Entity\Phrase;

class Group extends \XF\Service\AbstractService
{
	public function getAllPhraseGroups()
	{
		return $this->db()->fetchAllColumn("
			SELECT DISTINCT phrase_group
			FROM xf_phrase_map
			WHERE phrase_group IS NOT NULL
		");
	}

	public function compileAllPhraseGroups()
	{
		foreach ($this->getAllPhraseGroups() AS $group)
		{
			$this->compilePhraseGroup($group);
		}
	}

	public function compilePhraseGroup($group)
	{
		$languageIds = array_keys($this->app['language.all']);
		$languageIds[] = 0;

		$base = 'code-cache://phrase_groups';

		foreach ($languageIds AS $languageId)
		{
			$phrases = $this->db()->fetchPairs("
				SELECT map.title, phrase.phrase_text
				FROM xf_phrase_map AS map
				INNER JOIN xf_phrase AS phrase ON (map.phrase_id = phrase.phrase_id)
				WHERE map.language_id = ?
					AND map.phrase_group = ?
			", [$languageId, $group]);

			$path = "$base/l$languageId/$group.php";

			if ($phrases)
			{
				ksort($phrases);
				$output = "<?php\nreturn " . var_export($phrases, true) . ';';
				\XF\Util\File::writeToAbstractedPath($path, $output);
			}
			else
			{
				\XF\Util\File::deleteFromAbstractedPath($path);
			}
		}
	}
}