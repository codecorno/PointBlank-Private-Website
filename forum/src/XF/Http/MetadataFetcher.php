<?php

namespace XF\Http;

use XF\App;

class MetadataFetcher
{
	/**
	 * @var App
	 */
	protected $app;

	/**
	 * @var Reader
	 */
	protected $reader;

	protected $limits = [
		'time' => 5,
		'bytes' => 1.5 * 1024 * 1024 // max size of the document we'll try to download
	];

	public function __construct(App $app, Reader $reader)
	{
		$this->app = $app;
		$this->reader = $reader;
	}

	public function setLimits(array $limits)
	{
		$this->limits = array_replace($this->limits, $limits);
	}

	/**
	 * @param      $requestUrl
	 * @param null $error
	 * @param null $startTime
	 * @param null $timeLimit
	 *
	 * @return bool|\XF\Http\Metadata
	 * @throws \Exception
	 */
	public function fetch($requestUrl, &$error = null, $startTime = null, $timeLimit = null)
	{
		$requestUrl = $this->getValidRequestUrl($requestUrl, $startTime, $timeLimit);
		if (!$requestUrl)
		{
			$error = 'Could not get a valid request URL from: ' . htmlspecialchars($requestUrl);
			return false;
		}

		$response = $this->reader->getUntrusted(
			$requestUrl,
			$this->limits,
			null,
			[],
			$error
		);
		if (!$response)
		{
			return false;
		}

		if ($response->getStatusCode() != 200)
		{
			$error = 'Response returned a non-successful status code: ' . $response->getStatusCode();
			return false;
		}

		$headers = array_change_key_case($response->getHeaders(), CASE_LOWER);
		if (
			empty($headers['content-type'][0])
			|| !preg_match('#^text/html#i', ltrim($headers['content-type'][0]))
		)
		{
			$receivedType = empty($headers['content-type'][0]) ? '-' : $headers['content-type'][0];

			$error = 'Response is not HTML (received ' . $receivedType . ')';
			return false;
		}

		$responseBody = $response->getBody();
		$body = '';
		$bodyLen = 0;
		$hasHeadOpen = false;

		do
		{
			$additional = $responseBody->read(1 * 1024);
			$readLen = strlen($additional);

			$searchStart = max(0, $bodyLen - 10);

			$body .= $additional;
			$bodyLen += $readLen;

			if (!$hasHeadOpen)
			{
				if (preg_match('#<head(\s|>)#i', $body, $match, 0, $searchStart))
				{
					$hasHeadOpen = true;
				}
			}
			if ($hasHeadOpen)
			{
				if (preg_match('#</head>#i', $body, $match, PREG_OFFSET_CAPTURE, $searchStart))
				{
					// We found the end of the head tag, which is where the metadata should be found.
					// Remove scripts and CSS quickly so we don't waste time with them.
					$body = ltrim(substr($body, 0, $match[0][1] + 7));
					$body = preg_replace('#<script[^>]*(?<!/)>.*</script>\\s*?#siU', '', $body);
					$body = preg_replace('#<style[^>]*>.*</style>\\s*?#siU', '', $body);
					break;
				}
			}

			if (!$hasHeadOpen && $bodyLen > 102400)
			{
				// we don't have head tags, so we're just going to read the first 100KB
				break;
			}
		}
		while (!$responseBody->eof());

		$class = 'XF\Http\Metadata';
		$class = $this->app->extendClass($class);

		return new $class($this->app, $body, $headers, $requestUrl);
	}

	public function getValidRequestUrl($requestUrl, $startTime = null, $timeLimit = null)
	{
		$requestUrl = preg_replace('/#.*$/', '', $requestUrl);
		if (preg_match_all('/[^A-Za-z0-9._~:\/?#\[\]@!$&\'()*+,;=%-]/', $requestUrl, $matches))
		{
			foreach ($matches[0] AS $match)
			{
				$requestUrl = str_replace($match[0], '%' . strtoupper(dechex(ord($match[0]))), $requestUrl);
			}
		}

		if ($this->canFetchUrlHtml($requestUrl, $startTime, $timeLimit))
		{
			return $requestUrl;
		}

		return false;
	}

	protected function canFetchUrlHtml($requestUrl, $startTime = null, $timeLimit = null)
	{
		if ($requestUrl != $this->app->stringFormatter()->censorText($requestUrl))
		{
			return false;
		}

		if ($startTime && $timeLimit)
		{
			if (microtime(true) - $startTime > $timeLimit)
			{
				return false;
			}
		}

		return true;
	}
}