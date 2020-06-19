<?php

namespace XF\Proxy;

class Linker
{
	protected $types = [];
	protected $secret = '';
	protected $pather;

	// example: proxy.php?{type}={url}&hash={hash}
	protected $linkFormat = '';

	public function __construct($linkFormat, array $types, $secret, callable $pather)
	{
		$this->linkFormat = $linkFormat;
		$this->types = $types;
		$this->secret = $secret;
		$this->pather = $pather;
	}

	public function generate($type, $url)
	{
		if (empty($this->types[$type]))
		{
			return null;
		}

		$link = strtr($this->linkFormat, [
			'{type}' => urlencode($type),
			'{url}' => urlencode($url),
			'{hash}' => urlencode($this->hash($url))
		]);

		if ($this->pather)
		{
			$pather = $this->pather;
			$link = $pather($link, 'base');
		}

		return $link;
	}

	public function generateExtended($type, $url, array $options = [])
	{
		$url = $this->generate($type, $url);

		if ($url === null)
		{
			return null;
		}

		if ($options)
		{
			$queryString = http_build_query($options);
			$url .= (strpos($url, '?') !== false ? '&' : '?') . $queryString;
		}

		return $url;
	}

	public function isTypeEnabled($type)
	{
		return !empty($this->types[$type]);
	}

	public function verifyHash($url, $hash)
	{
		return $this->hash($url) === $hash;
	}

	public function hash($url)
	{
		return hash_hmac('md5', $url, $this->secret);
	}
}