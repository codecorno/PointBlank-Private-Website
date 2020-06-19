<?php

namespace XF\Service\Unfurl;

use XF\Entity\UnfurlResult;
use XF\Service\AbstractService;

class Fetcher extends AbstractService
{
	protected $result;

	public function __construct(\XF\App $app, UnfurlResult $result)
	{
		parent::__construct($app);
		$this->result = $result;
	}

	public function fetch(&$error = null)
	{
		$result = $this->result;

		$metadata = $this->app->http()->metadataFetcher()->fetch($result->url, $error);

		if (!$metadata)
		{
			$this->logError();
			return false;
		}

		$title = $metadata->getTitle();

		if (!$title)
		{
			// if no title, might as well bail out
			$error = 'Could not fetch title metadata from URL.';
			$this->logError();
			return false;
		}

		$unfurlData = [
			'title' => $title,
			'description' => $metadata->getDescription(),
			'image_url' => $metadata->getImage(),
			'favicon_url' => $metadata->getFavicon(),
			'last_request_date' => \XF::$time,
			'pending' => 0
		];
		$result->bulkSet(
			$this->prepareUnfurlData($unfurlData)
		);
		$result->save();

		return true;
	}

	public function render()
	{
		return $this->app->templater()->renderUnfurl($this->result);
	}

	protected function logError()
	{
		return $this->repository('XF:Unfurl')->logError($this->result);
	}

	protected function prepareUnfurlData(array $unfurlData)
	{
		return $unfurlData;
	}
}