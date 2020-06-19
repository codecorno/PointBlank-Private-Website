<?php

namespace XF\Mail;

use XF\CssRenderer;

class Styler
{
	/**
	 * @var CssRenderer
	 */
	protected $renderer;

	/**
	 * @var \Pelago\Emogrifier
	 */
	protected $inliner;

	protected $cssCache = [];

	public function __construct(CssRenderer $renderer, \Pelago\Emogrifier $inliner)
	{
		$this->renderer = $renderer;
		$this->inliner = $inliner;
	}

	public function styleHtml($html, $includeDefaultCss = true, \XF\Language $language = null)
	{
		if ($html)
		{
			if ($includeDefaultCss)
			{
				$this->inliner->setCss($this->getEmailCss($language));
			}
			$this->inliner->setHtml($html);
			$html = $this->inliner->emogrify();
		}

		return trim($html);
	}

	protected function getEmailCss(\XF\Language $language = null)
	{
		$templater = $this->renderer->getTemplater();

		if ($language)
		{
			$restoreLanguage = $templater->getLanguage();
			$templater->setLanguage($language);
		}
		else
		{
			$restoreLanguage = null;
		}

		$languageId = $this->renderer->getLanguageId();
		if (!isset($this->cssCache[$languageId]))
		{
			$this->cssCache[$languageId] = $this->renderCoreCss();
		}

		if ($restoreLanguage)
		{
			$templater->setLanguage($restoreLanguage);
		}

		return $this->cssCache[$languageId];
	}

	protected function renderCoreCss()
	{
		return $this->renderer->render('email:core.less', false);
	}

	public function generateTextBody($html)
	{
		if (preg_match('#<body[^>]*>(.*)</body>#siU', $html, $match))
		{
			$html = trim($match[1]);
		}

		$text = $html;
		$text = preg_replace('#\s*<style[^>]*>.*</style>\s*#siU', '', $text);
		$text = preg_replace('#\s*<script[^>]*>.*</script>\s*#siU', '', $text);
		$text = preg_replace('#<img[^>]*alt="([^"]+)"[^>]*>#siU', '$1', $text);
		$text = preg_replace_callback(
			'#<span class="inlineSpoiler"[^>]*>(.*)</span>#siU',
			function (array $matches)
			{
				$spoilerText = $matches[1];

				return \XF::phrase('(spoiler)') . ' ' . str_repeat('*', utf8_strlen($spoilerText));
			},
			$text
		);
		$text = preg_replace_callback(
			'#<a[^>]+href="([^"]+)"[^>]*>(.*)</a>#siU',
			function (array $matches)
			{
				$href = $matches[1];
				$text = $matches[2];

				if (substr($href, 0, 7) == 'mailto:')
				{
					$href = substr($href, 7);
				}

				if ($href == $text)
				{
					return $text;
				}
				else
				{
					return "$text ($href)";
				}
			},
			$text
		);
		$text = preg_replace('#<(h[12])[^>]*>(.*)</\\1>#siU', '****** $2 ******', $text);
		$text = preg_replace('#<(h[34])[^>]*>(.*)</\\1>#siU', '**** $2 ****', $text);
		$text = preg_replace('#<(h[56])[^>]*>(.*)</\\1>#siU', '** $2 **', $text);
		$text = preg_replace('#<hr[^>]*>(</hr>)?#i', '---------------', $text);

		$text = preg_replace('#\s*</td>\s*<td[^>]*>\s*#', ' - ', $text);
		$text = preg_replace('#\s*</tr>\s*<tr[^>]*>\s*#', "\n", $text);

		$text = strip_tags($text);
		$text = htmlspecialchars_decode($text, ENT_QUOTES);

		$text = preg_replace('#\n\t+#', "\n", $text);
		$text = preg_replace('#(\r?\n){3,}#', "\n\n", $text);

		return trim($text);
	}
}