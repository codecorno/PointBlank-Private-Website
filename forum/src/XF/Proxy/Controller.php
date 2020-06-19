<?php

namespace XF\Proxy;

use XF\Http\Request;

class Controller
{
	/**
	 * @var \XF\App
	 */
	protected $app;

	/**
	 * @var Linker
	 */
	protected $linker;

	/**
	 * @var Request
	 */
	protected $request;

	protected $requestUri;
	protected $referrer;
	protected $eTag;

	/**
	 * If true, image requests will return an error message (and HTTP code) rather than a placeholder image.
	 *
	 * @var bool
	 */
	protected $returnError = false;

	const ERROR_INVALID_URL = 1;
	const ERROR_INVALID_HASH = 2;
	const ERROR_INVALID_REFERRER = 3;
	const ERROR_DISABLED = 4;
	const ERROR_FAILED = 5;

	public function __construct(\XF\App $app, Linker $linker, Request $request = null)
	{
		$this->app = $app;
		$this->linker = $linker;

		if (!$request)
		{
			$request = $app->request();
		}
		$this->request = $request;

		$this->requestUri = $request->getFullRequestUri();
		$this->referrer = $request->getServer('HTTP_REFERER');
		$this->eTag = $request->getServer('HTTP_IF_NONE_MATCH');
	}

	public function setReferrer($referrer)
	{
		$this->referrer = $referrer;
	}

	public function setReturnError($returnError)
	{
		$this->returnError = (bool)$returnError;
	}

	public function resolveImageProxyRecursion(Request $request, $url)
	{
		$uriParts = explode('?', $request->getFullRequestUri(), 2);
		$subMatchTest = $uriParts[0] . '?';

		$subUrl = $url;
		$subHash = null;

		// Recursion can happen, mostly if people copy proxied image URLs.
		// Try to resolve that here.
		do
		{
			$hasSubMatch = false;

			if (strpos($subUrl, $subMatchTest) === 0)
			{
				$subMatchQs = substr($subUrl, strlen($subMatchTest));
				parse_str($subMatchQs, $subMatchParams);
				if (isset($subMatchParams['image'])
					&& is_scalar($subMatchParams['image'])
					&& isset($subMatchParams['hash'])
					&& is_scalar($subMatchParams['hash'])
				)
				{
					$subMatchUrl = trim(strval($subMatchParams['image']));
					$subMatchHash = trim(strval($subMatchParams['hash']));
					if ($this->linker->verifyHash($subMatchUrl, $subMatchHash))
					{
						$subUrl = $subMatchUrl;
						$subHash = $subMatchHash;
						$hasSubMatch = true;
					}
				}
			}
		}
		while ($hasSubMatch);

		if ($subHash)
		{
			return [$subUrl, $subHash];
		}
		else
		{
			return null;
		}
	}

	public function outputImage($url, $hash)
	{
		if ($this->validateImageRequest($url, $hash, $error))
		{
			/** @var \XF\Service\ImageProxy $imageProxy */
			$imageProxy = $this->app->service('XF:ImageProxy');
			$image = $imageProxy->getImage($url);
			if (!$image || !$image->isValid())
			{
				$image = null;
			}
		}
		else
		{
			$image = null;
		}

		if (!$image)
		{
			if (!$error)
			{
				$error = self::ERROR_FAILED;
			}

			/** @var \XF\Repository\ImageProxy $proxyRepo */
			$proxyRepo = $this->app->repository('XF:ImageProxy');
			$image = $proxyRepo->getPlaceholderImage();
		}

		if ($error && $this->returnError)
		{
			return $this->outputImageErrorResponse($url, $error);
		}

		if (!$error)
		{
			/** @var \XF\Repository\ImageProxy $proxyRepo */
			$proxyRepo = $this->app->repository('XF:ImageProxy');

			$proxyRepo->logImageView($image);
			if ($this->referrer && $this->app->options()->imageLinkProxyReferrer['enabled'])
			{
				$proxyRepo->logImageReferrer($image, $this->referrer);
			}
		}

		$response = $this->app->response();
		$this->applyImageResponseHeaders($response, $image, $error);

		if ($image->isPlaceholder())
		{
			$body = $response->responseFile($image->getPlaceholderPath());
		}
		else
		{
			$stream = $this->app->fs()->readStream($image->getAbstractedImagePath());
			$body = $response->responseStream($stream, $image->file_size);
		}

		$response->body($body);

		return $response;
	}

	public function applyImageResponseHeaders(\XF\Http\Response $response, \XF\Entity\ImageProxy $image, $error)
	{
		if ($error)
		{
			$response->header('Cache-Control', 'no-cache');
		}
		else
		{
			$nextRefresh = $image->getNextPlannedRefreshDate();
			if ($nextRefresh)
			{
				$maxAge = max(0, $nextRefresh - \XF::$time);
			}
			else
			{
				$maxAge = 86400; // allow daily revalidation if we don't know otherwise
			}

			$response->header('Cache-Control', 'public, max-age=' . $maxAge);
			$response->header('Last-Modified', gmdate('D, d M Y H:i:s', $image->fetch_date) . ' GMT');

			$expectedETag = $image->getETagValue();
			if ($expectedETag)
			{
				$response->header('ETag', '"' . $expectedETag . '"', true);

				if ($this->eTag && $this->eTag === "\"$expectedETag\"")
				{
					$response->httpCode(304);
					$response->removeHeader('Last-Modified');
					return;
				}
			}
		}

		$imageTypes = [
			'image/gif',
			'image/jpeg',
			'image/pjpeg',
			'image/png'
		];

		if (in_array($image->mime_type, $imageTypes))
		{
			$response->contentType($image->mime_type);
			$response->setDownloadFileName($image->file_name, true);
		}
		else
		{
			$response->contentType('application/octet-stream');
			$response->setDownloadFileName($image->file_name);
		}

		$response->header('X-Content-Type-Options', 'nosniff');

		if ($error)
		{
			$response->header('X-Proxy-Error', $error);
		}
	}

	protected function outputImageErrorResponse($url, $error)
	{
		$response = $this->app->response();
		$response->httpCode(404);
		$response->contentType('text/plain');
		$response->header('Cache-Control', 'no-cache');
		$response->header('X-Proxy-Error', $error);
		$response->body('error');

		return $response;
	}

	public function validateImageRequest($url, $hash, &$error = null)
	{
		if (!$this->linker->isTypeEnabled('image'))
		{
			$error = self::ERROR_DISABLED;
			return false;
		}

		if (!$this->validateProxyRequestGeneric($url, $hash, $error))
		{
			return false;
		}

		return true;
	}

	public function outputLink($url, $hash, $json = false)
	{
		$response = $this->app->response();
		$response->contentType('application/json', 'utf-8');

		if ($this->validateLinkRequest($url, $hash, $error))
		{
			/** @var \XF\Repository\LinkProxy $proxyRepo */
			$proxyRepo = $this->app->repository('XF:LinkProxy');

			$link = $proxyRepo->logLinkVisit($url);
			if ($link && $this->referrer && $this->app->options()->imageLinkProxyReferrer['enabled'])
			{
				$proxyRepo->logLinkReferrer($link, $this->referrer);
			}

			$response->body(json_encode(['logged' => true]));
		}
		else
		{
			$response->httpCode(400);

			if (\XF::$debugMode)
			{
				$response->body(json_encode(['invalid' => true, 'code' => $error]));
			}
			else
			{
				$response->body(json_encode(['invalid' => true]));
			}
		}

		return $response;
	}

	public function validateLinkRequest($url, $hash, &$error = null)
	{
		if (!$this->linker->isTypeEnabled('link'))
		{
			$error = self::ERROR_DISABLED;
			return false;
		}

		if (!$this->validateProxyRequestGeneric($url, $hash, $error))
		{
			return false;
		}

		return true;
	}

	protected function validateProxyRequestGeneric($url, $hash, &$error = null)
	{
		$error = null;

		if (!preg_match('#^https?://#i', $url))
		{
			$error = self::ERROR_INVALID_URL;
			return false;
		}

		$urlParts = @parse_url($url);
		if (!$urlParts || empty($urlParts['host']))
		{
			$error = self::ERROR_INVALID_URL;
			return false;
		}

		if (!$this->linker->verifyHash($url, $hash))
		{
			$error = self::ERROR_INVALID_HASH;
			return false;
		}

		if (!$this->isValidReferrer())
		{
			$error = self::ERROR_INVALID_REFERRER;
			return false;
		}

		return true;
	}

	protected function isValidReferrer()
	{
		if (!$this->referrer)
		{
			return true;
		}

		$referrerParts = @parse_url($this->referrer);
		if (!$referrerParts || empty($referrerParts['host']))
		{
			return true;
		}

		$requestParts = @parse_url($this->requestUri);
		if (!$requestParts || empty($requestParts['host']))
		{
			return true;
		}

		return ($requestParts['host'] === $referrerParts['host']);
	}
}