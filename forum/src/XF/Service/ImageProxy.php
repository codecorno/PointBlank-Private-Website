<?php

namespace XF\Service;

use XF\Db\Exception;

class ImageProxy extends AbstractService
{
	protected $forceRefresh = false;
	protected $maxConcurrent = 10;

	/**
	 * @var \XF\Repository\ImageProxy
	 */
	protected $proxyRepo;

	protected function setup()
	{
		$this->proxyRepo = $this->repository('XF:ImageProxy');
	}

	public function forceRefresh($value = true)
	{
		$this->forceRefresh = (bool)$value;
	}

	public function isRefreshForced()
	{
		return $this->forceRefresh;
	}

	public function getImage($url)
	{
		$image = $this->proxyRepo->getImageByUrl($url);
		if ($image)
		{
			if ($this->isRefreshRequired($image))
			{
				$this->refetchImage($image);
			}
		}
		else
		{
			// no image, if we can fetch an image, create it
			if ($this->canFetchImage())
			{
				$image = $this->fetchNewImage($url);
			}
		}

		return $image;
	}

	protected function isRefreshRequired(\XF\Entity\ImageProxy $image)
	{
		if ($this->forceRefresh)
		{
			return true;
		}

		return $image->isRefreshRequired() && $this->canFetchImage();
	}

	public function canFetchImage()
	{
		if ($this->forceRefresh)
		{
			return true;
		}

		$active = $this->proxyRepo->getTotalActiveFetches();
		return ($active < $this->maxConcurrent);
	}

	public function fetchNewImage($url)
	{
		/** @var \XF\Entity\ImageProxy $image */
		$image = $this->em()->create('XF:ImageProxy');
		$image->url = $url;
		$image->pruned = true;
		$image->is_processing = time(); // may have slept, need to set to now

		try
		{
			$image->save();
		}
		catch (\XF\Db\Exception $e)
		{
			// this is mostly a duplicate key issue
			return null;
		}

		$fetchResults = $this->fetchImageDataFromUrl($image->url);
		$this->finalizeFromFetchResults($image, $fetchResults);

		return $image;
	}

	public function refetchImage(\XF\Entity\ImageProxy $image)
	{
		$image->is_processing = time();
		$image->save();

		$fetchResults = $this->fetchImageDataFromUrl($image->url);
		$this->finalizeFromFetchResults($image, $fetchResults);

		return $image;
	}

	public function testImageFetch($url)
	{
		$results = $this->fetchImageDataFromUrl($url);
		if ($results['dataFile'])
		{
			@unlink($results['dataFile']);
			$results['dataFile'] = null;
		}

		return $results;
	}

	protected function fetchImageDataFromUrl($url)
	{
		$url = $this->proxyRepo->cleanUrlForFetch($url);
		if (!preg_match('#^https?://#i', $url))
		{
			throw new \InvalidArgumentException("URL must be http or https");
		}

		$urlParts = @parse_url($url);

		$validImage = false;
		$fileName = !empty($urlParts['path']) ? basename($urlParts['path']) : null;
		$mimeType = null;
		$error = null;
		$streamFile = \XF\Util\File::getTempDir() . '/' . strtr(md5($url) . '-' . uniqid(), '/\\.', '---') . '.temp';
		$imageProxyMaxSize = $this->app->options()->imageProxyMaxSize * 1024;

		try
		{
			$options = [
				'headers' => [
					'Accept' => 'image/*,*/*;q=0.8'
				]
			];
			$limits = [
				'time' => 8,
				'bytes' => $imageProxyMaxSize ?: -1
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
			$response->getBody()->close();

			if ($response->getStatusCode() == 200)
			{
				$disposition = $response->getHeader('Content-Disposition');
				if (!empty($disposition) && preg_match('/filename=(\'|"|)(.+)\\1/siU', $disposition[0], $match))
				{
					$fileName = $match[2];
				}
				if (!$fileName)
				{
					$fileName = 'image';
				}

				$imageInfo = filesize($streamFile) ? @getimagesize($streamFile) : false;
				if ($imageInfo)
				{
					$imageType = $imageInfo[2];

					$extension = \XF\Util\File::getFileExtension($fileName);
					$extensionMap = [
						IMAGETYPE_GIF => ['gif'],
						IMAGETYPE_JPEG => ['jpg', 'jpeg', 'jpe'],
						IMAGETYPE_PNG => ['png'],
						IMAGETYPE_ICO => ['ico']
					];
					if (isset($extensionMap[$imageType]))
					{
						$mimeType = $imageInfo['mime'];

						$validExtensions = $extensionMap[$imageType];
						if (!in_array($extension, $validExtensions))
						{
							$extensionStart = strrpos($fileName, '.');
							$fileName = (
								$extensionStart
									? substr($fileName, 0, $extensionStart)
									: $fileName
								) . '.' . $validExtensions[0];
						}

						$validImage = true;
					}
					else
					{
						$error = \XF::phraseDeferred('image_is_invalid_type');
					}
				}
				else
				{
					$error = \XF::phraseDeferred('file_not_an_image');
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

		if (!$validImage)
		{
			@unlink($streamFile);
		}

		return [
			'valid' => $validImage,
			'error' => $error,
			'dataFile' => $validImage ? $streamFile : null,
			'fileName' => $fileName,
			'mimeType' => $mimeType
		];
	}

	protected function finalizeFromFetchResults(\XF\Entity\ImageProxy $image, array $fetchResults)
	{
		$image->is_processing = 0;

		if ($fetchResults['valid'])
		{
			$newImagePath = $image->getAbstractedImagePath();

			if (\XF\Util\File::copyFileToAbstractedPath($fetchResults['dataFile'], $newImagePath))
			{
				$image->fetch_date = time();
				$image->file_name = $fetchResults['fileName'];
				$image->file_size = filesize($fetchResults['dataFile']);
				$image->mime_type = $fetchResults['mimeType'];
				$image->pruned = false;
				$image->failed_date = 0;
				$image->fail_count = 0;
			}
			else
			{
				$image->pruned = true;
			}

			@unlink($fetchResults['dataFile']);
		}
		else
		{
			$image->failed_date = time();
			$image->fail_count++;
		}

		$image->save();
	}
}