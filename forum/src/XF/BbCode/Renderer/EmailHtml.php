<?php

namespace XF\BbCode\Renderer;

class EmailHtml extends Html
{
	public function getDefaultOptions()
	{
		$options = parent::getDefaultOptions();
		$options['stopSmilies'] = true;
		$options['noProxy'] = true;
		$options['lightbox'] = false;

		return $options;
	}

	public function getCustomTagConfig(array $tag)
	{
		$output = parent::getCustomTagConfig($tag);

		if ($tag['bb_code_mode'] == 'replace')
		{
			$output['replace'] = $tag['replace_html_email'];
		}

		return $output;
	}

	public function renderTagInlineCode(array $children, $option, array $tag, array $options)
	{
		$content = $this->renderSubTree($children, $options);
		return $this->wrapHtml('<code>', $content, '</code>');
	}

	protected function getRenderedCode($content, $language, array $config = [])
	{
		return $this->templater->renderTemplate('email:bb_code_tag_code', [
			'content' => new \XF\PreEscaped($content),
			'language' => $language
		]);
	}

	protected function getRenderedQuote($content, $name, array $source, array $attributes)
	{
		return $this->templater->renderTemplate('email:bb_code_tag_quote', [
			'content' => new \XF\PreEscaped($content),
			'name' => $name ? new \XF\PreEscaped($name) : null,
			'source' => $source,
			'attributes' => $attributes
		]);
	}

	protected function getRenderedSpoiler($content, $title = null)
	{
		return $this->templater->renderTemplate('email:bb_code_tag_spoiler', [
			'content' => new \XF\PreEscaped($content),
			'title' => $title ? new \XF\PreEscaped($title) : null
		]);
	}

	protected function getRenderedInlineSpoiler($content)
	{
		return $this->templater->renderTemplate('email:bb_code_tag_ispoiler', [
			'content' => new \XF\PreEscaped($content)
		]);
	}

	public function renderTagMedia(array $children, $option, array $tag, array $options)
	{
		if (isset($this->mediaSites[strtolower($option)]))
		{
			return $this->templater->renderTemplate('email:bb_code_tag_media');
		}
		else
		{
			return '';
		}
	}

	protected function getRenderedUnfurl($url, array $options)
	{
		$text = $this->prepareTextFromUrl($url);
		return $this->getRenderedLink($text, $url, $options);
	}

	public static function factory(\XF\App $app)
	{
		$renderer = parent::factory($app);
		$renderer->setTemplater($app['mailer.templater']);

		return $renderer;
	}
}