<?php

namespace XF\Service\Template;

use XF\Entity\Template;

class Compile extends \XF\Service\AbstractService
{
	public function recompile(Template $template)
	{
		$compiler = $this->app->templateCompiler();
		$languages = $this->getCompilableLanguages($template);
		$styleIds = $this->getApplicableStyleIds($template);
		$phrases = $template->phrasesUsed;

		/** @var \XF\Template\Compiler\Ast $ast */
		$ast = $template->template_parsed;

		foreach ($languages AS $language)
		{
			$language->requirePhrases($phrases);
			$languageId = $language->getId();

			$compiler->reset();

			try
			{
				$compiled = $compiler->compileAst($ast, $language);
			}
			catch (\XF\Template\Compiler\Exception $e)
			{
				\XF::logException(
					$e,
					false,
					"Failed to recompile template {$template->type}:{$template->title} (lang $languageId): "
				);
				continue;
			}

			foreach ($styleIds AS $styleId)
			{
				$path = $template->getAbstractedCompiledTemplatePath($languageId, $styleId);
				$this->writeCompiled($template, $path, $compiled);
			}
		}

		$this->finalize($template);
	}

	public function recompileByTitle($type, $title)
	{
		$templates = $this->app->em()->getFinder('XF:Template')
			->where('type', $type)
			->where('title', $title)
			->fetch();
		foreach ($templates AS $template)
		{
			$this->recompile($template);
		}
	}

	public function deleteCompiled(Template $template, $newValue = true)
	{
		$languages = $this->getCompilableLanguages($template);
		$styleIds = $this->getApplicableStyleIds($template);
		$languageIds = array_keys($languages);

		foreach ($languages AS $language)
		{
			foreach ($styleIds AS $styleId)
			{
				$path = $template->getAbstractedCompiledTemplatePath($language->getId(), $styleId, $newValue);
				\XF\Util\File::deleteFromAbstractedPath($path);
			}
		}

		if ($styleIds && $languageIds && preg_match('/\.(css|less)$/i', $template->title))
		{
			$db = $this->db();
			$condition = 'title = ?'
				. ' AND style_id IN (' . $db->quote($styleIds) . ')'
				. ' AND language_id IN (' . $db->quote($languageIds) . ')';
			$db->delete('xf_css_cache', $condition, $template->type . ':' . $template->title);
		}

		$this->finalize($template);
	}

	public function updatePhrasesUsed(Template $template)
	{
		$id = $template->template_id;
		$db = $this->db();

		/** @var \XF\Template\Compiler\Ast $ast */
		$ast = $template->template_parsed;
		$phrases = $ast->analyzePhrases();

		$db->delete('xf_template_phrase', 'template_id = ?', $id);

		if ($phrases)
		{
			$sql = [];
			foreach ($phrases AS $phrase)
			{
				$sql[] = ['template_id' => $id, 'phrase_title' => $phrase];
			}
			$db->insertBulk('xf_template_phrase', $sql);
		}

		return $phrases;
	}

	protected function getApplicableStyleIds(Template $template)
	{
		if ($template->type == 'admin')
		{
			// only compile admin templates for style 0 (right now)
			return [0];
		}

		return $this->db()->fetchAllColumn("
			SELECT style_id
			FROM xf_template_map
			WHERE template_id = ?
		", $template->template_id);
	}

	/**
	 * @param Template $template
	 *
	 * @return \XF\Language[]
	 */
	protected function getCompilableLanguages(Template $template)
	{
		$languages = $this->app['language.all'];
		$languages[0] = $this->app['language.fallback'];

		return $languages;
	}

	protected function writeCompiled(Template $template, $abstractedPath, $compiled)
	{
		$hash = $template->getHash();
		$contents = "<?php\n// FROM HASH: $hash\n$compiled";

		\XF\Util\File::writeToAbstractedPath($abstractedPath, $contents);
	}

	protected function finalize(Template $template)
	{
		/** @var \XF\Repository\Style $repo */
		$repo = $this->app->repository('XF:Style');
		$repo->updateAllStylesLastModifiedDateLater();
	}
}