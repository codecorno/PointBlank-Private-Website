<?php

namespace XF\Service\Phrase;

use XF\Entity\Phrase;

class Compile extends \XF\Service\AbstractService
{
	public function recompile(Phrase $phrase)
	{
		$languageIds = $phrase->getApplicableLanguageIds();
		$title = $phrase->title;
		$text = $phrase->phrase_text;

		$output = [];
		foreach ($languageIds AS $languageId)
		{
			$language = $this->app->language($languageId);
			if ($phrase->global_cache || $phrase->getPhraseGroup())
			{
				// We must immediately cache the phrase if it's in a group. Not doing this can lead to old phrases
				// being seen as phrase groups are recompiled with a delay to prevent writing the cache file multiple
				// times. However, editing a phrase will immediately recompile any templates that use it so they
				// could be written before the cache is updated. If we keep the new version, we prevent that issue.
				$language->cachePhrase($phrase->title, $phrase->phrase_text);
			}
			else
			{
				// Ensure any failed attempts to get this phrase will work later. We could of course just cache the
				// phrase, but this could lead to all phrases being cached for all languages, which isn't ideal.
				$language->uncachePhrase($phrase->title);
			}

			$output[] = [
				'language_id' => $languageId,
				'title' => $title,
				'phrase_text' => $text
			];
		}

		if ($output)
		{
			$this->db()->insertBulk('xf_phrase_compiled', $output, false, 'phrase_text = VALUES(phrase_text)');
			$this->finalize($phrase);
		}
	}

	public function recompileByTitle($title)
	{
		$phrases = $this->app->em()->getFinder('XF:Phrase')->where('title', $title)->fetch();
		foreach ($phrases AS $phrase)
		{
			$this->recompile($phrase);
		}
	}

	public function recompileIncludeContent($title)
	{
		$templateIds = $this->db()->fetchAllColumn("
			SELECT template_id
			FROM xf_template_phrase
			WHERE phrase_title = ?
		", $title);
		if ($templateIds)
		{
			/** @var \XF\Service\Template\Compile $compileService */
			$compileService = $this->service('XF:Template\Compile');
			$templates = $this->app->em()->findByIds('XF:Template', $templateIds);
			foreach ($templates AS $template)
			{
				$compileService->recompile($template);
			}
		}
	}

	public function deleteCompiled(Phrase $phrase, $newValue = true)
	{
		$title = $newValue ? $phrase->getValue('title') : $phrase->getExistingValue('title');
		$languageIds = $phrase->getApplicableLanguageIds();

		if ($languageIds)
		{
			$db = $this->db();
			$db->delete('xf_phrase_compiled', 'title = ? AND language_id IN (' . $db->quote($languageIds) . ')', $title);
		}

		$this->finalize($phrase);
	}

	protected function finalize(Phrase $phrase)
	{
	}
}