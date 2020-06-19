<?php

namespace XF\Sitemap;

class Entry
{
	public $loc;
	public $lastmod;
	public $changefreq;
	public $priority;
	public $image;

	public function __construct($loc, $lastmod, $changefreq, $priority, $image)
	{
		$this->loc = strval($loc);
		$this->lastmod = intval($lastmod);
		$this->changefreq = $changefreq;
		$this->priority = $priority;
		$this->image = $image;
	}

	public function set($name, $value)
	{
		$this->{$name} = $value;
	}

	public static function create($loc, array $data = [])
	{
		$data = array_merge([
			'lastmod' => 0,
			'changefreq' => '',
			'priority' => '',
			'image' => ''
		], $data);

		return new self($loc, $data['lastmod'], $data['changefreq'], $data['priority'], $data['image']);
	}
}