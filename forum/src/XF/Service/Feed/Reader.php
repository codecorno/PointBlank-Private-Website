<?php

namespace XF\Service\Feed;

class Reader extends \XF\Service\AbstractService
{
	protected $url;

	/** @var \GuzzleHttp\Client */
	protected $client;

	/** @var \Zend\Feed\Reader\Feed\Rss */
	protected $feed;

	protected $feedData;

	/** @var \Exception */
	protected $exception;

	public function __construct(\XF\App $app, $url)
	{
		parent::__construct($app);
		$this->setUrl($url);
		$this->setClient();
		$this->setFeed();
	}

	public function setUrl($url)
	{
		/** @var \XF\Validator\Url $validator */
		$validator = $this->app->validator('Url');
		$validator->coerceValue($url);
		if (!$validator->isValid($url))
		{
			throw new \XF\PrintableException(\XF::phrase('please_enter_valid_url'));
		}
		$this->url = $url;
	}

	protected function setClient()
	{
		$this->client = $this->app->http()->client();
	}

	protected function setFeed()
	{
		try
		{
			$content = $this->client->get($this->url)
				->getBody()
				->getContents();

			$this->feed = \Zend\Feed\Reader\Reader::importString($content);
		}
		catch (\Exception $e) { $this->exception = $e; }
	}

	public function getFeed()
	{
		return $this->feed;
	}

	public function getTitle()
	{
		$feed = $this->feed;
		if ($feed)
		{
			return $feed->getTitle();
		}

		return '';
	}

	public function getFeedData($withEntries = true)
	{
		$feed = $this->feed;

		if (empty($feed))
		{
			return [];
		}

		$this->feedData = [
			'id' => $feed->getId(),
			'title' => $feed->getTitle(),
			'link' => $feed->getLink(),
			'date_modified' => $feed->getDateModified(),
			'description' => $feed->getDescription(),
			'language' => $feed->getLanguage(),
			'image' => $feed->getImage(),
			'generator' => $feed->getGenerator(),
			'baseUrl' => dirname($this->url),
			'entries' => []
		];

		if ($withEntries)
		{
			$this->feedData['entries'] = $this->getFeedEntries();
		}

		$this->feedData = \XF::cleanArrayStrings($this->feedData);

		return $this->feedData;
	}

	public function getFeedEntries()
	{
		$entries = [];

		$feed = $this->feed;
		foreach ($feed AS $entry)
		{
			try
			{
				$content = $entry->getContent();
			}
			catch (\Exception $e)
			{
				// there's a situation where getting the content can trigger an exception if malformed,
				// so ensure this doesn't error
				$content = $entry->getDescription();
			}

			// if wrong date format is used or date is missing from the entry then the feed
			// is invalid but try to workaround it by getting the feed date or current time
			$dateModified = $entry->getDateModified() ?: $feed->getDateModified();
			if ($dateModified)
			{
				$dateModified = $dateModified->getTimestamp();
			}
			else
			{
				$dateModified = time();
			}

			$entryData = [
				'id' => $entry->getId(),
				'title' => html_entity_decode($entry->getTitle(), ENT_COMPAT, 'utf-8'),
				'description' => html_entity_decode($entry->getDescription(), ENT_COMPAT, 'utf-8'),
				'date_modified' => $dateModified,
				'author' => $this->getAuthorsAsString($entry->getAuthors()),
				'link' => $entry->getLink(),
				'content' => $this->getContent($content)
			];

			$enclosure = $entry->getEnclosure();
			if ($enclosure)
			{
				$entryData['enclosure_url'] = $enclosure->url;
				$entryData['enclosure_length'] = $enclosure->length;
				$entryData['enclosure_type'] = $enclosure->type;
			}

			if (utf8_strlen($entryData['id']) > 250)
			{
				$entryData['id'] = md5($entryData['id']);
			}

			$entries[] = $entryData;
		}

		return $entries;
	}

	public function getException()
	{
		return $this->exception;
	}

	protected function getAuthorsAsString($feedAuthors)
	{
		$authorNames = [];

		if ($feedAuthors)
		{
			foreach ($feedAuthors AS $author)
			{
				if (isset($author['name']))
				{
					$authorNames[] = $author['name'];
				}
				else if (isset($author['email']))
				{
					$authorNames[] = $author['email'];
				}
			}
		}

		$authorNames = array_unique($authorNames);

		return implode(', ', $authorNames);
	}

	protected function getContent($html)
	{
		$html = preg_replace('#<p#i', '<br>$0', $html);
		$html = preg_replace('#</p>(?!\s*<br)#i', '$0<br>', $html);

		return \XF\Html\Renderer\BbCode::renderFromHtml($html, [
			'baseUrl' => $this->feedData['baseUrl']
		]);
	}
}