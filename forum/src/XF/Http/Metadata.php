<?php

namespace XF\Http;

use Symfony\Component\DomCrawler\Crawler;
use XF\App;

class Metadata
{
	/**
	 * @var App
	 */
	protected $app;

	/**
	 * @var string
	 */
	protected $body;

	/**
	 * @var string[][]
	 */
	protected $headers;

	/**
	 * @var string|null
	 */
	protected $requestedFrom;

	public function __construct(App $app, $body, array $headers, $requestedFrom = null)
	{
		$this->app = $app;
		$this->headers = $headers;
		$this->body = $body;
		$this->body = $this->convertBodyCharset();
		$this->requestedFrom = $requestedFrom;
	}

	public function getBody()
	{
		return $this->body;
	}

	public function getHeaders()
	{
		return $this->headers;
	}

	public function getContentType()
	{
		return isset($this->headers['content-type']) ? $this->headers['content-type'] : [];
	}

	public function getCharset()
	{
		$contentType = $this->getContentType();
		$charset = $this->app->http()->reader()->getCharset($contentType);

		if (!$charset)
		{
			$charset = $this->getXPathMetaByAttr('@charset', 'charset');

			if (!$charset)
			{
				$contentTypeTag = $this->getXPathMetaByAttr('@http-equiv="Content-Type"');

				if (!$contentTypeTag)
				{
					$contentTypeTag = $this->getXPathMetaByAttr('@http-equiv="content-type"');
				}

				if ($contentTypeTag)
				{
					preg_match('/charset=([^;"\\s]+|"[^;"]+")/i', $contentTypeTag, $contentTypeMatch);

					if (isset($contentTypeMatch[1]))
					{
						$charset = trim($contentTypeMatch[1], " \t\n\r\0\x0B\"");
					}
				}
			}

			if (!$charset)
			{
				if (substr($this->body, 0, 3) == "\xEF\xBB\xBF")
				{
					$charset = 'utf-8';
				}
				else
				{
					$charset = 'windows-1252';
				}
			}
		}

		return $charset;
	}

	public function getTitle()
	{
		$title = $this->getXPathMetaByAttr(['@property="og:title"', '@property="twitter:title"']);

		if (!$title)
		{
			$title = $this->getXPathMetaByAttr(['@name="title"']);
		}

		if (!$title && preg_match('#<title[^>]*>(.*)</title>#siU', $this->body, $match))
		{
			$title = $match[1];
		}

		return $this->cleanMetadataString($title);
	}

	public function getDescription()
	{
		$description = $this->getXPathMetaByAttr(['@property="og:description"', '@property="twitter:description"']);

		if (!$description)
		{
			$description = $this->getXPathMetaByAttr(['@name="description"']);
		}

		return $this->cleanMetadataString($description);
	}

	public function getImage()
	{
		$image = $this->getXPathMetaByAttr(['@property="og:image"', '@property="twitter:image"']);
		return $this->cleanMetadataString($image, true);
	}

	public function getFavicon()
	{
		$crawler = new Crawler($this->body);

		$xPath = '//*/link[contains(@rel, \'apple-touch-icon\') or contains(@rel, \'icon\') or contains(@rel, \'shortcut\')]';

		$content = $crawler
			->filterXPath($xPath)
			->extract(['href']);

		if (!count($content))
		{
			return '';
		}

		$return = '';

		/** @var \XF\Validator\Url $urlValidator */
		$urlValidator = $this->app->validator('Url');

		foreach ($content AS $value)
		{
			$value = $urlValidator->coerceValue($value);

			// if we don't have something which looks like an absolute URL attempt to convert it to one
			if (parse_url($value, PHP_URL_SCHEME) == '')
			{
				$value = \XF::convertToAbsoluteUrl($value, $this->requestedFrom);
			}

			if ($urlValidator->isValid($value))
			{
				$return = $value;
				break;
			}
		}

		return $this->cleanMetadataString($return, true);
	}

	public function convertBodyCharset()
	{
		if (!$this->body)
		{
			return '';
		}

		$charset = $this->getCharset();

		$body = $this->body;
		$newBody = false;
		if (function_exists('iconv'))
		{
			$newBody = @iconv($charset, 'utf-8//IGNORE', $body);
		}
		if (!$newBody && function_exists('mb_convert_encoding'))
		{
			$newBody = @mb_convert_encoding($body, 'utf-8', $charset);
		}
		$body = ($newBody ? $newBody : preg_replace('/[\x80-\xff]/', '', $body));

		$body = preg_replace_callback(
			'#<meta[^>]+>#siU',
			function($match)
			{
				return preg_replace_callback(
					'/charset=([^;"\\s]+|"[^;"]+")/i',
					function ($charsetMatch)
					{
						if (strpos($charsetMatch[0], '"') !== false)
						{
							return 'charset="utf-8"';
						}
						else
						{
							return 'charset=utf-8';
						}
					},
					$match[0]
				);
			},
			$body
		);

		return $body;
	}

	public function cleanMetadataString($string, $isUrl = false)
	{
		if (!$string)
		{
			return '';
		}

		$string = \XF::cleanString($string);
		$string = utf8_unhtml($string, true);
		$string = html_entity_decode($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
		$string = utf8_unhtml($string);
		$string = str_replace("\n", ' ', trim($string));
		$string = \XF::cleanString($string);

		if ($isUrl)
		{
			/** @var \XF\Validator\Url $validator */
			$validator = $this->app->validator('Url');

			$string = $validator->coerceValue($string);

			if (!$validator->isValid($string))
			{
				$string = '';
			}
		}

		return $string;
	}

	protected function getXPathMetaByAttr($properties, $returnAttr = 'content')
	{
		$body = $this->body;

		if (!is_array($properties))
		{
			$properties = [$properties];
		}

		$xPath = '//*/meta[' . implode(' or ', $properties) . ']';

		$crawler = new Crawler($body);

		$content = $crawler
			->filterXPath($xPath)
			->extract([$returnAttr]);

		if (!count($content))
		{
			return '';
		}

		$return = '';

		foreach ($content AS $value)
		{
			// "one page apps" may just contain placeholders
			if (preg_match('/^{{.*}}$/', $value))
			{
				continue;
			}

			if (strlen($value))
			{
				$return = $value;
				break;
			}
		}

		return $return;
	}
}