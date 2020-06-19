<?php

namespace XF\BbCode\Renderer;

use XF\BbCode\Traverser;
use XF\Str\Formatter;
use XF\Template\Templater;

class SimpleHtml extends Html
{
	public function getDefaultOptions()
	{
		$options = parent::getDefaultOptions();
		$options['lightbox'] = false;

		return $options;
	}

	public function filterFinalOutput($output)
	{
		return trim($output);
	}

	public function renderTagAttach(array $children, $option, array $tag, array $options)
	{
		$id = intval($this->renderSubTreePlain($children));
		if (!$id)
		{
			return '';
		}

		$link = \XF::app()->router('public')->buildLink('full:attachments', ['attachment_id' => $id]);
		$phrase = \XF::phrase('view_attachment_x', ['name' => $id]);

		return '<a href="' . htmlspecialchars($link) . '">' . $phrase . '</a>';
	}

	public function renderTagCode(array $children, $option, array $tag, array $options)
	{
		$content = $this->renderSubTree($children, $options);
		// a bit like ltrim, but only remove blank lines, not leading tabs on the first line
		$content = preg_replace('#^([ \t]*\r?\n)+#', '', $content);
		$content = rtrim($content);

		return $this->wrapHtml('<pre style="margin: 1em 0">', $content, '</pre>');
	}

	public function renderTagMedia(array $children, $option, array $tag, array $options)
	{
		$mediaKey = trim($this->renderSubTreePlain($children));
		if (preg_match('#[&?"\'<>\r\n]#', $mediaKey) || strpos($mediaKey, '..') !== false)
		{
			return '';
		}

		$censored = $this->formatter->censorText($mediaKey);
		if ($censored != $mediaKey)
		{
			return '';
		}

		return $this->wrapHtml('<div>', \XF::phrase('media'), '</div>');
	}

	public function renderTagQuote(array $children, $option, array $tag, array $options)
	{
		if (!$children)
		{
			return '';
		}

		$this->trimChildrenList($children);

		$content = $this->renderSubTree($children, $options);
		if ($content === '')
		{
			return '';
		}

		return $this->wrapHtml('<blockquote>', $content, '</blockquote>');
	}

	public function renderTagSpoiler(array $children, $option, array $tag, array $options)
	{
		if (!$children)
		{
			return '';
		}

		$this->trimChildrenList($children);

		$content = $this->renderSubTree($children, $options);
		if ($content === '')
		{
			return '';
		}

		return $this->wrapHtml('<div>', $content, '</div>');
	}

	public function renderTagUrl(array $children, $option, array $tag, array $options)
	{
		$options['shortenUrl'] = false;

		return parent::renderTagUrl($children, $option, $tag, $options);
	}

	public function countLines($string, \XF\BbCode\Parser $parser, \XF\BbCode\RuleSet $rules, array $options = [])
	{
		$html = $this->render($string, $parser, $rules, $options);
		return $this->countLinesInHtml($html);
	}

	public function countLinesInHtml($html)
	{
		$processed = preg_replace('#<br />\s*<(div|p|pre|ul|ol|blockquote)#i', '<$1', $html);
		$processed = preg_replace('#(<(ul|ol)[^>]*>)\s*<li[^>]*>#i', '$1', $processed);
		$processed = preg_replace('#</li>\s*(</(ul|ol)>)#i', '$1', $processed);
		$processed = preg_replace('#</?(div|p|pre|ul|ol|li|blockquote)[^>]*>\s*<(div|p|pre|ul|ol|li|blockquote)[^>]*>#i', '<br />', $processed);
		$processed = preg_replace('#</?(div|p|pre|ul|ol|li|blockquote)[^>]*>#i', '<br />', $processed);
		$processed = preg_replace('#^(<br[^>]*>)+#i', '', $processed);
		$processed = preg_replace('#(<br[^>]*>)+$#i', '', $processed);

		return substr_count($processed, '<br') + 1;
	}
}