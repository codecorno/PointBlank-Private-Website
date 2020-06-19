<?php

namespace XF\BbCode\Renderer;

use XF\BbCode\Traverser;
use XF\Str\Formatter;
use XF\Template\Templater;

class EditorHtml extends Html
{
	protected $undisplayableTags = [
		'code',
		'icode',
		'html',
		'php',
		'media',
		'plain',
		'quote',
		'spoiler',
		'ispoiler',
		'user',
	];

	protected $blockTagsOpenRegex = '<p|<div|<blockquote|<ul|<ol|<table';
	protected $blockTagsCloseRegex = '</p>|</div>|</blockquote>|</ul>|</ol>|</table>';

	public function addDefaultTags()
	{
		parent::addDefaultTags();

		$this->modifyTag('b', ['replace' => ['<strong>', '</strong>']]);
		$this->addTag('i', ['replace' => ['<em>', '</em>']]);
		$this->addTag('u', ['replace' => ['<u>', '</u>']]);
		$this->addTag('s', ['replace' => ['<s>', '</s>']]);

		$this->modifyTag('url', ['trimAfter' => 0]);

		foreach ($this->undisplayableTags AS $tag)
		{
			$this->modifyTag($tag, [
				'replace' => null,
				'callback' => null,
				'trimAfter' => 0,
				'stopBreakConversion' => false
			]);
		}
	}

	public function getCustomTagConfig(array $tag)
	{
		// custom tags are displayed
		return [
			'replace' => null,
			'callback' => null
		];
	}

	public function getDefaultOptions()
	{
		$options = parent::getDefaultOptions();
		$options['noProxy'] = true;
		$options['inList'] = false;
		$options['lightbox'] = false;

		return $options;
	}

	public function filterFinalOutput($output)
	{
		$debug = false;

		$btOpen = $this->blockTagsOpenRegex;
		$btClose = $this->blockTagsCloseRegex;

		$debugNl = ($debug ? "\n" : '');

		if ($debug) { echo '<hr /><b>Original:</b><br />'. nl2br(htmlspecialchars($output)); }

		$output = preg_replace('#\s*<break-start />(?>\s*)(?!' . $btOpen . '|' . $btClose . '|$)#i', $debugNl . "<p>", $output);
		$output = preg_replace('#\s*<break-start />#i', '', $output);
		$output = preg_replace('#(' . $btClose . ')\s*<break />#i', "\\1", $output);
		$output = preg_replace('#<break />\s*(' . $btOpen . ')#i', "</p>" . ($debug ? "\n" : '') . "\\1", $output);
		$output = preg_replace('#<break />\s*#i', "</p>" . $debugNl . "<p>", $output);

		if ($debug) { echo '<hr /><b>Post-break:</b><br />'. nl2br(htmlspecialchars($output)); }

		$output = trim($output);
		if (!preg_match('#^(' . $btOpen . ')#i', $output))
		{
			$output = '<p>' . $output;
		}
		if (!preg_match('#(' . $btClose . ')$#i', $output))
		{
			$output .= '</p>';
		}

		$output = preg_replace_callback('#(<p[^>]*>)(.*)(</p>)#siU',
			[$this, 'replaceEmptyContent'], $output
		);
		$output = str_replace('<empty-content />', '', $output); // just in case

		$output = $this->fixListStyles($output);

		if ($debug) { echo '<hr /><b>Final:</b><br />'. nl2br(htmlspecialchars($output)); }

		return $output;
	}

	protected function replaceEmptyContent(array $match)
	{
		$emptyParaText = ''; // was  <br /> -- Froala seems to handle this for us and removing this fixes some minor issues

		if (strlen(trim($match[2])) == 0)
		{
			// paragraph is actually empty
			$output = $emptyParaText;
		}
		else
		{
			$test = strip_tags($match[2], '<empty-content><img><br><hr>');
			if (trim($test) == '<empty-content />')
			{
				$output = str_replace('<empty-content />', $emptyParaText, $match[2]);
			}
			else
			{
				// we had a break
				$output = str_replace('<empty-content />', '', $match[2]);
			}
		}

		return $match[1] . $output . $match[3];
	}

	protected function fixListStyles($output)
	{
		$fix = function(array $match)
		{
			$pAttrs = $match[1];
			$listTag = $match[2];
			$listAttrs = $match[3];
			$listContent = $match[4];

			if (!$listAttrs)
			{
				$listAttrs = $pAttrs;
			}
			else
			{
				// TODO: this is not actually correct as it doesn't merge style attributes, but it's likely to be uncommon
				$listAttrs = $listAttrs . ' ' . $pAttrs;
			}

			return "<{$listTag}{$listAttrs}>{$listContent}</{$listTag}>";
		};

		do
		{
			$original = $output;
			$output = preg_replace_callback(
				'#<p([^>]*)>\s*<(ul|ol)([^>]*)>(.*)</\\2>\s*</p>#siU',
				$fix,
				$output
			);
		}
		while ($original != $output);

		return $output;
	}

	public function filterString($string, array $options)
	{
		if (!empty($options['treatAsStructuredText']))
		{
			$string = $this->formatter->convertStructuredTextLinkToBbCode($string);
			$string = $this->formatter->convertStructuredTextMentionsToBbCode($string);
		}

		if (empty($options['stopSmilies']))
		{
			$string = $this->formatter->replaceSmiliesHtml($string);
		}
		else
		{
			$string = htmlspecialchars($string);
		}

		$string = str_replace("\t", '    ', $string);

		// doing this twice handles situations with 3 spaces
		$string = str_replace('  ', '&nbsp; ', $string);
		$string = str_replace('  ', '&nbsp; ', $string);

		if (empty($options['stopLineBreakConversion']))
		{
			if (!empty($options['inList']) || !empty($options['inTable']))
			{
				$string = nl2br($string);
			}
			else
			{
				$string = preg_replace('/\r\n|\n|\r/', "<break />\n", $string);
			}
		}

		$string = $this->formatter->getEmojiFormatter()->formatEmojiToImage($string);

		return $string;
	}

	public function renderTagUrl(array $children, $option, array $tag, array $options)
	{
		$options['shortenUrl'] = false;

		if (is_array($option)
			&& !empty($option['unfurl'])
		)
		{
			// we're inside a URL tag already
			// don't treat what's inside it was structured text
			// otherwise unfurled URLs will be wrapped in a further URL
			$options['treatAsStructuredText'] = false;
			return $this->renderUnparsedTag($tag, $options);
		}

		return parent::renderTagUrl($children, $option, $tag, $options);
	}

	protected function getRenderedLink($text, $url, array $options)
	{
		return $this->wrapHtml(
			'<a href="' . htmlspecialchars($url) . '">',
			$text,
			'</a>'
		);
	}

	public function renderTagAttach(array $children, $option, array $tag, array $options)
	{
		$id = intval($this->renderSubTreePlain($children));
		if (!$id)
		{
			return '';
		}

		// TODO: get the extra attributes for this - align, width etc.
		$full = $this->isFullAttachView($option);
		$bbCode = '[ATTACH' . ($full ? '=full' : '') . ']' . $id . '[/ATTACH]';

		if (empty($options['attachments'][$id]))
		{
			return $bbCode;
		}

		/** @var \XF\Entity\Attachment $attachment */
		$attachment = $options['attachments'][$id];

		$isVideo = $attachment->isVideo();

		if (!$attachment->has_thumbnail && !$isVideo)
		{
			return $bbCode;
		}

		$type = ($full || $isVideo) ? 'full' : 'thumb';

		if ($type == 'full')
		{
			$router = \XF::app()->router('public');
			$url = $router->buildLink('public:attachments', $attachment, ['hash' => $attachment->temp_hash]);
		}
		else
		{
			$url = $attachment->thumbnail_url;
		}
		$url = htmlspecialchars($url);

		$alignClass = '';
		if ($align = $this->hasAlignOption($option))
		{
			if ($align == 'R')
			{
				$alignClass = $isVideo ? 'fr-fvr' : 'fr-fir';
			}
			else if ($align == 'L')
			{
				$alignClass = $isVideo ? 'fr-fvl' : 'fr-fil';
			}
		}

		$styleAttr = $this->getAttachStyleAttr($option);

		if ($isVideo)
		{
			$video = "<span class=\"fr-video fr-dvi fr-draggable fr-deletable {$alignClass}\" contenteditable=\"false\" draggable=\"true\">";
			$video .= "<video data-xf-init=\"video-init\" data-attachment=\"{$type}:{$id}\" src=\"{$url}\" controls class=\"fr-draggable\"></video>";
			$video .= '</span>';

			return $video;
		}
		else
		{
			$alt = htmlspecialchars($this->getImageAltText($option) ?: ($attachment ? $attachment->filename : ''));
			return "<img src=\"{$url}\" data-attachment=\"{$type}:{$id}\" alt=\"{$alt}\" class=\"{$alignClass}\" style=\"{$styleAttr}\" />";
		}
	}

	public function renderTagAlign(array $children, $option, array $tag, array $options)
	{
		$output = $this->renderSubTree($children, $options);

		switch (strtolower($tag['tag']))
		{
			case 'left':
			case 'center':
			case 'right':
				$wrapped = $this->wrapHtml('<p style="text-align: ' . $tag['tag'] . '">', $output, '</p>');
				return "{$wrapped}<break-start />\n";

			default:
				return $this->wrapHtml('<p>', $output, '</p>') . "<break-start />\n";
		}
	}

	public function renderTagList(array $children, $option, array $tag, array $options)
	{
		$wasInList = !empty($options['inList']);
		$options['inList'] = true;

		$output = parent::renderTagList($children, $option, $tag, $options);
		$output = preg_replace('#\s*<break-start />\s*#i', "\n", $output);
		if (!$wasInList)
		{
			$output = "<break-start />\n$output<break-start />\n";
		}

		return $output;
	}

	public function renderTagIndent(array $children, $option, array $tag, array $options)
	{
		$output = $this->renderSubTree($children, $options);

		$amount = $option ? intval($option) : 1;
		$amount = max(1, min($amount, 5));

		$side = \XF::language()->isRtl() ? 'right' : 'left';
		$css = 'margin-' . $side . ': ' . ($amount * 20) . 'px';

		$wrapped = $this->wrapHtml(
			'<p style="' . $css . '">',
			$output,
			'</p>'
		);
		return "{$wrapped}<break-start />\n";
	}

	protected function getAttachAlignClass($option, $prefix = '')
	{
		if (is_array($option) && isset($option['align']))
		{
			switch (strtoupper($option['align'][0]))
			{
				case 'L':
					return 'fr-fil';

				case 'R':
					return 'fr-fir';
			}
		}

		return null;
	}

	public function renderTagTable(array $children, $option, array $tag, array $options)
	{
		$wasInTable = !empty($options['inTable']);
		$options['inTable'] = true;

		$output = parent::renderTagTable($children, $option, $tag, $options);
		$output = preg_replace('#\s*<break-start />\s*#i', "\n", $output);
		if (!$wasInTable)
		{
			$output = "<break-start />\n$output\n";
		}

		return $output;
	}

	protected function renderFinalTableHtml($tableHtml, $tagOption, $extraContent)
	{
		$output = "<table style='width: 100%'>$tableHtml</table>";

		if (strlen($extraContent))
		{
			$output .= "<p>$extraContent</p>";
		}

		return $output;
	}

	public function wrapHtml($open, $inner, $close, $option = null)
	{
		if ($option !== null)
		{
			$open = sprintf($open, $option);
			$close = sprintf($close, $option);
		}

		$btOpen = $this->blockTagsOpenRegex;
		$btClose = $this->blockTagsCloseRegex;

		$inner = preg_replace('#(<break />\s*)(?=<break />|$)#i', '\\1<empty-content />', $inner);

		if (preg_match('#^(' . $btOpen . ')#i', $open))
		{
			$inner = preg_replace(
				'#<break-start />(?>\s*)(?!' . $btOpen . '|' . $btClose . '|$)#i',
				"$close\\0$open",
				$inner
			);
			$inner = preg_replace(
				'#<break />(?>\s*)(?!' . $btOpen . ')#i',
				"$close\\0$open",
				$inner
			);
		}
		else
		{
			if (preg_match('#^<break />#i', $inner))
			{
				$inner = '<empty-content />' . $inner;
			}
			$inner = preg_replace('#<break />\s*((' . $btOpen . ')[^>]*>)?#i', "$close\\0$open", $inner);
		}

		return $open . $inner . $close;
	}
}