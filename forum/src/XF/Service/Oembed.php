<?php

namespace XF\Service;

use XF\Db\Exception;

class Oembed extends AbstractService
{
	protected $forceRefresh = false;
	protected $maxConcurrent = 10;

	/**
	 * @var \XF\Repository\Oembed
	 */
	protected $oEmbedRepo;

	protected function setup()
	{
		$this->oEmbedRepo = $this->repository('XF:Oembed');
	}

	public function forceRefresh($value = true)
	{
		$this->forceRefresh = (bool)$value;
	}

	public function isRefreshForced()
	{
		return $this->forceRefresh;
	}

	public function getOembed($mediaSiteId, $mediaId)
	{
		$oEmbed = $this->oEmbedRepo->getOembed($mediaSiteId, $mediaId);
		if ($oEmbed)
		{
			if ($this->isRefreshRequired($oEmbed))
			{
				$this->refetchOembed($oEmbed);
			}
		}
		else
		{
			if ($this->canFetchOembed())
			{
				$oEmbed = $this->fetchNewOembed($mediaSiteId, $mediaId);
			}
		}

		return $oEmbed;
	}

	protected function isRefreshRequired(\XF\Entity\Oembed $oEmbed)
	{
		if ($this->forceRefresh)
		{
			return true;
		}

		return $oEmbed->isRefreshRequired() && $this->canFetchOembed();
	}

	protected function canFetchOembed()
	{
		if ($this->forceRefresh)
		{
			return true;
		}

		$active = $this->oEmbedRepo->getTotalActiveFetches();
		return ($active < $this->maxConcurrent);
	}

	public function fetchNewOembed($mediaSiteId, $mediaId)
	{
		/** @var \XF\Entity\Oembed */
		$oEmbed = $this->em()->create('XF:Oembed');
		$oEmbed->media_site_id = $mediaSiteId;
		$oEmbed->media_id = $mediaId;
		$oEmbed->is_processing = time();

		try
		{
			$oEmbed->save();
		}
		catch (\XF\Db\Exception $e)
		{
			// usually duplicate key
			return null;
		}

		$fetchResults = $this->fetchJsonData($mediaSiteId, $mediaId);
		$this->finalizeFromFetchResults($oEmbed, $fetchResults);

		return $oEmbed;
	}

	public function refetchOembed(\XF\Entity\Oembed $oEmbed)
	{
		$oEmbed->is_processing = time();
		$oEmbed->save();

		$fetchResults = $this->fetchJsonData($oEmbed->BbCodeMediaSite, $oEmbed->media_id);
		$this->finalizeFromFetchResults($oEmbed, $fetchResults);

		return $oEmbed;
	}

	public function testOembedFetch($mediaSiteId, $mediaId)
	{
		$results = $this->fetchJsonData($mediaSiteId, $mediaId);
		if ($results['dataFile'])
		{
			@unlink($results['dataFile']);
			$results['dataFile'] = null;
		}

		return $results;
	}

	/**
	 * @param \XF\Entity\BbCodeMediaSite|string $mediaSiteId Use the BbCodeMediaSite entity to avoid a query,
	 *                                                       otherwise use media_site_id
	 * @param string $mediaId
	 *
	 * @return array
	 */
	protected function fetchJsonData($mediaSiteId, $mediaId)
	{
		$url = $this->oEmbedRepo->getOembedUrl($mediaSiteId, $mediaId);

		return $this->fetchJsonDataFromUrl($url);
	}

	protected function fetchJsonDataFromUrl($url)
	{
		if (!preg_match('#^https?://#i', $url))
		{
			throw new \InvalidArgumentException("URL must be http or https");
		}

		$validOembed = false;
		$title = null;
		$error = null;
		$streamFile = \XF\Util\File::getTempDir() . '/' . strtr(md5($url) . '-' . uniqid(), '/\\.', '---') . '.temp';

		try
		{
			$options = [];
			$limits = [
				'time' => 5,
				'bytes' => 1.5 * 1024 * 1024
			];

			$response = $this->app->http()->reader()->getUntrusted($url, $limits, $streamFile, $options, $error);
		}
		catch (\Exception $e)
		{
			$response = null;
			$error = $e->getMessage();
		}

		if ($response)
		{
			$jsonText = $response->getBody()->getContents();

			$response->getBody()->close();

			if ($response->getStatusCode() == 200)
			{
				try
				{
					$json = json_decode($jsonText, true);

					if (!empty($json['title']))
					{
						$title = $json['title'];
					}
					else if (!empty($json['author_name']))
					{
						$title = $json['author_name'];
					}

					$validOembed = true;
				}
				catch (\Exception $e)
				{
					$error = \XF::phraseDeferred('returned_data_is_not_json');
				}
			}
			else
			{
				$error = \XF::phraseDeferred('received_unexpected_response_code_x_message_y', [
					'code' => $response->getStatusCode(),
					'message' => $response->getReasonPhrase()
				]);
			}
		}

		if (!$validOembed)
		{
			@unlink($streamFile);
		}

		return [
			'valid' => $validOembed,
			'title' => \XF::cleanString($title),
			'error' => $error,
			'dataFile' => $validOembed ? $streamFile : null
		];
	}

	protected function finalizeFromFetchResults(\XF\Entity\Oembed $oEmbed, array $fetchResults, &$error = null)
	{
		$oEmbed->is_processing = 0;

		if ($fetchResults['valid'])
		{
			$newJsonPath = $oEmbed->getAbstractedJsonPath();

			if (\XF\Util\File::copyFileToAbstractedPath($fetchResults['dataFile'], $newJsonPath))
			{
				$oEmbed->fetch_date = time();
				$oEmbed->pruned = false;
				$oEmbed->failed_date = 0;
				$oEmbed->fail_count = 0;

				if (!empty($fetchResults['title']))
				{
					$oEmbed->title = $fetchResults['title'];
				}
			}
			else
			{
				$oEmbed->pruned = true;
			}

			@unlink($fetchResults['dataFile']);
		}
		else
		{
			$oEmbed->failed_date = time();
			$oEmbed->fail_count++;

			$error = $fetchResults['error'];
		}

		$oEmbed->save();
	}
}